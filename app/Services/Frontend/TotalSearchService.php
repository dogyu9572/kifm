<?php

namespace App\Services\Frontend;

use App\Models\AcademicEvent;
use App\Models\EduCourse;
use App\Models\EduTraining;
use App\Models\EduTrainingRound;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TotalSearchService
{
    public function __construct(
        private readonly PublicAcademicEventService $academicEventService,
        private readonly PublicTrainingCourseService $trainingCourseService,
        private readonly PublicOnlineAcademyService $onlineAcademyService,
    ) {}

    /**
     * @return array<int, array{label: string, total: int, list_url: string, items: Collection<int, array{title: string, url: string, status_label: string, status_class: string, primary_label: string, primary_text: string, secondary_label: string, secondary_text: string}>}>
     */
    public function contentGroups(string $keyword, int $limit = 2): array
    {
        return [
            $this->academicEventGroup($keyword, $limit),
            $this->trainingGroup($keyword, $limit),
            $this->onlineAcademyGroup($keyword, $limit),
        ];
    }

    /**
     * @return array<int, array{label: string, slug: string, list_url: string, total: int, items: Collection<int, array{title: string, summary: string, url: string}>}>
     */
    public function boardGroups(string $keyword, int $limit = 2): array
    {
        return collect($this->boardDefinitions())
            ->map(function (array $board) use ($keyword, $limit): array {
                $board['list_url'] = $this->listUrl($board['list_url'], $keyword);
                $table = 'board_'.$board['slug'];
                if (! Schema::hasTable($table)) {
                    return array_merge($board, [
                        'total' => 0,
                        'items' => collect(),
                    ]);
                }

                $base = DB::table($table)
                    ->whereNull('deleted_at')
                    ->where('is_active', true)
                    ->where('is_secret', false);

                if ($keyword !== '') {
                    $base->where(function ($query) use ($keyword) {
                        $like = '%'.$keyword.'%';
                        $query->where('title', 'like', $like)
                            ->orWhere('content', 'like', $like);
                    });
                }

                $total = (clone $base)->count();
                $items = (clone $base)
                    ->orderBy('is_notice', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get(['id', 'title', 'content'])
                    ->map(fn ($post): array => [
                        'title' => (string) $post->title,
                        'summary' => $this->summary((string) ($post->content ?? '')),
                        'url' => route($board['show_route'], $post->id),
                    ]);

                return array_merge($board, [
                    'total' => $total,
                    'items' => $items,
                ]);
            })
            ->all();
    }

    public function total(array $groups): int
    {
        return array_sum(array_map(static fn (array $group): int => (int) $group['total'], $groups));
    }

    public function boardTotal(array $boardGroups): int
    {
        return $this->total($boardGroups);
    }

    private function academicEventGroup(string $keyword, int $limit): array
    {
        $query = AcademicEvent::query()->where('is_public', 'Y');
        if ($keyword !== '') {
            $like = '%'.$keyword.'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('event_material_description', 'like', $like)
                    ->orWhere('greeting_title', 'like', $like)
                    ->orWhere('venue', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $items = (clone $query)
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function (AcademicEvent $event): array {
                $status = $this->academicEventService->status($event);

                return [
                    'title' => $event->title,
                    'url' => $this->academicEventService->eventUrl($event),
                    'status_label' => $status['label'],
                    'status_class' => $status['class'],
                    'primary_label' => '일시:',
                    'primary_text' => $this->academicEventService->eventDateText($event),
                    'secondary_label' => '장소:',
                    'secondary_text' => $event->venue ?: '-',
                ];
            });

        return [
            'label' => '학술대회',
            'anchor_id' => 'total-search-academic-event',
            'total' => $total,
            'list_url' => $this->listUrl(route('academic_event.conference'), $keyword),
            'items' => $items,
        ];
    }

    private function trainingGroup(string $keyword, int $limit): array
    {
        $query = EduTraining::query()
            ->with('rounds')
            ->where('status', 'PUBLIC');
        if ($keyword !== '') {
            $like = '%'.$keyword.'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('overview', 'like', $like)
                    ->orWhere('program', 'like', $like)
                    ->orWhere('registration_info', 'like', $like)
                    ->orWhere('introduction', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $items = (clone $query)
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function (EduTraining $training): array {
                $status = $this->trainingCourseService->status($training);
                $round = $this->trainingCourseService->publicRounds($training)->first();

                return [
                    'title' => $training->title,
                    'url' => $this->trainingCourseService->detailUrl($training),
                    'status_label' => $status['label'],
                    'status_class' => $status['class'],
                    'primary_label' => '일시:',
                    'primary_text' => $this->trainingCourseService->roundDateText($round),
                    'secondary_label' => '장소:',
                    'secondary_text' => $round instanceof EduTrainingRound ? ($round->location_link ?: '-') : '-',
                ];
            });

        return [
            'label' => '연수교육',
            'anchor_id' => 'total-search-training',
            'total' => $total,
            'list_url' => $this->listUrl(route('academic_event.training_course'), $keyword),
            'items' => $items,
        ];
    }

    private function onlineAcademyGroup(string $keyword, int $limit): array
    {
        $query = EduCourse::query()
            ->with('professorMember')
            ->where('use_yn', 'Y')
            ->where('expose_yn', 'Y');
        if ($keyword !== '') {
            $like = '%'.$keyword.'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('topic_detail', 'like', $like)
                    ->orWhere('topics', 'like', $like)
                    ->orWhere('keywords', 'like', $like)
                    ->orWhere('professor_name', 'like', $like)
                    ->orWhere('professor_org', 'like', $like)
                    ->orWhereHas('professorMember', fn (Builder $memberQ) => $memberQ->where('name', 'like', $like));
            });
        }

        $courseTypeLabels = $this->onlineAcademyService->courseTypeLabels();
        $total = (clone $query)->count();
        $items = (clone $query)
            ->orderByDesc('open_year')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (EduCourse $course): array => [
                'title' => $course->title,
                'url' => route('online_academy.show', $course),
                'status_label' => $courseTypeLabels[$course->course_type] ?? '수강 가능',
                'status_class' => 'ing',
                'primary_label' => '수강기간:',
                'primary_text' => $this->onlineAcademyService->periodText($course),
                'secondary_label' => '강사:',
                'secondary_text' => $this->onlineAcademyService->professorText($course) ?: '-',
            ]);

        return [
            'label' => '온라인 아카데미',
            'anchor_id' => 'total-search-online-academy',
            'total' => $total,
            'list_url' => route('online_academy.index', [
                'search_field' => 'all',
                'search_keyword' => $keyword,
            ]),
            'items' => $items,
        ];
    }

    /**
     * @return array<int, array{label: string, slug: string, list_url: string, show_route: string}>
     */
    private function boardDefinitions(): array
    {
        return [
            [
                'label' => '학회공지',
                'anchor_id' => 'total-search-society-notices',
                'slug' => 'member_square_notices',
                'list_url' => route('member_plaza.society_notices'),
                'show_route' => 'member_plaza.society_notices_show',
            ],
            [
                'label' => '기타공지',
                'anchor_id' => 'total-search-other-notices',
                'slug' => 'other_notices',
                'list_url' => route('member_plaza.other_notices'),
                'show_route' => 'member_plaza.other_notices_show',
            ],
            [
                'label' => '일반자료실',
                'anchor_id' => 'total-search-general-archive',
                'slug' => 'general_archive',
                'list_url' => route('archives.general'),
                'show_route' => 'archives.general_show',
            ],
            [
                'label' => '학술자료실',
                'anchor_id' => 'total-search-academic-archive',
                'slug' => 'academic_archive',
                'list_url' => route('archives.academic'),
                'show_route' => 'archives.academic_show',
            ],
            [
                'label' => '회원자료실',
                'anchor_id' => 'total-search-member-archive',
                'slug' => 'member_archive',
                'list_url' => route('archives.members'),
                'show_route' => 'archives.members_show',
            ],
        ];
    }

    private function summary(string $content): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');

        return $text !== '' ? Str::limit($text, 120) : '';
    }

    private function listUrl(string $url, string $keyword): string
    {
        if ($keyword === '') {
            return $url;
        }

        return $url.'?'.http_build_query([
            'search_type' => 'all',
            'keyword' => $keyword,
        ]);
    }
}

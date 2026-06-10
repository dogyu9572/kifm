<?php

namespace App\Services\Frontend;

use App\Models\AcademicEvent;
use App\Models\AcademicEventSession;
use App\Models\AcademicEventSessionItem;
use App\Models\AcademicEventSpeaker;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class PublicAcademicConferenceService
{
    public const FALLBACK_MAIN_IMAGE = 'images/academic_mvisual.jpg';

    public function findPublicEventByFolder(string $folderName): AcademicEvent
    {
        $relations = [
            'venueFloors',
            'fields',
            'sponsors.master',
            'mainSponsorSlots.sponsor.master',
            'speakers.member',
            'speakers.abstractSubmission',
        ];
        $relations[] = Schema::hasTable('academic_event_session_items')
            ? 'sessions.items.abstractSubmission'
            : 'sessions';

        return AcademicEvent::query()
            ->with($relations)
            ->where('folder_name', $folderName)
            ->where('is_public', 'Y')
            ->firstOrFail();
    }

    /** @return array{view: string, page_type: string, gNum: string, sNum?: string, gName: string, sName: string, gSlug: string} */
    public function pageData(AcademicEvent $event, ?string $path): array
    {
        $path = trim((string) $path, '/');
        $pages = $this->pageDefinitions($event);
        abort_unless(array_key_exists($path, $pages), 404);

        $page = $pages[$path];
        $eventView = 'academic_conference.' . $event->folder_name . '.' . $page['view'];
        $defaultView = 'academic_conference.default.' . $page['view'];
        $view = View::exists($eventView) ? $eventView : $defaultView;

        abort_unless(View::exists($view), 404);

        return array_merge($page, ['view' => $view]);
    }

    public function baseUrl(AcademicEvent $event): string
    {
        return url('/academic_conference/' . $event->folder_name);
    }

    public function url(AcademicEvent $event, string $path = ''): string
    {
        $base = $this->baseUrl($event);
        $path = trim($path, '/');

        return $path === '' ? $base : $base . '/' . $path;
    }

    public function imageUrl(?string $path, string $fallback = self::FALLBACK_MAIN_IMAGE): string
    {
        if ($path === null || $path === '') {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function optionalImageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function eventTitle(AcademicEvent $event): string
    {
        return $event->title;
    }

    public function mainTitle1(AcademicEvent $event): string
    {
        $text = trim((string) $event->main_title_1);

        return $text !== '' ? $text : 'KIFM ' . $event->year . ' The ' . $event->year . ' Korean Institute for Functional Medicine';
    }

    public function mainTitle2(AcademicEvent $event): string
    {
        $text = trim((string) $event->main_title_2);

        if ($text !== '') {
            return $text;
        }

        return $this->themeText($event);
    }

    public function themeText(AcademicEvent $event): string
    {
        $text = trim(strip_tags((string) ($event->event_material_description ?: $event->greeting_title ?: '')));

        return $text !== '' ? Str::limit($text, 80) : '';
    }

    public function contentHtml(?string $content): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        return $content;
    }

    public function fullAddress(AcademicEvent $event): string
    {
        return trim(implode(' ', array_filter([
            $event->address,
            $event->address_detail,
        ])));
    }

    public function floorFileUrl(?string $path): ?string
    {
        return $this->optionalImageUrl($path);
    }

    public function sessionDateText(AcademicEventSession $session): string
    {
        return $session->session_date ? $session->session_date->format('Y.m.d') . ' (' . $this->weekday($session->session_date) . ')' : '';
    }

    public function sessionTimeRange(AcademicEventSession $session): string
    {
        $start = $this->timeText($session->start_time);
        $end = $this->timeText($session->end_time);

        return trim(implode(' ~ ', array_filter([$start, $end])));
    }

    public function sessionCategoryLabel(?string $category): string
    {
        return [
            'oral' => '구연 발표',
            'poster' => '포스터 발표',
            'special' => '특별 강연',
            'symposium' => '심포지엄',
        ][$category] ?? trim((string) $category);
    }

    public function programSessionGroups(Collection $sessions): Collection
    {
        return $sessions->sortBy([
            ['session_date', 'asc'],
            ['start_time', 'asc'],
            ['sort_order', 'asc'],
        ])->values()->map(function (AcademicEventSession $session) {
            return [
                'session' => $session,
                'chairs' => trim((string) $session->chair_speakers),
                'items' => $session->relationLoaded('items') && $session->items->isNotEmpty()
                    ? $session->items
                    : collect([$session]),
            ];
        });
    }

    public function programItemTitle(AcademicEventSession|AcademicEventSessionItem $item): string
    {
        if ($item instanceof AcademicEventSessionItem) {
            if ($item->row_type === 'break') {
                return $item->title ?: 'Coffee Break';
            }

            return trim((string) ($item->title ?: optional($item->abstractSubmission)->title));
        }

        return $this->sessionCategoryLabel($item->category) ?: $item->name;
    }

    public function programItemPresenter(AcademicEventSession|AcademicEventSessionItem $item): string
    {
        if ($item instanceof AcademicEventSessionItem) {
            return trim((string) $item->presenter);
        }

        return $this->sessionDateText($item);
    }

    public function programItemDescription(AcademicEventSession|AcademicEventSessionItem $item): string
    {
        if ($item instanceof AcademicEventSessionItem) {
            return '';
        }

        return $this->contentHtml($item->description);
    }

    public function timeText(mixed $time): string
    {
        if ($time instanceof DateTimeInterface) {
            return $time->format('H:i');
        }

        $time = trim((string) $time);
        if ($time === '') {
            return '';
        }

        try {
            return Carbon::parse($time)->format('H:i');
        } catch (\Throwable) {
            return $time;
        }
    }

    public function timeMachine(mixed $time): string
    {
        return $this->timeText($time);
    }

    public function speakerImageUrl(AcademicEventSpeaker $speaker): string
    {
        return $this->imageUrl($speaker->image_path, 'images/img_noimage.jpg');
    }

    public function speakerTitle(AcademicEventSpeaker $speaker): string
    {
        $title = trim((string) ($speaker->abstract_title ?: optional($speaker->abstractSubmission)->title));

        return $title !== '' ? $title : '발표 주제 미정';
    }

    public function speakerAffiliation(AcademicEventSpeaker $speaker): string
    {
        return trim((string) $speaker->affiliation);
    }

    public function speakerPosition(AcademicEventSpeaker $speaker): string
    {
        return trim((string) $speaker->position);
    }

    public function speakerRoleLine(AcademicEventSpeaker $speaker): string
    {
        return trim(implode(' ', array_filter([
            $this->speakerAffiliation($speaker),
            $this->speakerPosition($speaker),
        ])));
    }

    public function mainSpeakers(AcademicEvent $event): Collection
    {
        $speakers = $event->relationLoaded('speakers')
            ? $event->speakers
            : $event->speakers()->with(['member', 'abstractSubmission'])->get();

        return $speakers
            ->sortBy([
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->values()
            ->filter(fn (AcademicEventSpeaker $speaker) => trim((string) $speaker->name) !== '')
            ->map(fn (AcademicEventSpeaker $speaker) => [
                'name' => trim((string) $speaker->name),
                'affiliation' => $this->speakerAffiliation($speaker),
                'title' => $this->speakerTitle($speaker),
                'image_url' => $this->speakerImageUrl($speaker),
            ])
            ->values();
    }

    public function mainNotices(AcademicEvent $event, int $limit = 3): Collection
    {
        return DB::table('board_academic_notices')
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false)
            ->where('event_id', $event->id)
            ->orderBy('is_notice', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function notices(AcademicEvent $event, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = DB::table('board_academic_notices')
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false)
            ->where('event_id', $event->id);

        $this->applyNoticeKeywordFilter($query, $request);

        return $query
            ->orderBy('is_notice', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function notice(AcademicEvent $event, ?int $id): ?object
    {
        $query = DB::table('board_academic_notices')
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false)
            ->where('event_id', $event->id);

        $post = $id
            ? $query->where('id', $id)->first()
            : $query->orderBy('is_notice', 'desc')->orderBy('created_at', 'desc')->first();

        if ($post === null) {
            return null;
        }

        DB::table('board_academic_notices')->where('id', $post->id)->increment('view_count');
        $post->view_count = (int) ($post->view_count ?? 0) + 1;

        return $post;
    }

    /** @return array{prev: ?object, next: ?object} */
    public function noticePrevNext(AcademicEvent $event, int $id): array
    {
        $base = function () use ($event) {
            return DB::table('board_academic_notices')
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->where('is_secret', false)
                ->where('event_id', $event->id);
        };

        $current = $base()->where('id', $id)->first();
        if ($current === null) {
            return ['prev' => null, 'next' => null];
        }

        return [
            'prev' => $base()
                ->where('created_at', '<', $current->created_at)
                ->orderBy('created_at', 'desc')
                ->first(),
            'next' => $base()
                ->where('created_at', '>', $current->created_at)
                ->orderBy('created_at', 'asc')
                ->first(),
        ];
    }

    /** @return array<int, array{name: string, url: string, size: int, size_text: string}> */
    public function noticeAttachments(?string $attachments): array
    {
        $items = json_decode((string) $attachments, true);
        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->filter(fn ($item) => is_array($item) && ! empty($item['path']))
            ->map(function (array $item) {
                $size = (int) ($item['size'] ?? 0);

                return [
                    'name' => (string) ($item['name'] ?? basename((string) $item['path'])),
                    'url' => Storage::disk('public')->url((string) $item['path']),
                    'size' => $size,
                    'size_text' => $this->fileSizeText($size),
                ];
            })
            ->values()
            ->all();
    }

    public function sponsorGroups(AcademicEvent $event): Collection
    {
        $labels = [
            'vip' => 'VIP',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'exhibitors' => 'Exhibitors',
        ];

        $sponsors = $event->relationLoaded('sponsors')
            ? $event->sponsors
            : $event->sponsors()->with('master')->get();

        return collect($labels)->map(function (string $label, string $level) use ($sponsors) {
            return [
                'level' => $level,
                'label' => $label,
                'sponsors' => $sponsors
                    ->where('level', $level)
                    ->values()
                    ->map(function ($sponsor) {
                        $logoPath = $sponsor->logo_path ?: optional($sponsor->master)->logo_path;

                        return [
                            'name' => trim((string) $sponsor->name),
                            'logo_url' => $this->optionalImageUrl($logoPath),
                        ];
                    })
                    ->filter(fn (array $sponsor) => $sponsor['name'] !== '' || $sponsor['logo_url'] !== null)
                    ->values(),
            ];
        })->values();
    }

    public function mainSponsorGroups(AcademicEvent $event): Collection
    {
        $labels = [
            'vip' => 'VIP',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'exhibitors' => 'Exhibitors',
        ];

        $slots = $event->relationLoaded('mainSponsorSlots')
            ? $event->mainSponsorSlots
            : $event->mainSponsorSlots()->with('sponsor.master')->get();

        $sponsorsByLevel = $slots
            ->sortBy([
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->map(function ($slot) {
                $sponsor = $slot->sponsor;
                if (! $sponsor) {
                    return null;
                }

                $logoPath = $sponsor->logo_path ?: optional($sponsor->master)->logo_path;
                $logoUrl = $this->optionalImageUrl($logoPath);

                if ($logoUrl === null) {
                    return null;
                }

                return [
                    'level' => $slot->placement,
                    'name' => trim((string) $sponsor->name),
                    'logo_url' => $logoUrl,
                ];
            })
            ->filter()
            ->groupBy('level');

        return collect($labels)
            ->map(function (string $label, string $level) use ($sponsorsByLevel) {
                return [
                    'level' => $level,
                    'label' => $label,
                    'sponsors' => $sponsorsByLevel->get($level, collect())->values(),
                ];
            })
            ->filter(fn (array $group) => $group['sponsors']->isNotEmpty())
            ->values();
    }

    public function dateLine(AcademicEvent $event): string
    {
        if (! $event->start_at) {
            return '';
        }
        if ($event->end_at && ! $event->start_at->isSameDay($event->end_at)) {
            return $this->dateText($event->start_at) . ' ~ ' . $this->dateText($event->end_at);
        }

        return $this->dateText($event->start_at);
    }

    public function dateMachine(?Carbon $date): string
    {
        return $date ? $date->format('Y-m-d') : '';
    }

    /** @return array{year: string, month: string, day: string, weekday: string, datetime: string}|null */
    public function startDateParts(AcademicEvent $event): ?array
    {
        if (! $event->start_at) {
            return null;
        }

        return [
            'year' => $event->start_at->format('Y'),
            'month' => $event->start_at->format('n'),
            'day' => $event->start_at->format('j'),
            'weekday' => $this->weekday($event->start_at),
            'datetime' => $event->start_at->format('Y-m-d'),
        ];
    }

    /** @return array{label: string, date: string, datetime: string, days: int}|null */
    public function deadline(?Carbon $date): ?array
    {
        if (! $date) {
            return null;
        }

        return [
            'label' => $date->format('Y.m.d') . ' (' . $this->weekday($date) . ') 까지',
            'date' => $date->format('Y.m.d'),
            'datetime' => $date->format('Y-m-d'),
            'days' => (int) max(0, Carbon::today()->diffInDays($date, false)),
        ];
    }

    public function dateRangeText(?Carbon $start, ?Carbon $end, string $fallback = ''): string
    {
        if (! $start && ! $end) {
            return $fallback;
        }
        if ($start && $end) {
            return $start->format('Y. m. d') . ' (' . $this->weekday($start) . ') ~ '
                . $end->format('Y. m. d') . ' (' . $this->weekday($end) . ')';
        }

        $date = $start ?: $end;

        return $date->format('Y. m. d') . ' (' . $this->weekday($date) . ')';
    }

    private function dateText(Carbon $date): string
    {
        return $date->format('Y년 n월 j일') . ' (' . $this->weekday($date) . ')';
    }

    private function weekday(Carbon $date): string
    {
        $weekdays = ['일', '월', '화', '수', '목', '금', '토'];

        return $weekdays[(int) $date->dayOfWeek];
    }

    private function applyNoticeKeywordFilter($query, Request $request): void
    {
        $keyword = trim((string) $request->get('keyword', ''));
        if ($keyword === '') {
            return;
        }

        $searchType = $request->get('search_type', 'all');
        $like = '%' . $keyword . '%';

        $query->where(function ($q) use ($searchType, $like) {
            if ($searchType === 'title') {
                $q->where('title', 'like', $like);
            } elseif ($searchType === 'content') {
                $q->where('content', 'like', $like);
            } else {
                $q->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like);
            }
        });
    }

    private function fileSizeText(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0KB';
        }
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 1) . 'MB';
        }

        return number_format($bytes / 1024, 1) . 'KB';
    }

    /** @return array<string, array{view: string, page_type: string, gNum: string, sNum?: string, gName: string, sName: string, gSlug: string}> */
    private function pageDefinitions(AcademicEvent $event): array
    {
        $slugPrefix = 'academic_conference_' . $event->folder_name;

        return [
            '' => ['view' => 'index', 'page_type' => 'academic_conference', 'gNum' => 'main', 'gName' => '학술대회', 'sName' => $event->title, 'gSlug' => $slugPrefix],
            'invitation' => ['view' => 'kifm.invitation', 'page_type' => 'academic_conference', 'gNum' => '01', 'sNum' => '01', 'gName' => 'KIFM', 'sName' => '초대의 글', 'gSlug' => $slugPrefix . '_invitation'],
            'committee' => ['view' => 'kifm.committee', 'page_type' => 'academic_conference', 'gNum' => '01', 'sNum' => '02', 'gName' => 'KIFM', 'sName' => '조직위원회', 'gSlug' => $slugPrefix . '_committee'],
            'venue' => ['view' => 'kifm.venue', 'page_type' => 'academic_conference', 'gNum' => '01', 'sNum' => '03', 'gName' => 'KIFM', 'sName' => '행사장 안내', 'gSlug' => $slugPrefix . '_venue'],
            'program' => ['view' => 'program.program', 'page_type' => 'academic_conference', 'gNum' => '02', 'sNum' => '01', 'gName' => 'Program', 'sName' => 'Program', 'gSlug' => $slugPrefix . '_program'],
            'speakers' => ['view' => 'program.speakers', 'page_type' => 'academic_conference', 'gNum' => '02', 'sNum' => '02', 'gName' => 'Program', 'sName' => 'Speakers', 'gSlug' => $slugPrefix . '_speakers'],
            'registration' => ['view' => 'registration.info', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '01', 'gName' => 'Registration', 'sName' => 'Information', 'gSlug' => $slugPrefix . '_registration_info'],
            'registration/reg' => ['view' => 'registration.registration', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '02', 'gName' => 'Registration', 'sName' => '학술대회 사전등록', 'gSlug' => $slugPrefix . '_registration_reg'],
            'registration/form' => ['view' => 'registration.registration_form', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '02', 'gName' => 'Registration', 'sName' => '학술대회 사전등록 정보입력', 'gSlug' => $slugPrefix . '_registration_form'],
            'registration/form_non_member' => ['view' => 'registration.registration_form_non_member', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '02', 'gName' => 'Registration', 'sName' => '학술대회 사전등록 정보입력(비회원)', 'gSlug' => $slugPrefix . '_registration_form_non_member'],
            'registration/end' => ['view' => 'registration.registration_end', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '02', 'gName' => 'Registration', 'sName' => '학술대회 사전등록 결제 완료', 'gSlug' => $slugPrefix . '_registration_end'],
            'registration/check_member' => ['view' => 'registration.registration_check_member', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '03', 'gName' => 'Registration', 'sName' => '학술대회 등록 확인', 'gSlug' => $slugPrefix . '_registration_check_member'],
            'registration/check_non_member' => ['view' => 'registration.registration_check_non_member', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '03', 'gName' => 'Registration', 'sName' => '학술대회 등록 확인(비회원)', 'gSlug' => $slugPrefix . '_registration_check_non_member'],
            'registration/result' => ['view' => 'registration.registration_result', 'page_type' => 'academic_conference', 'gNum' => '03', 'sNum' => '03', 'gName' => 'Registration', 'sName' => '학술대회 등록 확인 완료', 'gSlug' => $slugPrefix . '_registration_result'],
            'abstract' => ['view' => 'abstract.abstract_info', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '01', 'gName' => 'Abstract', 'sName' => 'Information', 'gSlug' => $slugPrefix . '_abstract_info'],
            'abstract/submission' => ['view' => 'abstract.abstract_submit', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '02', 'gName' => 'Abstract', 'sName' => '초록 접수', 'gSlug' => $slugPrefix . '_abstract_submit'],
            'abstract/form_member' => ['view' => 'abstract.abstract_form_member', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '02', 'gName' => 'Abstract', 'sName' => '초록 접수', 'gSlug' => $slugPrefix . '_abstract_form_member'],
            'abstract/form_non_member' => ['view' => 'abstract.abstract_form_non_member', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '02', 'gName' => 'Abstract', 'sName' => '초록 접수', 'gSlug' => $slugPrefix . '_abstract_form_non_member'],
            'abstract/complete' => ['view' => 'abstract.abstract_complete', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '02', 'gName' => 'Abstract', 'sName' => '초록 접수', 'gSlug' => $slugPrefix . '_abstract_complete'],
            'abstract/check' => ['view' => 'abstract.abstract_check', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '03', 'gName' => 'Abstract', 'sName' => '학술대회 초록등록 확인', 'gSlug' => $slugPrefix . '_abstract_check'],
            'abstract/check_member' => ['view' => 'abstract.abstract_check', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '03', 'gName' => 'Abstract', 'sName' => '학술대회 초록등록 확인', 'gSlug' => $slugPrefix . '_abstract_check_member'],
            'abstract/check_non_member' => ['view' => 'abstract.abstract_check_non_member', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '03', 'gName' => 'Abstract', 'sName' => '학술대회 초록등록 확인', 'gSlug' => $slugPrefix . '_abstract_check_non_member'],
            'abstract/list' => ['view' => 'abstract.abstract_list', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '03', 'gName' => 'Abstract', 'sName' => '초록 접수 리스트', 'gSlug' => $slugPrefix . '_abstract_list'],
            'abstract/result' => ['view' => 'abstract.abstract_result', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '03', 'gName' => 'Abstract', 'sName' => '학술대회 초록등록 확인 완료', 'gSlug' => $slugPrefix . '_abstract_result'],
            'abstract/modify' => ['view' => 'abstract.abstract_modify', 'page_type' => 'academic_conference', 'gNum' => '04', 'sNum' => '03', 'gName' => 'Abstract', 'sName' => '학술대회 초록등록 확인 완료', 'gSlug' => $slugPrefix . '_abstract_modify'],
            'notice' => ['view' => 'notice.notice', 'page_type' => 'academic_conference', 'gNum' => '05', 'sNum' => '01', 'gName' => 'Notice', 'sName' => 'Notice', 'gSlug' => $slugPrefix . '_notice'],
            'notice/view' => ['view' => 'notice.notice_view', 'page_type' => 'academic_conference', 'gNum' => '05', 'sNum' => '01', 'gName' => 'Notice', 'sName' => 'Notice', 'gSlug' => $slugPrefix . '_notice_view'],
            'sponsors' => ['view' => 'etc.sponsors', 'page_type' => 'academic_conference', 'gNum' => '06', 'sNum' => '01', 'gName' => 'Sponsors', 'sName' => 'Sponsors', 'gSlug' => $slugPrefix . '_sponsors'],
            'exhibition' => ['view' => 'etc.exhibition', 'page_type' => 'academic_conference', 'gNum' => '07', 'sNum' => '01', 'gName' => 'Exhibition', 'sName' => 'Exhibition', 'gSlug' => $slugPrefix . '_exhibition'],
            'onsite' => ['view' => 'onsite.index', 'page_type' => 'academic_conference', 'gNum' => 'intro', 'gName' => '현장등록', 'sName' => '회원등록 안내', 'gSlug' => $slugPrefix . '_onsite'],
            'onsite_info' => ['view' => 'onsite.onsite_info', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '01', 'gName' => '현장등록', 'sName' => '학술대회 현장 등록', 'gSlug' => $slugPrefix . '_onsite_info'],
            'onsite_member_registration' => ['view' => 'onsite.onsite_member_registration', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '02', 'gName' => '현장등록', 'sName' => '회원 등록', 'gSlug' => $slugPrefix . '_onsite_member_registration'],
            'onsite_non_member_registration' => ['view' => 'onsite.onsite_non_member_registration', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '02', 'gName' => '현장등록', 'sName' => '비회원 등록', 'gSlug' => $slugPrefix . '_onsite_non_member_registration'],
            'onsite_welcome_approved' => ['view' => 'onsite.onsite_welcome_approved', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '02', 'gName' => '현장등록', 'sName' => '현장등록 완료', 'gSlug' => $slugPrefix . '_onsite_welcome_approved'],
            'onsite_check_registration' => ['view' => 'onsite.onsite_check_registration', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '03', 'gName' => '현장등록', 'sName' => '학술대회 현장등록 조회', 'gSlug' => $slugPrefix . '_onsite_check_registration'],
            'onsite_check_non_registration' => ['view' => 'onsite.onsite_check_non_registration', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '03', 'gName' => '현장등록', 'sName' => '학술대회 현장등록 조회', 'gSlug' => $slugPrefix . '_onsite_check_non_registration'],
            'onsite_confirmation_complete' => ['view' => 'onsite.onsite_confirmation_complete', 'page_type' => 'academic_conference', 'gNum' => 'academic_conference', 'sNum' => '03', 'gName' => '현장등록', 'sName' => '현장 등록 확인 완료', 'gSlug' => $slugPrefix . '_onsite_confirmation_complete'],
        ];
    }
}

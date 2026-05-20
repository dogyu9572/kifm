<?php

namespace App\Services\Frontend;

use App\Models\EduCourse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicOnlineAcademyService
{
    public const FALLBACK_HEAD_IMAGE = 'images/img_sample_online_academy_head.jpg';
    public const FALLBACK_LIST_IMAGE = 'images/img_sample_online_academy_list.jpg';
    public const FALLBACK_VIEW_IMAGE = 'images/img_sample_online_academy_view.jpg';

    /** @return array<string, string> */
    public function courseTypeLabels(): array
    {
        return [
            'required' => '필수 과정',
            'conference' => '학술대회 연계 과정',
            'training' => '연수강좌 연계 과정',
            'regular' => '수시 과정',
            'online_advanced' => '온라인 심화과정',
        ];
    }

    /** @return array<string, string> */
    public function searchFieldLabels(): array
    {
        return [
            'all' => '전체',
            'title' => '강의명',
            'topic_detail' => '강의주제',
            'professor_name' => '교수명',
        ];
    }

    public function paginateVisible(Request $request, int $perPage = 8): LengthAwarePaginator
    {
        $query = EduCourse::query()->with('professorMember');
        $this->applyVisibleScope($query);
        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('open_year')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return Collection<int, EduCourse> */
    public function featuredCourses(int $limit = 5): Collection
    {
        $query = EduCourse::query()->with('professorMember');
        $this->applyVisibleScope($query);

        return $query
            ->orderByDesc('open_year')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function findVisible(int $id): EduCourse
    {
        $query = EduCourse::query()->with(['professorMember', 'examQuestions']);
        $this->applyVisibleScope($query);

        return $query->findOrFail($id);
    }

    public function firstVisible(): ?EduCourse
    {
        $query = EduCourse::query();
        $this->applyVisibleScope($query);

        return $query->orderByDesc('open_year')->orderByDesc('id')->first();
    }

    /** @return array<string, mixed> */
    public function examPageData(EduCourse $course): array
    {
        $question = $course->examQuestions->first();
        $total = $course->examQuestions->count();
        $choices = is_array($question?->choices_json)
            ? array_values(array_filter(array_map('strval', $question->choices_json)))
            : [];
        $choiceItems = array_map(
            static fn (string $choice, int $index): array => [
                'id' => 'test_select' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'number' => $index + 1,
                'text' => $choice,
            ],
            $choices,
            array_keys($choices),
        );

        return [
            'question' => $question,
            'choices' => $choiceItems,
            'currentStep' => $question ? 1 : 0,
            'totalSteps' => $total,
            'stepText' => $question ? '01/' . str_pad((string) $total, 2, '0', STR_PAD_LEFT) : '00/00',
        ];
    }

    /** @return list<int> */
    public function yearOptions(): array
    {
        $query = EduCourse::query();
        $this->applyVisibleScope($query);

        return $query
            ->select('open_year')
            ->distinct()
            ->orderByDesc('open_year')
            ->pluck('open_year')
            ->map(static fn ($year): int => (int) $year)
            ->all();
    }

    /** @return list<string> */
    public function keywordOptions(): array
    {
        $query = EduCourse::query();
        $this->applyVisibleScope($query);

        return $query
            ->whereNotNull('keywords')
            ->pluck('keywords')
            ->flatMap(fn (?string $keywords) => $this->splitCsv($keywords))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    public function filters(Request $request): array
    {
        $courseTypes = array_keys($this->courseTypeLabels());
        $keywordFilters = $request->input('keywords', []);
        if (! is_array($keywordFilters)) {
            $keywordFilters = [$keywordFilters];
        }
        $courseType = (string) $request->input('course_type', $courseTypes[0] ?? '');
        if ($courseType !== '' && ! array_key_exists($courseType, $this->courseTypeLabels())) {
            $courseType = $courseTypes[0] ?? '';
        }

        return [
            'course_type' => $courseType,
            'open_year' => (string) $request->input('open_year', ''),
            'keywords' => array_values(array_filter(array_map('strval', $keywordFilters))),
            'search_field' => (string) $request->input('search_field', 'all'),
            'search_keyword' => trim((string) $request->input('search_keyword', '')),
        ];
    }

    public function imageUrl(?string $path, string $fallback): string
    {
        if ($path === null || $path === '') {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function lectureFileUrl(EduCourse $course): ?string
    {
        if (! $course->lecture_file_path) {
            return null;
        }

        return Storage::disk('public')->url($course->lecture_file_path);
    }

    public function periodText(EduCourse $course): string
    {
        if ($course->period_type === 'range' && $course->period_start && $course->period_end) {
            return $course->period_start->format('Y.m.d') . ' ~ ' . $course->period_end->format('Y.m.d');
        }

        return ((int) ($course->duration_days ?: 0)) . '일 수강';
    }

    public function professorText(EduCourse $course): string
    {
        $name = $course->professorMember->name ?? $course->professor_name ?? '';
        $org = $course->professorMember->workplace_name ?? $course->professor_org ?? '';

        if ($name === '' && $org === '') {
            return '';
        }
        if ($org === '') {
            return $name . ' 교수';
        }

        return $name . ' 교수 (' . $org . ')';
    }

    /** @return list<string> */
    public function topicList(?string $topics): array
    {
        return $this->splitCsv($topics);
    }

    /** @return list<string> */
    public function keywordList(?string $keywords): array
    {
        return $this->splitCsv($keywords);
    }

    public function summaryText(EduCourse $course, int $limit = 120): string
    {
        $text = trim(strip_tags((string) ($course->topic_detail ?: $course->content ?: '')));

        return $text !== '' ? Str::limit($text, $limit) : $course->title;
    }

    public function videoEmbedUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]+)~', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }
        if (preg_match('~vimeo\.com/(\d+)~', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return null;
    }

    private function applyVisibleScope(Builder $query): void
    {
        $query->where('use_yn', 'Y')->where('expose_yn', 'Y');
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $filters = $this->filters($request);

        if ($filters['course_type'] !== '') {
            $query->where('course_type', $filters['course_type']);
        }
        if ($filters['open_year'] !== '') {
            $query->where('open_year', (int) $filters['open_year']);
        }
        $keywords = array_values(array_filter(
            $filters['keywords'],
            static fn (string $keyword): bool => $keyword !== '' && $keyword !== '전체'
        ));
        if ($keywords !== []) {
            $query->where(function (Builder $q) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $q->orWhere('keywords', 'like', '%' . $keyword . '%');
                }
            });
        }
        if ($filters['search_keyword'] === '') {
            return;
        }

        $like = '%' . $filters['search_keyword'] . '%';
        $field = $filters['search_field'];

        $query->where(function (Builder $q) use ($field, $like): void {
            if ($field === 'title') {
                $q->where('title', 'like', $like);
                return;
            }
            if ($field === 'topic_detail') {
                $q->where('topic_detail', 'like', $like)->orWhere('topics', 'like', $like);
                return;
            }
            if ($field === 'professor_name') {
                $q->where('professor_name', 'like', $like)
                    ->orWhereHas('professorMember', fn (Builder $memberQ) => $memberQ->where('name', 'like', $like));
                return;
            }

            $q->where('title', 'like', $like)
                ->orWhere('topic_detail', 'like', $like)
                ->orWhere('topics', 'like', $like)
                ->orWhere('keywords', 'like', $like)
                ->orWhere('professor_name', 'like', $like)
                ->orWhereHas('professorMember', fn (Builder $memberQ) => $memberQ->where('name', 'like', $like));
        });
    }

    /** @return list<string> */
    private function splitCsv(?string $value): array
    {
        $items = preg_split('/\s*,\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_map('trim', $items ?: [])));
    }
}

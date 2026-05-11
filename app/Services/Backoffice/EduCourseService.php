<?php

namespace App\Services\Backoffice;

use App\Models\EduCourse;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EduCourseService
{
    /** @return array<string,string> */
    public static function courseTypeLabels(): array
    {
        return [
            'required' => '필수 과정',
            'conference' => '학술대회 연계 과정',
            'training' => '연수강좌 연계 과정',
            'regular' => '수시 과정',
            'online_advanced' => '온라인 심화과정',
        ];
    }

    /** @return array<string,string> */
    public static function gradeLabels(): array
    {
        return [
            'nonmember' => '비회원',
            'associate' => '준회원',
            'regular' => '정회원',
            'lifetime' => '평생회원',
            'senior' => '시니어회원',
        ];
    }

    /** @return array<string,string> */
    public static function searchFieldLabels(): array
    {
        return [
            'all' => '전체',
            'topic_detail' => '강의주제',
            'keywords' => '강의 키워드',
            'title' => '강의제목',
            'professor_name' => '교수명',
        ];
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $query = EduCourse::query()->with(['gradePrices', 'linkSetting', 'professorMember']);
        $this->applyFilters($query, $request);

        return $query->orderByDesc('id')->paginate($perPage)->withQueryString();
    }

    public function create(array $validated): EduCourse
    {
        return DB::transaction(function () use ($validated) {
            $course = new EduCourse();
            $this->fillCourse($course, $validated);
            $course->save();
            $this->syncChildRows($course, $validated);

            return $course->fresh(['gradePrices', 'examQuestions', 'linkSetting', 'professorMember']);
        });
    }

    public function update(EduCourse $course, array $validated): EduCourse
    {
        return DB::transaction(function () use ($course, $validated) {
            $this->fillCourse($course, $validated);
            $course->save();
            $this->syncChildRows($course, $validated);

            return $course->fresh(['gradePrices', 'examQuestions', 'linkSetting', 'professorMember']);
        });
    }

    public function deleteMany(array $ids): int
    {
        return EduCourse::destroy($ids);
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('course_type')) {
            $query->where('course_type', $request->string('course_type')->toString());
        }
        if ($request->filled('open_year')) {
            $query->where('open_year', (int) $request->input('open_year'));
        }
        if ($request->filled('use_yn')) {
            $query->where('use_yn', '=', $request->string('use_yn')->toString());
        }
        $keyword = trim((string) $request->input('search_keyword'));
        if ($keyword === '') {
            return;
        }
        $field = (string) $request->input('search_field', 'all');
        $like = '%' . $keyword . '%';
        $query->where(function (Builder $q) use ($field, $like) {
            if ($field === 'topic_detail') {
                $q->where('topic_detail', 'like', $like);
                return;
            }
            if ($field === 'keywords') {
                $q->where('keywords', 'like', $like);
                return;
            }
            if ($field === 'title') {
                $q->where('title', 'like', $like);
                return;
            }
            if ($field === 'professor_name') {
                $q->whereHas('professorMember', function (Builder $memberQuery) use ($like) {
                    $memberQuery->where('name', 'like', $like)
                        ->orWhere('workplace_name', 'like', $like);
                })->orWhere('professor_name', 'like', $like);
                return;
            }

            $q->where('topic_detail', 'like', $like)
                ->orWhere('keywords', 'like', $like)
                ->orWhere('title', 'like', $like)
                ->orWhere('professor_name', 'like', $like)
                ->orWhereHas('professorMember', function (Builder $memberQuery) use ($like) {
                    $memberQuery->where('name', 'like', $like)
                        ->orWhere('workplace_name', 'like', $like);
                });
        });
    }

    protected function fillCourse(EduCourse $course, array $validated): void
    {
        $professorMember = null;
        if (! empty($validated['professor_member_id'])) {
            $professorMember = User::query()
                ->select(['id', 'name', 'workplace_name'])
                ->find($validated['professor_member_id']);
        }

        $course->fill([
            'course_type' => $validated['course_type'],
            'open_year' => (int) $validated['open_year'],
            'linked_event_id' => $validated['linked_event_id'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'topics' => $validated['topics'] ?? null,
            'title' => $validated['title'],
            'professor_member_id' => $validated['professor_member_id'] ?? null,
            // 표시/검색/기존 스키마 호환을 위해 회원 정보를 캐시 컬럼에 동기화
            'professor_name' => $professorMember?->name ?? ($validated['professor_name'] ?? ''),
            'professor_org' => $professorMember?->workplace_name ?? ($validated['professor_org'] ?? null),
            'topic_detail' => $validated['topic_detail'] ?? null,
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'duration_min' => (int) $validated['duration_min'],
            'completion_score' => (int) $validated['completion_score'],
            'annual_fee_target' => $validated['annual_fee_target'],
            'free_yn' => $validated['free_yn'],
            'free_start_date' => $validated['free_yn'] === 'Y' ? ($validated['free_start_date'] ?? null) : null,
            'free_end_date' => $validated['free_yn'] === 'Y' ? ($validated['free_end_date'] ?? null) : null,
            'period_type' => $validated['period_type'],
            'duration_days' => $validated['period_type'] === 'days' ? ((int) ($validated['duration_days'] ?? 30)) : null,
            'period_start' => $validated['period_type'] === 'range' ? ($validated['period_start'] ?? null) : null,
            'period_end' => $validated['period_type'] === 'range' ? ($validated['period_end'] ?? null) : null,
            'exam_yn' => $validated['exam_yn'],
            'expose_yn' => $validated['expose_yn'],
            'use_yn' => $validated['use_yn'],
        ]);
    }

    protected function syncChildRows(EduCourse $course, array $validated): void
    {
        $course->gradePrices()->delete();
        foreach (($validated['grade_prices'] ?? []) as $grade => $row) {
            $course->gradePrices()->create([
                'grade_code' => $grade,
                'is_enabled' => (bool) ($row['enabled'] ?? false),
                'price' => ($row['enabled'] ?? false) ? (int) ($row['price'] ?? 0) : null,
            ]);
        }

        $course->examQuestions()->delete();
        if (($validated['exam_yn'] ?? 'N') === 'Y') {
            $questions = $validated['exam_questions'] ?? [];
            $order = 1;
            foreach ($questions as $question) {
                $text = trim((string) ($question['question'] ?? ''));
                if ($text === '') {
                    continue;
                }
                $course->examQuestions()->create([
                    'question' => $text,
                    'choices_json' => is_array($question['choices'] ?? null) ? array_values($question['choices']) : [],
                    'answer_index' => max(0, (int) ($question['answer_index'] ?? 0)),
                    'sort_order' => $order++,
                ]);
            }
        }

        $course->linkSetting()->delete();
        $course->linkSetting()->create([
            'link_event_id' => $validated['link_event_id'] ?? null,
            'link_training_id' => $validated['link_training_id'] ?? null,
            'link_round_use' => $validated['link_round_use'] ?? 'N',
            'link_round_ids' => $validated['link_round_use'] === 'Y' ? ($validated['link_round_ids'] ?? []) : [],
            'link_period_type' => $validated['link_period_type'] ?? 'days',
            'link_duration_days' => ($validated['link_period_type'] ?? 'days') === 'days'
                ? (int) ($validated['link_duration_days'] ?? 30)
                : null,
            'link_period_start' => ($validated['link_period_type'] ?? 'days') === 'range'
                ? ($validated['link_period_start'] ?? null)
                : null,
            'link_period_end' => ($validated['link_period_type'] ?? 'days') === 'range'
                ? ($validated['link_period_end'] ?? null)
                : null,
        ]);
    }
}


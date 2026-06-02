<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EduCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $gradeKeys = ['nonmember', 'associate', 'regular', 'lifetime', 'senior'];
        $grades = [];

        foreach ($gradeKeys as $grade) {
            $enabled = $this->boolean("grade_prices.$grade.enabled");
            $price = $this->input("grade_prices.$grade.price");
            $grades[$grade] = [
                'enabled' => $enabled,
                'price' => $price === '' ? null : $price,
            ];
        }

        $this->merge([
            'grade_prices' => $grades,
            'exam_questions' => is_array($this->input('exam_questions')) ? $this->input('exam_questions') : [],
            'link_round_ids' => is_array($this->input('link_round_ids')) ? $this->input('link_round_ids') : [],
            'duration_sec' => ((int) $this->input('duration_min', 0) * 60) + (int) $this->input('duration_seconds', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'course_type' => ['required', Rule::in(['required', 'conference', 'training', 'regular', 'online_advanced'])],
            'open_year' => ['required', 'integer', 'min:2010', 'max:2100'],
            'linked_event_id' => ['nullable', 'integer'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'topics' => ['nullable', 'string', 'max:500'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:5120'],
            'title' => ['required', 'string', 'max:200'],
            'professor_name' => ['nullable', 'string', 'max:100'],
            'professor_org' => ['nullable', 'string', 'max:200'],
            'professor_member_id' => ['required', 'integer', 'exists:users,id'],
            'topic_detail' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'lecture_file' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,doc,docx', 'max:51200'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'duration_min' => ['required', 'integer', 'min:0', 'max:10000'],
            'duration_seconds' => ['required', 'integer', 'min:0', 'max:59'],
            'duration_sec' => ['required', 'integer', 'min:0', 'max:600000'],
            'completion_score' => ['required', 'integer', 'min:0', 'max:100'],
            'annual_fee_target' => ['required', Rule::in(['all', 'paid'])],
            'free_yn' => ['required', Rule::in(['Y', 'N'])],
            'free_start_date' => ['nullable', 'date', 'required_if:free_yn,Y'],
            'free_end_date' => ['nullable', 'date', 'required_if:free_yn,Y', 'after_or_equal:free_start_date'],
            'period_type' => ['required', Rule::in(['days', 'range'])],
            'duration_days' => ['nullable', 'integer', 'min:1', 'required_if:period_type,days'],
            'period_start' => ['nullable', 'date', 'required_if:period_type,range'],
            'period_end' => ['nullable', 'date', 'required_if:period_type,range', 'after_or_equal:period_start'],
            'exam_yn' => ['required', Rule::in(['Y', 'N'])],
            'expose_yn' => ['required', Rule::in(['Y', 'N'])],
            'use_yn' => ['required', Rule::in(['Y', 'N'])],
            'grade_prices' => ['required', 'array'],
            'grade_prices.*.enabled' => ['boolean'],
            'grade_prices.*.price' => ['nullable', 'integer', 'min:0'],
            'exam_questions' => ['nullable', 'array'],
            'exam_questions.*.question' => ['nullable', 'string'],
            'exam_questions.*.choices' => ['nullable', 'array'],
            'exam_questions.*.answer_index' => ['nullable', 'integer', 'min:0'],
            'link_event_id' => ['nullable', 'integer'],
            'link_training_id' => ['nullable', 'integer', 'exists:edu_trainings,id'],
            'link_round_use' => ['nullable', Rule::in(['Y', 'N'])],
            'link_round_ids' => ['nullable', 'array'],
            'link_round_ids.*' => ['string', 'max:20'],
            'link_period_type' => ['nullable', Rule::in(['days', 'range'])],
            'link_duration_days' => ['nullable', 'integer', 'min:1'],
            'link_period_start' => ['nullable', 'date'],
            'link_period_end' => ['nullable', 'date', 'after_or_equal:link_period_start'],
            'delete_thumbnail' => ['nullable', 'boolean'],
            'delete_lecture_file' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('exam_yn') === 'Y') {
                $validQuestions = collect($this->input('exam_questions', []))
                    ->filter(fn ($row) => is_array($row) && trim((string) ($row['question'] ?? '')) !== '')
                    ->values();

                if ($validQuestions->isEmpty()) {
                    $validator->errors()->add('exam_questions', '시험 사용 시 문제를 1개 이상 입력해야 합니다.');
                }
            }

            $enabledGrades = collect($this->input('grade_prices', []))
                ->filter(fn ($row) => (bool) ($row['enabled'] ?? false));
            if ($enabledGrades->isEmpty()) {
                $validator->errors()->add('grade_prices', '수강 가능 회원을 1개 이상 선택하세요.');
            }
            $enabledGrades->each(function ($row, $grade) use ($validator) {
                if (($row['price'] ?? null) === null || $row['price'] === '') {
                    $validator->errors()->add("grade_prices.$grade.price", '선택한 회원의 금액을 입력하세요.');
                }
            });

            if ($this->input('course_type') === 'conference' && ! $this->filled('linked_event_id')) {
                $validator->errors()->add('linked_event_id', '학술대회 연계 과정은 연계 학술대회 선택이 필요합니다.');
            }

            if ($this->input('link_round_use') === 'Y' && count($this->input('link_round_ids', [])) < 1) {
                $validator->errors()->add('link_round_ids', '차수별 연동 사용 시 차수를 1개 이상 선택하세요.');
            }
        });
    }
}

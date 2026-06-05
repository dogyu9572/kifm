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
        $annualFeeTarget = (string) $this->input('annual_fee_target', 'all');
        $freeYn = $annualFeeTarget === 'paid' ? 'N' : (string) $this->input('free_yn', 'N');
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
            'free_yn' => $freeYn,
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

            if ($this->input('free_yn') !== 'Y') {
                $enabledGrades = collect($this->input('grade_prices', []))
                    ->filter(fn ($row) => (bool) ($row['enabled'] ?? false));
                if ($enabledGrades->isEmpty()) {
                    $validator->errors()->add('grade_prices', '무료 미제공 강좌는 수강 가능 회원을 1개 이상 선택하세요.');
                }
                $enabledGrades->each(function ($row, $grade) use ($validator) {
                    if (($row['price'] ?? null) === null || $row['price'] === '') {
                        $validator->errors()->add("grade_prices.$grade.price", '무료 미제공 강좌는 선택한 회원의 금액을 입력하세요.');
                    }
                });
            }

            if ($this->input('course_type') === 'conference' && ! $this->filled('linked_event_id')) {
                $validator->errors()->add('linked_event_id', '학술대회 연계 과정은 연계 학술대회 선택이 필요합니다.');
            }

            if ($this->input('link_round_use') === 'Y' && count($this->input('link_round_ids', [])) < 1) {
                $validator->errors()->add('link_round_ids', '차수별 연동 사용 시 차수를 1개 이상 선택하세요.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute을(를) 입력해주세요.',
            'required_if' => ':attribute을(를) 입력해주세요.',
            'integer' => ':attribute은(는) 숫자로 입력해주세요.',
            'min' => ':attribute은(는) :min 이상으로 입력해주세요.',
            'max' => ':attribute은(는) :max 이하로 입력해주세요.',
            'string.max' => ':attribute은(는) :max자 이하로 입력해주세요.',
            'date' => ':attribute은(는) 올바른 날짜로 입력해주세요.',
            'after_or_equal' => ':attribute은(는) :date 이후 날짜로 입력해주세요.',
            'url' => ':attribute은(는) 올바른 URL로 입력해주세요.',
            'file' => ':attribute은(는) 파일로 등록해주세요.',
            'mimes' => ':attribute은(는) 허용된 파일 형식만 등록할 수 있습니다.',
            'in' => ':attribute을(를) 올바르게 선택해주세요.',
            'exists' => '선택한 :attribute 정보를 찾을 수 없습니다.',
            'professor_member_id.required' => '강사를 선택해주세요.',
            'free_start_date.required_if' => '무료 제공 시작일을 입력해주세요.',
            'free_end_date.required_if' => '무료 제공 종료일을 입력해주세요.',
            'duration_days.required_if' => '수강 기간을 입력해주세요.',
            'period_start.required_if' => '수강 시작일을 입력해주세요.',
            'period_end.required_if' => '수강 종료일을 입력해주세요.',
        ];
    }

    public function attributes(): array
    {
        return [
            'course_type' => '과정 유형',
            'open_year' => '개설연도',
            'linked_event_id' => '연계 학술대회',
            'keywords' => '강의 키워드',
            'topics' => '강의 주제',
            'thumbnail' => '썸네일',
            'title' => '강의 제목',
            'professor_member_id' => '강사',
            'topic_detail' => '강의주제 상세',
            'content' => '강의 내용',
            'lecture_file' => '강의록',
            'video_url' => '강의 URL',
            'duration_min' => '강의시간',
            'duration_seconds' => '강의 초',
            'duration_sec' => '강의 총 시간',
            'completion_score' => '수강 완료 평점',
            'annual_fee_target' => '연회비 납부 대상 강의',
            'free_yn' => '무료 제공 여부',
            'free_start_date' => '무료 제공 시작일',
            'free_end_date' => '무료 제공 종료일',
            'period_type' => '유료 강좌 기간',
            'duration_days' => '수강 기간',
            'period_start' => '수강 시작일',
            'period_end' => '수강 종료일',
            'exam_yn' => '시험 여부',
            'expose_yn' => '상단 노출여부',
            'use_yn' => '사용여부',
            'grade_prices' => '수강 가능 회원',
            'grade_prices.*.price' => '회원별 금액',
            'link_round_ids' => '연동 차수',
        ];
    }
}

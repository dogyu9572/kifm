<?php

namespace App\Http\Requests\Backoffice;

use App\Models\EduTraining;
use App\Services\Backoffice\EduTrainingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EduTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gradeKeys = array_keys(EduTrainingService::registrationGradeLabels());

        $gradeRules = [];
        foreach ($gradeKeys as $gk) {
            $gradeRules["rounds.*.grades.{$gk}.eligible"] = ['nullable', 'boolean'];
            $gradeRules["rounds.*.grades.{$gk}.price"] = ['nullable', 'numeric', 'min:0'];
        }

        $rules = [
            'year' => ['required', 'integer', 'between:2000,2100'],
            'season' => ['required', Rule::in(['spring', 'summer', 'fall', 'winter'])],
            'title' => ['required', 'string', 'max:200'],
            'use_round' => ['required', 'boolean'],
            'round_type' => ['nullable', 'string', 'max:50'],
            'training_method' => [
                Rule::requiredIf(fn () => ! $this->boolean('use_round')),
                'nullable',
                Rule::in(['offline', 'online', 'hybrid']),
            ],
            'status' => ['required', Rule::in(['PUBLIC', 'PRIVATE'])],
            'overview' => ['nullable', 'string'],
            'registration_info' => ['nullable', 'string'],
            'introduction' => ['nullable', 'string'],
            'textbook_file' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,hwp', 'max:20480'],
            'delete_textbook_file' => ['nullable', 'boolean'],
            'attachment_files' => ['nullable', 'array'],
            'attachment_files.*' => ['nullable', 'file', 'max:20480', 'mimes:pdf,ppt,pptx,hwp'],
            'rounds' => [Rule::requiredIf((bool) $this->boolean('use_round')), 'array'],
            'rounds.*.round_label' => ['nullable', 'string', 'max:30'],
            'rounds.*.training_method' => ['required_with:rounds', Rule::in(['offline', 'online', 'hybrid'])],
            'rounds.*.lecture_date' => ['required_with:rounds', 'date'],
            'rounds.*.location_link' => ['required_with:rounds', 'string', 'max:255'],
            'rounds.*.apply_start_date' => ['nullable', 'date'],
            'rounds.*.apply_end_date' => ['nullable', 'date'],
            'rounds.*.capacity' => ['nullable', 'integer', 'min:1'],
            'rounds.*.is_capacity_unlimited' => ['nullable', 'boolean'],
            'rounds.*.duration_hours' => ['required_with:rounds', 'numeric', 'min:0'],
            'rounds.*.score' => ['required_with:rounds', 'numeric', 'min:0'],
            'rounds.*.status' => ['required_with:rounds', Rule::in(['PUBLIC', 'PRIVATE'])],
        ];

        $rules = array_merge($rules, $gradeRules);

        $training = $this->route('edu_training');
        if ($training instanceof EduTraining) {
            $rules['delete_attachment_ids'] = ['nullable', 'array'];
            $rules['delete_attachment_ids.*'] = [
                'integer',
                Rule::exists('edu_training_attachments', 'id')->where(
                    'edu_training_id',
                    $training->id
                ),
            ];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $rounds = $this->input('rounds');
        if (! is_array($rounds)) {
            $rounds = [];
        }

        $useRound = $this->boolean('use_round');

        $gradeKeys = array_keys(EduTrainingService::registrationGradeLabels());

        $normalized = [];
        if ($useRound) {
            foreach ($rounds as $round) {
                if (! is_array($round)) {
                    continue;
                }
                $gradesIn = is_array($round['grades'] ?? null) ? $round['grades'] : [];
                $gradesOut = [];
                foreach ($gradeKeys as $gk) {
                    $g = is_array($gradesIn[$gk] ?? null) ? $gradesIn[$gk] : [];
                    $rawEl = $g['eligible'] ?? false;
                    if (is_array($rawEl)) {
                        $eligibleOut = (bool) array_filter(
                            $rawEl,
                            static fn ($v) => filter_var($v, FILTER_VALIDATE_BOOL)
                        );
                    } else {
                        $eligibleOut = filter_var($rawEl, FILTER_VALIDATE_BOOL);
                    }
                    $gradesOut[$gk] = [
                        'eligible' => $eligibleOut,
                        'price' => $g['price'] ?? null,
                    ];
                }
                $normalized[] = [
                    'round_label' => isset($round['round_label']) ? trim((string) $round['round_label']) : null,
                    'training_method' => $round['training_method'] ?? null,
                    'lecture_date' => $round['lecture_date'] ?? null,
                    'location_link' => isset($round['location_link']) ? trim((string) $round['location_link']) : null,
                    'apply_start_date' => $round['apply_start_date'] ?? null,
                    'apply_end_date' => $round['apply_end_date'] ?? null,
                    'capacity' => $round['capacity'] ?? null,
                    'is_capacity_unlimited' => filter_var($round['is_capacity_unlimited'] ?? false, FILTER_VALIDATE_BOOL),
                    'duration_hours' => $round['duration_hours'] ?? null,
                    'score' => $round['score'] ?? null,
                    'grades' => $gradesOut,
                    'status' => $round['status'] ?? 'PUBLIC',
                ];
            }
        }

        $this->merge([
            'rounds' => $normalized,
            'use_round' => $useRound,
        ]);

        if ($useRound && count($normalized) > 0) {
            $methods = array_values(array_unique(array_filter(array_map(
                static fn (array $r) => $r['training_method'] ?? null,
                $normalized
            ))));
            if (count($methods) === 1) {
                $this->merge(['training_method' => $methods[0]]);
            } elseif (count($methods) > 1) {
                $this->merge(['training_method' => 'hybrid']);
            }
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->boolean('use_round') && count($this->input('rounds', [])) < 1) {
                $validator->errors()->add('rounds', '다회차 관리 시 최소 1개 차수가 필요합니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'rounds.*.lecture_date.required_with' => ':attribute 항목은 차수 사용 시 필수입니다.',
            'rounds.*.location_link.required_with' => ':attribute 항목은 차수 사용 시 필수입니다.',
            'rounds.*.duration_hours.required_with' => ':attribute 항목은 차수 사용 시 필수입니다.',
            'rounds.*.score.required_with' => ':attribute 항목은 차수 사용 시 필수입니다.',
        ];
    }

    public function attributes(): array
    {
        return [
            'rounds.*.lecture_date' => '강의 일시',
            'rounds.*.location_link' => '장소 / 링크',
            'rounds.*.duration_hours' => '연수 시간',
            'rounds.*.score' => '이수 평점',
        ];
    }
}

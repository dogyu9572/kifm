<?php

namespace App\Http\Requests\Backoffice;

use App\Models\PaymentPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $grades = $this->input('grades');
        if (! is_array($grades)) {
            $grades = [];
        }
        $types = $this->input('member_types');
        if (! is_array($types)) {
            $types = [];
        }
        $this->merge([
            'grades' => $grades,
            'member_types' => $types,
        ]);
    }

    public function rules(): array
    {
        $gradeValues = array_keys(\App\Services\Backoffice\PaymentPlanService::gradeLabels());
        $typeValues = array_keys(\App\Services\Backoffice\PaymentPlanService::memberTypeLabels());

        $uniqueName = Rule::unique('payment_plans', 'plan_name')
            ->where('category', $this->input('category'));

        $existing = $this->route('payment_plan');
        if ($existing instanceof PaymentPlan) {
            $uniqueName->ignore($existing->id);
        }

        $rules = [
            'plan_name' => ['required', 'string', 'max:200', $uniqueName],
            'category' => ['required', Rule::in(['conference', 'membership', 'education'])],
            'member_status' => ['required', Rule::in(['member', 'non-member'])],
            'use_status' => ['required', Rule::in(['active', 'inactive'])],
            'grades' => [
                Rule::requiredIf((string) $this->input('member_status') === 'member'),
                'array',
            ],
            'grades.*' => [Rule::in($gradeValues)],
            'member_types' => [
                Rule::requiredIf((string) $this->input('member_status') === 'member'),
                'array',
            ],
            'member_types.*' => [Rule::in($typeValues)],
            'executive' => [
                Rule::requiredIf((string) $this->input('member_status') === 'member'),
                'nullable',
                Rule::in(['executive', 'non-executive']),
            ],
            'price_early' => [
                Rule::requiredIf((string) $this->input('category') === 'conference'),
                'nullable',
                'integer',
                'min:0',
            ],
            'price_site' => [
                Rule::requiredIf((string) $this->input('category') === 'conference'),
                'nullable',
                'integer',
                'min:0',
            ],
            'price' => [
                Rule::requiredIf(
                    in_array((string) $this->input('category'), ['membership', 'education'], true)
                ),
                'nullable',
                'integer',
                'min:0',
            ],
        ];

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ((string) $this->input('member_status') === 'member') {
                if (count($this->input('grades', [])) < 1) {
                    $v->errors()->add('grades', '회원 등급을 1개 이상 선택하세요.');
                }
                if (count($this->input('member_types', [])) < 1) {
                    $v->errors()->add('member_types', '구분을 1개 이상 선택하세요.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'plan_name.unique' => '같은 결제 항목 유형에서 동일한 결제항목명이 이미 있습니다.',
        ];
    }
}

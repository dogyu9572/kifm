<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberExecutiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('term_end_date') === '') {
            $this->merge(['term_end_date' => null]);
        }
    }

    public function rules(): array
    {
        $roles = array_keys(\App\Models\MemberExecutive::roleLabels());
        $isIndefinite = (bool) $this->boolean('is_indefinite');

        return [
            'member_id' => ['required', 'integer', 'exists:users,id'],
            'executive_role' => ['required', Rule::in($roles)],
            'term_start_date' => ['required', 'date'],
            'term_end_date' => [
                Rule::requiredIf(! $isIndefinite),
                'nullable',
                'date',
                'after_or_equal:term_start_date',
            ],
            'is_indefinite' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute을(를) 입력해주세요.',
            'integer' => ':attribute은(는) 숫자로 입력해주세요.',
            'date' => ':attribute은(는) 올바른 날짜로 입력해주세요.',
            'max' => ':attribute은(는) :max자 이하로 입력해주세요.',
            'in' => ':attribute을(를) 올바르게 선택해주세요.',
            'exists' => '선택한 :attribute 정보를 찾을 수 없습니다.',
            'term_end_date.required' => '임기 종료일을 입력하거나 무기한을 선택해주세요.',
            'term_end_date.after_or_equal' => '임기 종료일은 임기 시작일 이후여야 합니다.',
        ];
    }

    public function attributes(): array
    {
        return [
            'member_id' => '회원',
            'executive_role' => '직책',
            'term_start_date' => '임기 시작일',
            'term_end_date' => '임기 종료일',
            'is_indefinite' => '무기한 여부',
            'note' => '비고',
            'is_active' => '활성 상태',
        ];
    }
}

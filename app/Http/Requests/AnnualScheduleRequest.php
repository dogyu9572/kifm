<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnualScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_single_day' => ['nullable', 'boolean'],
            'content' => ['nullable', 'string', 'max:1000'],
            'is_visible' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '일정명은 필수 입력 항목입니다.',
            'start_date.required' => '시작일은 필수 입력 항목입니다.',
            'end_date.required' => '종료일은 필수 입력 항목입니다.',
            'end_date.after_or_equal' => '종료일은 시작일과 같거나 이후여야 합니다.',
            'content.max' => '일정 내용은 최대 1000자까지 입력 가능합니다.',
            'is_visible.required' => '노출 여부를 선택해주세요.',
        ];
    }
}

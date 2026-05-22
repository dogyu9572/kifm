<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicEventSessionItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['nullable', 'array'],
            'items.*.row_type' => ['required', Rule::in(['abstract', 'break'])],
            'items.*.academic_event_abstract_id' => ['nullable', 'integer', 'exists:academic_event_abstracts,id'],
            'items.*.start_time' => ['required', 'date_format:H:i'],
            'items.*.end_time' => ['required', 'date_format:H:i'],
            'items.*.title' => ['nullable', 'string', 'max:500'],
            'items.*.presenter' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'items.*.row_type' => '행 구분',
            'items.*.start_time' => '시작 시간',
            'items.*.end_time' => '종료 시간',
            'items.*.title' => '초록명/제목',
            'items.*.presenter' => '발표자/연자',
        ];
    }
}

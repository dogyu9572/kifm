<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertifiedMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:users,id'],
            'validity_start_date' => ['required', 'date'],
            'validity_end_date' => ['required', 'date', 'after_or_equal:validity_start_date'],
            'acquired_date' => ['required', 'date'],
            'acquired_validity_start' => ['required', 'date'],
            'acquired_validity_end' => ['required', 'date', 'after_or_equal:acquired_validity_start'],
            'winter_course_completed' => ['nullable', 'boolean'],
            'exam_passed' => ['nullable', 'boolean'],
        ];
    }
}


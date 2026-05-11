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
}


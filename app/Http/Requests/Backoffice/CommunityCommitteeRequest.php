<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommunityCommitteeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'committee_type' => ['required', Rule::in(['general', 'special'])],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'delete_thumbnail' => ['nullable', 'boolean'],
            'delete_banner' => ['nullable', 'boolean'],
            'pending_count' => ['nullable', 'integer', 'min:0'],
            'member_count' => ['nullable', 'integer', 'min:0'],
            'member_limit' => ['nullable', 'integer', 'min:1'],
            'no_member_limit' => ['nullable', 'boolean'],
            'visibility_yn' => ['required', Rule::in(['Y', 'N'])],
            'is_mandatory' => ['nullable', Rule::in(['Y', 'N'])],
            'regulation' => ['nullable', 'string'],
            'protocol' => ['nullable', 'string'],
            'committee_members' => ['nullable', 'array'],
            'committee_members.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'committee_members.*.role' => ['required', Rule::in(['chairman', 'secretary', 'member'])],
        ];
    }
}


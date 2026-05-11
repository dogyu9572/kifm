<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_number' => ['nullable', 'string', 'max:30'],
            'recipient_type' => ['required', 'in:all,addressbook,specific'],
            'member_grade' => ['nullable', 'string', 'max:30'],
            'exclude_phones' => ['nullable', 'string'],
            'schedule_enabled' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
            'subject' => ['nullable', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:2000'],
            'submit_action' => ['required', 'in:draft,send'],
            'selected_address_books' => ['nullable', 'string'],
            'selected_member_ids' => ['nullable', 'string'],
        ];
    }
}

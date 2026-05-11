<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_name' => ['nullable', 'string', 'max:100'],
            'sender_email' => ['nullable', 'email', 'max:255'],
            'recipient_type' => ['required', 'in:all,addressbook,specific,executive'],
            'member_grade' => ['nullable', 'string', 'max:30'],
            'exclude_emails' => ['nullable', 'string'],
            'schedule_enabled' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
            'mail_type' => ['required', 'in:bulk,newsletter'],
            'subject' => ['nullable', 'string', 'max:200'],
            'body' => ['nullable', 'string'],
            'submit_action' => ['required', 'in:draft,send'],
            'selected_address_books' => ['nullable', 'string'],
            'selected_member_ids' => ['nullable', 'string'],
            'selected_executive_ids' => ['nullable', 'string'],
        ];
    }
}

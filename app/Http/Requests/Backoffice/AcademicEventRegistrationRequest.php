<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_event_id' => ['required', 'integer', 'exists:academic_events,id'],
            'reg_type' => ['required', Rule::in(['pre', 'onsite'])],
            'member_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'license_no' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'registered_at' => ['required', 'date'],
            'payment_status' => ['required', Rule::in(['pending_payment', 'pending', 'completed', 'cancel_requested', 'cancelled'])],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer', 'onsite'])],
            'bank_depositor' => ['nullable', 'string', 'max:100', 'required_if:payment_method,bank_transfer'],
            'bank_deposit_date' => ['nullable', 'date', 'required_if:payment_method,bank_transfer'],
            'bank_account_text' => ['nullable', 'string', 'max:5000'],
            'admin_memo' => ['nullable', 'string'],
            'receipt_issue' => ['required', Rule::in(['NO', 'YES'])],
            'receipt_type' => ['nullable', 'required_if:receipt_issue,YES', Rule::in(['PERSONAL', 'CARD'])],
            'receipt_number' => ['nullable', 'string', 'max:100', 'required_if:receipt_issue,YES'],
            'refund_bank' => ['nullable', 'string', 'max:100'],
            'refund_account' => ['nullable', 'string', 'max:100'],
            'refund_holder' => ['nullable', 'string', 'max:100'],
            'payment_items_payload' => ['required', 'string'],
        ];
    }
}

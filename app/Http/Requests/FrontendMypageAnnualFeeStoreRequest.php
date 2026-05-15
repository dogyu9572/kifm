<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrontendMypageAnnualFeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'user';
    }

    public function rules(): array
    {
        return [
            'membership_plan_id' => ['required', 'integer', 'exists:payment_plans,id'],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer'])],
            'depositor_name' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'refund_bank_name' => ['nullable', 'string', 'max:100'],
            'refund_account_no' => ['nullable', 'string', 'max:120'],
            'refund_holder_name' => ['nullable', 'string', 'max:120'],
        ];
    }
}

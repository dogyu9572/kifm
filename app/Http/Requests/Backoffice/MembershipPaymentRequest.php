<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembershipPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isBank = (string) $this->input('payment_method') === 'bank_transfer';
        $isReceiptIssued = (string) $this->input('receipt_issue') === 'YES';
        $isCancelled = (string) $this->input('payment_status') === 'cancelled';

        return [
            'membership_plan_id' => ['nullable', 'integer', 'exists:payment_plans,id'],
            'payment_status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer'])],
            'depositor_name' => [Rule::requiredIf($isBank), 'nullable', 'string', 'max:100'],
            'paid_at' => [Rule::requiredIf($isBank), 'nullable', 'date'],
            'receipt_issue' => ['required', Rule::in(['NO', 'YES'])],
            'receipt_type' => [Rule::requiredIf($isReceiptIssued), 'nullable', Rule::in(['PERSONAL', 'CARD'])],
            'receipt_number' => [Rule::requiredIf($isReceiptIssued), 'nullable', 'string', 'max:120'],
            'refund_bank_name' => [Rule::requiredIf($isCancelled), 'nullable', 'string', 'max:100'],
            'refund_account_no' => [Rule::requiredIf($isCancelled), 'nullable', 'string', 'max:120'],
            'refund_holder_name' => [Rule::requiredIf($isCancelled), 'nullable', 'string', 'max:120'],
        ];
    }
}


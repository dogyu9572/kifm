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
        $isCompleted = (string) $this->input('payment_status') === 'completed';
        $isReceiptIssued = (string) $this->input('receipt_issue') === 'YES';
        return [
            'membership_plan_id' => ['nullable', 'integer', 'exists:payment_plans,id'],
            'payment_status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer'])],
            'depositor_name' => [Rule::requiredIf($isBank && $isCompleted), 'nullable', 'string', 'max:100'],
            'paid_at' => [Rule::requiredIf($isBank && $isCompleted), 'nullable', 'date'],
            'receipt_issue' => ['required', Rule::in(['NO', 'YES'])],
            'receipt_type' => [Rule::requiredIf($isReceiptIssued), 'nullable', Rule::in(['PERSONAL', 'CARD'])],
            'receipt_number' => [Rule::requiredIf($isReceiptIssued), 'nullable', 'string', 'max:120'],
            'refund_bank_name' => ['nullable', 'string', 'max:100'],
            'refund_account_no' => ['nullable', 'string', 'max:120'],
            'refund_holder_name' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute을(를) 입력해주세요.',
            'integer' => ':attribute은(는) 숫자로 입력해주세요.',
            'date' => ':attribute은(는) 올바른 날짜로 입력해주세요.',
            'max' => ':attribute은(는) :max자 이하로 입력해주세요.',
            'in' => ':attribute을(를) 올바르게 선택해주세요.',
            'exists' => '선택한 :attribute 정보를 찾을 수 없습니다.',
        ];
    }

    public function attributes(): array
    {
        return [
            'membership_plan_id' => '연회비 항목',
            'payment_status' => '납부 상태',
            'payment_method' => '결제 수단',
            'depositor_name' => '입금자명',
            'paid_at' => '결제 완료일',
            'receipt_issue' => '현금영수증 발행 여부',
            'receipt_type' => '발급 유형',
            'receipt_number' => '발급 번호',
            'refund_bank_name' => '은행명',
            'refund_account_no' => '계좌번호',
            'refund_holder_name' => '예금주명',
        ];
    }
}

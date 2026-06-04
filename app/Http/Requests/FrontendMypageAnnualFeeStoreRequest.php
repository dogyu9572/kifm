<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'terms_agree' => ['accepted'],
            'depositor_name' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'deposit_expected_date' => ['required_if:payment_method,bank_transfer', 'nullable', 'date'],
            'refund_bank_name' => ['nullable', 'string', 'max:100'],
            'refund_account_no' => ['nullable', 'string', 'max:120'],
            'refund_holder_name' => ['nullable', 'string', 'max:120'],
            'receipt_issue' => ['nullable', Rule::in(['NO', 'YES'])],
            'receipt_type' => ['nullable', 'required_if:receipt_issue,YES', Rule::in(['PERSONAL', 'CARD'])],
            'receipt_number' => ['nullable', 'required_if:receipt_issue,YES', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'membership_plan_id.required' => '결제 항목을 선택해주세요.',
            'payment_method.required' => '결제수단을 선택해주세요.',
            'terms_agree.accepted' => '결제 이용 약관 및 개인정보 처리 동의에 체크해주세요.',
            'depositor_name.required_if' => '입금자명을 입력해 주세요.',
            'deposit_expected_date.required_if' => '입금 예정일을 입력해 주세요.',
            'deposit_expected_date.date' => '입금 예정일 형식을 확인해 주세요.',
            'receipt_type.required_if' => '현금영수증 발급 구분을 입력해 주세요.',
            'receipt_number.required_if' => '현금영수증 번호를 입력해 주세요.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new ValidationException($validator, response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422));
        }

        $this->session()->flash('alert', $validator->errors()->first());

        throw (new ValidationException($validator))
            ->redirectTo($this->getRedirectUrl());
    }
}

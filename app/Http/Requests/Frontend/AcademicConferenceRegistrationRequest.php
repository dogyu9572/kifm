<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicConferenceRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->role === 'user';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/\D+/', '', (string) $this->input('phone')),
            'payment_plan_ids' => is_array($this->input('payment_plan_ids')) ? $this->input('payment_plan_ids') : [],
            'coupon_code' => $this->filled('coupon_code') ? strtoupper(trim((string) $this->input('coupon_code'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'affiliated_hospital' => ['nullable', 'string', 'max:200'],
            'workplace_phone' => ['nullable', 'string', 'max:40'],
            'address_postcode' => ['nullable', 'string', 'max:20'],
            'address_base' => ['nullable', 'string', 'max:300'],
            'address_detail' => ['nullable', 'string', 'max:300'],
            'payment_plan_ids' => ['required', 'array', 'min:1'],
            'payment_plan_ids.*' => ['integer', 'exists:payment_plans,id'],
            'membership_plan_id' => ['nullable', 'integer', 'exists:payment_plans,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer'])],
            'bank_depositor' => ['nullable', 'required_if:payment_method,bank_transfer', 'string', 'max:100'],
            'bank_deposit_date' => ['nullable', 'required_if:payment_method,bank_transfer', 'date'],
            'bank_account_text' => ['nullable', 'string', 'max:5000'],
            'receipt_issue' => ['required', Rule::in(['NO', 'YES'])],
            'receipt_type' => ['nullable', 'required_if:receipt_issue,YES', Rule::in(['PERSONAL', 'CARD'])],
            'receipt_number' => ['nullable', 'required_if:receipt_issue,YES', 'string', 'max:100'],
            'terms_agree' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => '이메일',
            'phone' => '휴대폰번호',
            'affiliated_hospital' => '직장명',
            'workplace_phone' => '직장 전화',
            'address_postcode' => '우편번호',
            'address_base' => '주소',
            'address_detail' => '상세주소',
            'payment_plan_ids' => '결제 항목',
            'bank_depositor' => '입금자명',
            'bank_deposit_date' => '입금 예정일',
            'receipt_issue' => '현금영수증 발행 여부',
            'receipt_type' => '발급 구분',
            'receipt_number' => '현금영수증 번호',
            'terms_agree' => '결제 이용 약관, 개인정보 처리 동의',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
            'phone.required' => '휴대폰번호를 입력해주세요.',
            'payment_plan_ids.required' => '결제 항목을 선택해주세요.',
            'payment_plan_ids.min' => '결제 항목을 선택해주세요.',
            'payment_plan_ids.*.exists' => '결제 항목을 확인해주세요.',
            'membership_plan_id.exists' => '연회비 결제 항목을 확인해주세요.',
            'payment_method.required' => '결제수단을 선택해주세요.',
            'payment_method.in' => '결제수단을 확인해주세요.',
            'bank_depositor.required_if' => '입금자명을 입력해주세요.',
            'bank_deposit_date.required_if' => '입금 예정일을 선택해주세요.',
            'bank_deposit_date.date' => '입금 예정일 형식을 확인해주세요.',
            'receipt_type.required_if' => '현금영수증 발급 구분을 선택해주세요.',
            'receipt_number.required_if' => '현금영수증 번호를 입력해주세요.',
            'terms_agree.accepted' => '결제 이용 약관, 개인정보 처리 동의가 필요합니다.',
        ];
    }
}

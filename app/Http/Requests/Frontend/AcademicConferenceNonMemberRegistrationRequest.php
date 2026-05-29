<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicConferenceNonMemberRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! auth()->check() || auth()->user()?->role !== 'user';
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^01[016789]\d{7,8}$/'],
            'name_en' => ['required', 'string', 'max:100'],
            'license_no' => ['nullable', 'string', 'max:80'],
            'major_subject' => ['required', 'string', 'max:100'],
            'affiliated_hospital' => ['required', 'string', 'max:200'],
            'address_postcode' => ['required', 'string', 'max:20'],
            'address_base' => ['required', 'string', 'max:300'],
            'address_detail' => ['required', 'string', 'max:300'],
            'payment_plan_ids' => ['required', 'array', 'min:1'],
            'payment_plan_ids.*' => ['integer', 'exists:payment_plans,id'],
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
            'name' => '이름',
            'email' => '이메일',
            'phone' => '휴대폰번호',
            'name_en' => '이름(영문)',
            'license_no' => '면허번호',
            'major_subject' => '전공과목',
            'affiliated_hospital' => '소속병의원명',
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
}

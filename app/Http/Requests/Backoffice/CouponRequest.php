<?php

namespace App\Http\Requests\Backoffice;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $categories = $this->input('payment_categories');
        if (! is_array($categories)) {
            $categories = [];
        }
        $this->merge([
            'payment_categories' => $categories,
            'coupon_code' => $this->filled('coupon_code') ? strtoupper(trim((string) $this->input('coupon_code'))) : $this->input('coupon_code'),
        ]);
    }

    public function rules(): array
    {
        $uniqueCode = Rule::unique('coupons', 'coupon_code');
        $existing = $this->route('coupon');
        if ($existing instanceof Coupon) {
            $uniqueCode->ignore($existing->id);
        }

        return [
            'coupon_name' => ['required', 'string', 'max:200'],
            'coupon_code' => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9_-]+$/', $uniqueCode],
            'payment_categories' => ['required', 'array', 'min:1'],
            'payment_categories.*' => [Rule::in(['conference', 'membership', 'education'])],
            'discount_type' => ['required', Rule::in(['FIXED', 'RATE'])],
            'discount_amount' => [
                Rule::requiredIf((string) $this->input('discount_type') === 'FIXED'),
                'nullable',
                'numeric',
                'min:0',
            ],
            'discount_rate' => [
                Rule::requiredIf((string) $this->input('discount_type') === 'RATE'),
                'nullable',
                'numeric',
                'min:1',
                'max:100',
            ],
            'valid_from' => ['required', 'date'],
            'valid_to' => ['required', 'date', 'after_or_equal:valid_from'],
            'status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.unique' => '이미 사용 중인 쿠폰 코드입니다.',
            'coupon_code.regex' => '쿠폰 코드는 영문 대문자, 숫자, -, _ 만 사용할 수 있습니다.',
        ];
    }

    /**
     * 서비스 저장용 payload (검증된 필드 + discount_value)
     *
     * @return array<string, mixed>
     */
    public function payloadForService(): array
    {
        $data = $this->validated();
        $data['discount_value'] = $data['discount_type'] === 'FIXED'
            ? (float) $data['discount_amount']
            : (float) $data['discount_rate'];
        unset($data['discount_amount'], $data['discount_rate']);

        return $data;
    }
}

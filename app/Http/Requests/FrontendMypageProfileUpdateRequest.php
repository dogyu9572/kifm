<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrontendMypageProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'user';
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'password' => ['nullable', 'string', 'min:8', 'max:10', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'same:password'],
            'name' => ['required', 'string', 'max:20'],
            'name_en' => ['required', 'string', 'max:100'],
            'phone_number' => [
                'required',
                'string',
                'regex:/^01[016789]\d{7,8}$/',
                Rule::unique('users', 'phone_number')->ignore($userId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'job_type' => ['required', Rule::in(['specialist', 'resident', 'public_doctor', 'military_doctor', 'nurse', 'other'])],
            'license_number' => ['required', 'string', 'max:80'],
            'specialty' => ['required', 'string', 'max:120'],
            'workplace_name' => ['required', 'string', 'max:200'],
            'workplace_phone' => ['required', 'string', 'max:40'],
            'workplace_zipcode' => ['nullable', 'string', 'max:20'],
            'workplace_address' => ['nullable', 'string'],
            'workplace_address_detail' => ['nullable', 'string'],
            'graduate_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'school_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $license = trim((string) $this->input('license_number', ''));
            if ($license === '') {
                return;
            }
            $userId = $this->user()?->id;
            $exists = User::query()
                ->where('role', 'user')
                ->whereNull('withdrawn_at')
                ->where('license_number', $license)
                ->when($userId, fn ($q) => $q->where('id', '!=', $userId))
                ->exists();
            if ($exists) {
                $validator->errors()->add('license_number', '이미 등록된 의사면허번호입니다.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $gy = $this->input('graduate_year');
        if ($gy === '' || $gy === null) {
            $this->merge(['graduate_year' => null]);
        }
        $this->merge([
            'phone_number' => User::normalizePhone((string) $this->input('phone_number', '')),
        ]);
    }
}

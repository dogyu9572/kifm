<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class AcademicConferenceNonMemberAbstractLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! auth()->check() || auth()->user()?->role !== 'user';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/\D+/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['required', 'string', 'regex:/^01[016789]\d{7,8}$/'],
            'lookup_password' => ['required', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '이름',
            'email' => '이메일',
            'phone' => '휴대폰 번호',
            'lookup_password' => '초록 접수 비밀번호',
        ];
    }
}

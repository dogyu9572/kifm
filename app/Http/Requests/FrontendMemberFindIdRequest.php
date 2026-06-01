<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class FrontendMemberFindIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'phone_number' => ['required', 'string', 'regex:/^01[016789]\d{7,8}$/'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone_number' => User::normalizePhone((string) $this->input('phone_number', '')),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => '이름을 입력해주세요.',
            'phone_number.required' => '휴대폰 번호를 입력해주세요.',
            'phone_number.regex' => '휴대폰 번호 형식을 확인해주세요. (010 등 10~11자리)',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
        ];
    }
}

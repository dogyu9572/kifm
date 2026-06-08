<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class FrontendMemberResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reset_token' => ['required', 'string', 'size:64'],
            'password' => ['required', 'string', 'min:8', 'max:10', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $password = (string) $this->input('password', '');
            if ($password === '') {
                return;
            }

            if (! preg_match('/^[A-Za-z0-9!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\\\|`~]+$/', $password)) {
                $validator->errors()->add('password', '비밀번호는 영문, 숫자, 특수문자만 사용할 수 있습니다.');
                return;
            }

            if (! preg_match('/[A-Za-z]/', $password) || ! preg_match('/\d/', $password)) {
                $validator->errors()->add('password', '비밀번호는 8~10자의 영문과 숫자를 반드시 포함해야 합니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'reset_token.required' => '비밀번호 재설정 인증이 만료되었습니다. 다시 진행해주세요.',
            'reset_token.size' => '비밀번호 재설정 인증이 올바르지 않습니다. 다시 진행해주세요.',
            'password.required' => '새 비밀번호를 입력해주세요.',
            'password.min' => '비밀번호는 8~10자로 입력해주세요.',
            'password.max' => '비밀번호는 8~10자로 입력해주세요.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password_confirmation.required' => '새 비밀번호 확인을 입력해주세요.',
            'password_confirmation.same' => '비밀번호 확인이 일치하지 않습니다.',
        ];
    }

    protected function failedValidation(ValidationContract $validator): void
    {
        $this->session()->flash('alert', $validator->errors()->first());

        throw (new ValidationException($validator))->redirectTo($this->getRedirectUrl());
    }
}

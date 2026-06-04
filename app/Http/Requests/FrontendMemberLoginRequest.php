<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class FrontendMemberLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'login_id.required' => '아이디를 입력해주세요.',
            'password.required' => '비밀번호를 입력해주세요.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('alert', $validator->errors()->first());

        throw (new ValidationException($validator))
            ->redirectTo($this->getRedirectUrl());
    }
}

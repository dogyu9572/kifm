<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class FrontendMypageSecessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'user';
    }

    public function rules(): array
    {
        return [
            'secession_agreed' => ['accepted'],
            'password' => ['required', 'string'],
            'withdrawal_reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $user = $this->user();
            $password = (string) $this->input('password', '');

            if ($user === null || $password === '') {
                return;
            }

            if (! Hash::check($password, (string) $user->password)) {
                $validator->errors()->add('password', '비밀번호가 일치하지 않습니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'secession_agreed.accepted' => '회원탈퇴 안내 확인에 동의해주세요.',
            'password.required' => '비밀번호를 입력해주세요.',
            'withdrawal_reason.required' => '탈퇴 사유를 입력해주세요.',
            'withdrawal_reason.max' => '탈퇴 사유는 1000자 이내로 입력해주세요.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('alert', $validator->errors()->first());

        throw (new ValidationException($validator))
            ->redirectTo($this->getRedirectUrl());
    }
}

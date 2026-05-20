<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontendSubcommitteeDiscussionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'captcha' => trim((string) $this->input('captcha')),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'captcha' => [
                'required',
                'string',
                'max:6',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $expected = (string) $this->session()->get('captcha.discussion', '');
                    $actual = strtolower((string) $value);

                    if ($expected === '' || ! hash_equals($expected, $actual)) {
                        $fail('자동등록방지 문자를 정확히 입력해 주세요.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '토론 주제를 입력해 주세요.',
            'title.max' => '토론 주제는 최대 255자까지 입력 가능합니다.',
            'captcha.required' => '자동등록방지 문자를 입력해 주세요.',
            'captcha.max' => '자동등록방지 문자는 최대 6자까지 입력 가능합니다.',
        ];
    }
}

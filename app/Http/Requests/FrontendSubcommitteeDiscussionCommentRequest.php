<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontendSubcommitteeDiscussionCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'content' => trim((string) $this->input('content')),
        ]);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:65535'],
            'attach_file' => ['nullable', 'file', 'max:10240'],
            'attach_image' => ['nullable', 'image', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '답글 내용을 작성해주세요.',
        ];
    }
}

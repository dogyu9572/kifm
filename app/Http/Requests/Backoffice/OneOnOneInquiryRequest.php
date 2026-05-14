<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OneOnOneInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'answer_status' => ['required', Rule::in(['PENDING', 'DONE'])],
            'answered_at' => ['nullable', 'date'],
            'answer_content' => ['nullable', 'string', 'required_if:answer_status,DONE'],
            'answer_attachments' => ['nullable', 'array', 'max:5'],
            'answer_attachments.*' => ['nullable', 'file', 'max:10240'],
            'delete_answer_attachment_indexes' => ['nullable', 'array'],
            'delete_answer_attachment_indexes.*' => ['integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'answer_status.required' => '답변 상태를 선택해주세요.',
            'answer_content.required_if' => '답변 완료 시 답변 내용을 입력해주세요.',
            'answer_attachments.max' => '답변 첨부는 최대 5개까지 업로드할 수 있습니다.',
            'answer_attachments.*.max' => '답변 첨부 파일은 각 10MB 이하만 업로드할 수 있습니다.',
        ];
    }
}

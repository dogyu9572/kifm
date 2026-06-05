<?php

namespace App\Http\Requests\Frontend;

use App\Services\Frontend\PublicAcademicConferenceAbstractService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicConferenceAbstractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->role === 'user';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'author_mobile' => preg_replace('/\D+/', '', (string) $this->input('author_mobile')),
            'attachments' => is_array($this->file('attachments')) ? $this->file('attachments') : [],
        ]);
    }

    public function rules(): array
    {
        return [
            'author_email' => ['required', 'email', 'max:200'],
            'author_mobile' => ['required', 'string', 'regex:/^01[016789]\d{7,8}$/'],
            'title' => ['required', 'string', 'max:500'],
            'presentation_type' => ['required', Rule::in(array_keys((new PublicAcademicConferenceAbstractService())->presentationTypeLabels()))],
            'academic_event_field_id' => ['required', 'integer', 'exists:academic_event_fields,id'],
            'note' => ['nullable', 'string'],
            'attachments' => ['required', 'array', 'min:1', 'max:' . PublicAcademicConferenceAbstractService::MAX_FILES],
            'attachments.*' => ['file', 'max:' . PublicAcademicConferenceAbstractService::MAX_FILE_SIZE_KB],
        ];
    }

    public function attributes(): array
    {
        return [
            'author_email' => '이메일',
            'author_mobile' => '휴대폰번호',
            'title' => '초록 제목',
            'presentation_type' => '발표구분',
            'academic_event_field_id' => '발표 분야',
            'attachments' => '초록 양식 업로드',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute 항목을 확인해주세요.',
            'author_email.required' => '이메일을 입력해주세요.',
            'author_mobile.required' => '휴대폰번호를 입력해주세요.',
            'title.required' => '초록 제목을 입력해주세요.',
            'presentation_type.required' => '발표구분을 선택해주세요.',
            'academic_event_field_id.required' => '발표 분야를 선택해주세요.',
            'attachments.required' => '초록 양식을 업로드해주세요.',
            'attachments.min' => '초록 양식을 업로드해주세요.',
            'email' => ':attribute 형식이 올바르지 않습니다.',
            'regex' => ':attribute 형식이 올바르지 않습니다.',
            'in' => ':attribute 항목을 올바르게 선택해주세요.',
            'integer' => ':attribute 항목을 올바르게 선택해주세요.',
            'exists' => ':attribute 항목을 올바르게 선택해주세요.',
            'array' => ':attribute 항목을 올바르게 등록해주세요.',
            'min' => ':attribute 항목을 확인해주세요.',
            'max' => ':attribute은(는) 허용 범위를 초과할 수 없습니다.',
            'file' => ':attribute은(는) 파일만 등록할 수 있습니다.',
        ];
    }
}

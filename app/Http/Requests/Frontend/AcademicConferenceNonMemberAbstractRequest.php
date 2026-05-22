<?php

namespace App\Http\Requests\Frontend;

use App\Services\Frontend\PublicAcademicConferenceAbstractService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicConferenceNonMemberAbstractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! auth()->check() || auth()->user()?->role !== 'user';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'author_phone' => preg_replace('/\D+/', '', (string) $this->input('author_phone')),
            'author_mobile' => preg_replace('/\D+/', '', (string) $this->input('author_mobile')),
            'attachments' => is_array($this->file('attachments')) ? $this->file('attachments') : [],
        ]);
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:100'],
            'author_name_en' => ['nullable', 'string', 'max:100'],
            'author_phone' => ['nullable', 'string', 'max:30'],
            'author_mobile' => ['required', 'string', 'regex:/^01[016789]\d{7,8}$/'],
            'author_email' => ['required', 'email', 'max:200'],
            'title' => ['required', 'string', 'max:500'],
            'presentation_type' => ['required', Rule::in(array_keys((new PublicAcademicConferenceAbstractService())->presentationTypeLabels()))],
            'academic_event_field_id' => ['nullable', 'integer', 'exists:academic_event_fields,id'],
            'note' => ['nullable', 'string'],
            'lookup_password' => ['required', 'string', 'min:4', 'max:100'],
            'attachments' => ['nullable', 'array', 'max:' . PublicAcademicConferenceAbstractService::MAX_FILES],
            'attachments.*' => ['file', 'max:' . PublicAcademicConferenceAbstractService::MAX_FILE_SIZE_KB],
        ];
    }

    public function attributes(): array
    {
        return [
            'author_name' => '이름(국문)',
            'author_name_en' => '이름(영문)',
            'author_phone' => '전화번호',
            'author_mobile' => '휴대폰번호',
            'author_email' => '이메일',
            'title' => '초록 제목',
            'presentation_type' => '발표구분',
            'academic_event_field_id' => '발표 분야',
            'lookup_password' => '초록 접수 비밀번호',
            'attachments' => '초록 양식 업로드',
        ];
    }
}

<?php

namespace App\Http\Requests\Frontend;

use App\Services\Frontend\PublicAcademicConferenceAbstractService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicConferenceAbstractUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'author_phone' => preg_replace('/\D+/', '', (string) $this->input('author_phone')),
            'author_mobile' => preg_replace('/\D+/', '', (string) $this->input('author_mobile')),
            'remove_file_ids' => is_array($this->input('remove_file_ids')) ? $this->input('remove_file_ids') : [],
            'attachments' => is_array($this->file('attachments')) ? $this->file('attachments') : [],
        ]);
    }

    public function rules(): array
    {
        return [
            'author_name' => ['nullable', 'string', 'max:100'],
            'author_name_en' => ['nullable', 'string', 'max:100'],
            'author_phone' => ['nullable', 'string', 'max:30'],
            'author_mobile' => ['nullable', 'string', 'regex:/^01[016789]\d{7,8}$/'],
            'author_email' => ['nullable', 'email', 'max:200'],
            'title' => ['required', 'string', 'max:500'],
            'presentation_type' => ['required', Rule::in(array_keys((new PublicAcademicConferenceAbstractService())->presentationTypeLabels()))],
            'academic_event_field_id' => ['nullable', 'integer', 'exists:academic_event_fields,id'],
            'note' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:' . PublicAcademicConferenceAbstractService::MAX_FILE_SIZE_KB],
            'remove_file_ids' => ['nullable', 'array'],
            'remove_file_ids.*' => ['integer'],
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
            'attachments' => '초록 양식 업로드',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute 항목을 확인해주세요.',
            'title.required' => '초록 제목을 입력해주세요.',
            'presentation_type.required' => '발표구분을 선택해주세요.',
            'email' => ':attribute 형식이 올바르지 않습니다.',
            'regex' => ':attribute 형식이 올바르지 않습니다.',
            'in' => ':attribute 항목을 올바르게 선택해주세요.',
            'integer' => ':attribute 항목을 올바르게 선택해주세요.',
            'exists' => ':attribute 항목을 올바르게 선택해주세요.',
            'array' => ':attribute 항목을 올바르게 등록해주세요.',
            'max' => ':attribute은(는) 허용 범위를 초과할 수 없습니다.',
            'file' => ':attribute은(는) 파일만 등록할 수 있습니다.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $abstract = $this->route('abstract');
            if (! $abstract) {
                return;
            }

            $validFileIds = $abstract->files()->pluck('id')->all();
            $removeIds = array_values(array_unique(array_map('intval', (array) $this->input('remove_file_ids', []))));
            $removeIds = array_values(array_intersect($removeIds, $validFileIds));
            $newFiles = array_filter((array) $this->file('attachments', []), static fn ($file) => $file && $file->isValid());
            $after = $abstract->files()->count() - count($removeIds) + count($newFiles);
            if ($after > PublicAcademicConferenceAbstractService::MAX_FILES) {
                $validator->errors()->add('attachments', '첨부파일은 최대 5개까지 등록할 수 있습니다.');
            }
        });
    }
}

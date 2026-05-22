<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\Backoffice\AcademicEventAbstractService;

class StoreAcademicEventAbstractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = (int) $this->input('academic_event_id');

        return [
            'academic_event_id' => ['required', 'integer', 'exists:academic_events,id'],
            'status' => ['required', Rule::in(['receipt', 'confirmed'])],
            'file_receipt_status' => ['required', Rule::in(['received', 'not_received', 'not_submitted'])],
            'registered_by' => ['required', Rule::in(['user', 'admin'])],
            'member_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::requiredIf($this->input('registered_by') === 'admin'),
            ],
            'author_name' => ['required', 'string', 'max:100'],
            'author_name_en' => ['nullable', 'string', 'max:100'],
            'author_phone' => ['nullable', 'string', 'max:30'],
            'author_mobile' => ['nullable', 'string', 'max:30'],
            'author_email' => ['nullable', 'email', 'max:200'],
            'title' => ['required', 'string', 'max:500'],
            'presentation_type' => ['required', Rule::in(array_keys(AcademicEventAbstractService::presentationTypeLabels()))],
            'academic_event_field_id' => [
                'nullable',
                'integer',
                Rule::exists('academic_event_fields', 'id')->where(
                    static fn ($q) => $q->where('academic_event_id', $eventId)
                ),
            ],
            'note' => ['nullable', 'string'],
            'submitted_at' => ['required', 'date'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $newFiles = array_filter((array) $this->file('attachments', []), static fn ($f) => $f && $f->isValid());
            if (count($newFiles) > \App\Services\Backoffice\AcademicEventAbstractService::MAX_FILES_PER_ABSTRACT) {
                $validator->errors()->add('attachments', '첨부파일은 최대 5개까지 등록할 수 있습니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'member_id.required_if' => '관리자 등록인 경우 회원을 선택해주세요.',
        ];
    }
}

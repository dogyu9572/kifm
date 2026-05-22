<?php

namespace App\Http\Requests\Backoffice;

use App\Models\AcademicEventAbstract;
use App\Services\Backoffice\AcademicEventAbstractService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicEventAbstractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var AcademicEventAbstract $abstract */
        $abstract = $this->route('academic_event_abstract');
        $eventId = (int) $this->input('academic_event_id', $abstract->academic_event_id);

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
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
            'remove_file_ids' => ['nullable', 'array'],
            'remove_file_ids.*' => ['integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var AcademicEventAbstract|null $abstract */
            $abstract = $this->route('academic_event_abstract');
            if (! $abstract) {
                return;
            }
            $validFileIds = $abstract->files()->pluck('id')->all();
            $removeIds = array_values(array_unique(array_map('intval', (array) $this->input('remove_file_ids', []))));
            $removeIds = array_values(array_intersect($removeIds, $validFileIds));
            $newFiles = array_filter((array) $this->file('attachments', []), static fn ($f) => $f && $f->isValid());
            $after = $abstract->files()->count() - count($removeIds) + count($newFiles);
            if ($after > \App\Services\Backoffice\AcademicEventAbstractService::MAX_FILES_PER_ABSTRACT) {
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

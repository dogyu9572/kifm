<?php

namespace App\Http\Requests\Backoffice;

use App\Models\AcademicEvent;
use App\Models\AcademicEventAbstract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('academic_event');
        $eventId = $event instanceof AcademicEvent ? $event->id : null;
        $isUpdate = $eventId !== null;

        $folderRules = ['string', 'max:120', 'regex:/^[a-zA-Z0-9_-]+$/'];
        if (! $isUpdate) {
            $folderRules = array_merge(['required'], $folderRules, [
                Rule::unique('academic_events', 'folder_name'),
            ]);
        } else {
            $folderRules = array_merge(['prohibited'], $folderRules);
        }

        return [
            'year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'season' => ['required', 'string', Rule::in(array_keys(\App\Services\Backoffice\AcademicEventService::seasonLabels()))],
            'folder_name' => $folderRules,
            'title' => ['required', 'string', 'max:255'],
            'main_title_1' => ['nullable', 'string', 'max:255'],
            'main_title_2' => ['nullable', 'string', 'max:255'],
            'event_material_description' => ['nullable', 'string', 'max:2000'],
            'event_type' => ['required', Rule::in(['offline', 'online'])],
            'online_url' => ['nullable', 'required_if:event_type,online', 'string', 'max:500'],
            'is_public' => ['required', Rule::in(['Y', 'N'])],
            'main_exposure' => ['nullable', Rule::in(['Y', 'N'])],
            'venue' => ['nullable', 'required_if:event_type,offline', 'string', 'max:2000'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'start_time_omit' => ['sometimes', 'boolean'],
            'end_time_omit' => ['sometimes', 'boolean'],
            'greeting_title' => ['nullable', 'string', 'max:200'],
            'greeting_content' => ['nullable', 'string'],
            'committee_content' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'address_detail' => ['nullable', 'string', 'max:300'],
            'address_lat' => ['nullable', 'numeric'],
            'address_lng' => ['nullable', 'numeric'],
            'walking_guide' => ['nullable', 'string'],
            'shuttle_guide' => ['nullable', 'string'],
            'pre_reg_start' => ['nullable', 'date'],
            'pre_reg_end' => ['nullable', 'date', 'after_or_equal:pre_reg_start'],
            'onsite_reg_start' => ['nullable', 'date'],
            'onsite_reg_end' => ['nullable', 'date', 'after_or_equal:onsite_reg_start'],
            'onsite_reg_allowed' => ['nullable', Rule::in(['allowed', 'disallowed'])],
            'pre_reg_guide' => ['nullable', 'string'],
            'reg_fee_guide' => ['nullable', 'string'],
            'cert_doc_guide' => ['nullable', 'string'],
            'reg_info_guide' => ['nullable', 'string'],
            'abstract_start' => ['nullable', 'date'],
            'abstract_end' => ['nullable', 'date', 'after_or_equal:abstract_start'],
            'abstract_revision_end' => ['nullable', 'date'],
            'abstract_judging_end' => ['nullable', 'date'],
            'abstract_result_date' => ['nullable', 'date'],
            'presentation_types' => ['nullable', 'array'],
            'presentation_types.*' => ['string', Rule::in(array_keys(\App\Services\Backoffice\AcademicEventService::presentationTypeLabels()))],
            'submission_guide' => ['nullable', 'string'],
            'abstract_notes' => ['nullable', 'string'],
            'venue_floors' => ['nullable', 'array'],
            'venue_floors.*.floor_name' => ['nullable', 'string', 'max:100'],
            'venue_floors.*.file_path' => ['nullable', 'string', 'max:500'],
            'venue_floors.*.sort_order' => ['nullable', 'integer'],
            'abstract_fields' => ['nullable', 'array'],
            'abstract_fields.*.name' => ['nullable', 'string', 'max:150'],
            'abstract_fields.*.sort_order' => ['nullable', 'integer'],
            'sponsors' => ['nullable', 'array'],
            'sponsors.*.name' => ['nullable', 'string', 'max:200'],
            'sponsors.*.level' => ['nullable', Rule::in(array_keys(\App\Services\Backoffice\AcademicEventService::sponsorLevelLabels()))],
            'sponsors.*.academic_sponsor_master_id' => ['nullable', 'integer', 'exists:academic_sponsor_masters,id'],
            'sponsors.*.logo_path' => ['nullable', 'string', 'max:500'],
            'sponsors.*.sort_order' => ['nullable', 'integer'],
            'main_sponsor_slots' => ['nullable', 'array'],
            'main_sponsor_slots.*.active' => ['nullable', 'in:0,1'],
            'main_sponsor_slots.*.placement' => ['nullable', Rule::in(array_keys(\App\Services\Backoffice\AcademicEventService::mainPlacementLabels()))],
            'main_sponsor_slots.*.sponsor_index' => ['nullable', 'integer', 'min:0'],
            'main_sponsor_slots.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'speakers' => ['nullable', 'array'],
            'speakers.*.source' => ['nullable', Rule::in(['manual', 'member', 'abstract'])],
            'speakers.*.member_id' => ['nullable', 'integer', 'exists:users,id'],
            'speakers.*.academic_event_abstract_id' => ['nullable', 'integer', 'exists:academic_event_abstracts,id'],
            'speakers.*.name' => ['nullable', 'string', 'max:200'],
            'speakers.*.affiliation' => ['nullable', 'string', 'max:255'],
            'speakers.*.position' => ['nullable', 'string', 'max:100'],
            'speakers.*.abstract_title' => ['nullable', 'string', 'max:500'],
            'speakers.*.bio' => ['nullable', 'string'],
            'speakers.*.image_path' => ['nullable', 'string', 'max:500'],
            'speakers.*.sort_order' => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $event = $this->route('academic_event');
            $eventId = $event instanceof AcademicEvent ? $event->id : null;
            $speakers = $this->input('speakers', []);
            if (! is_array($speakers)) {
                return;
            }
            foreach ($speakers as $i => $row) {
                if (! is_array($row)) {
                    continue;
                }
                $rawAbstractId = $row['academic_event_abstract_id'] ?? null;
                if ($rawAbstractId === null || $rawAbstractId === '') {
                    continue;
                }
                $abstractId = (int) $rawAbstractId;
                if ($abstractId <= 0) {
                    continue;
                }
                if ($eventId === null) {
                    $validator->errors()->add(
                        "speakers.{$i}.academic_event_abstract_id",
                        '초록 연동은 학술행사 저장 후에만 가능합니다.'
                    );

                    continue;
                }
                $abstract = AcademicEventAbstract::query()->find($abstractId);
                if (! $abstract) {
                    continue;
                }
                if ((int) $abstract->academic_event_id !== (int) $eventId) {
                    $validator->errors()->add(
                        "speakers.{$i}.academic_event_abstract_id",
                        '선택한 초록이 이 학술행사에 속하지 않습니다.'
                    );
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->mergeNullableDates([
            'pre_reg_start', 'pre_reg_end', 'onsite_reg_start', 'onsite_reg_end',
            'abstract_start', 'abstract_end', 'abstract_revision_end', 'abstract_judging_end', 'abstract_result_date',
        ]);
        $this->merge([
            'start_time_omit' => $this->boolean('start_time_omit'),
            'end_time_omit' => $this->boolean('end_time_omit'),
            'presentation_types' => $this->input('presentation_types', []),
        ]);
    }

    /** @param list<string> $keys */
    protected function mergeNullableDates(array $keys): void
    {
        $m = $this->all();
        foreach ($keys as $k) {
            if (array_key_exists($k, $m) && $m[$k] === '') {
                $m[$k] = null;
            }
        }
        $this->replace($m);
    }

    public function attributes(): array
    {
        return [
            'year' => '연도',
            'season' => '시즌',
            'folder_name' => '폴더명',
            'title' => '행사명',
            'main_title_1' => '타이틀 1',
            'main_title_2' => '타이틀 2',
            'event_material_description' => '행사 자료 설명',
            'event_type' => '행사 유형',
            'online_url' => '온라인 접속 URL',
            'is_public' => '공개 여부',
            'main_exposure' => '메인 노출',
            'venue' => '개최장소',
            'start_at' => '시작 일시',
            'end_at' => '종료 일시',
            'greeting_title' => '인사말 제목',
            'greeting_content' => '인사말 내용',
            'committee_content' => '위원회 내용',
            'address' => '주소',
            'address_detail' => '상세 주소',
            'address_lat' => '위도',
            'address_lng' => '경도',
            'walking_guide' => '도보 안내',
            'shuttle_guide' => '셔틀 안내',
            'pre_reg_start' => '사전등록 시작',
            'pre_reg_end' => '사전등록 종료',
            'onsite_reg_start' => '현장등록 시작',
            'onsite_reg_end' => '현장등록 종료',
            'onsite_reg_allowed' => '현장등록 허용',
            'pre_reg_guide' => '사전등록 안내',
            'reg_fee_guide' => '등록비 안내',
            'cert_doc_guide' => '증빙서류 안내',
            'reg_info_guide' => '등록 안내',
            'abstract_start' => '초록 접수 시작',
            'abstract_end' => '초록 접수 종료',
            'abstract_revision_end' => '초록 수정 마감',
            'abstract_judging_end' => '심사 마감',
            'abstract_result_date' => '결과 발표일',
            'presentation_types' => '발표 구분',
            'submission_guide' => '투고 안내',
            'abstract_notes' => '초록 비고',
        ];
    }

    public function messages(): array
    {
        return [
            'event_type.required' => '행사 유형을 선택해 주세요.',
            'event_type.in' => '행사 유형 값이 올바르지 않습니다.',
            'venue.required_if' => '오프라인 행사일 때는 개최장소를 입력해 주세요.',
            'online_url.required_if' => '온라인 행사일 때는 온라인 접속 URL을 입력해 주세요.',
            'folder_name.required' => '폴더명을 입력해 주세요.',
            'folder_name.unique' => '이미 사용 중인 폴더명입니다.',
            'folder_name.regex' => '폴더명은 영문·숫자·하이픈·밑줄만 사용할 수 있습니다.',
            'title.required' => '행사명을 입력해 주세요.',
            'year.required' => '연도를 선택해 주세요.',
            'season.required' => '시즌을 선택해 주세요.',
            'is_public.required' => '공개 여부를 선택해 주세요.',
            'is_public.in' => '공개 여부 값이 올바르지 않습니다.',
            'end_at.after_or_equal' => '종료 일시는 시작 일시 이후여야 합니다.',
            'pre_reg_end.after_or_equal' => '사전등록 종료는 시작 이후여야 합니다.',
            'onsite_reg_end.after_or_equal' => '현장등록 종료는 시작 이후여야 합니다.',
            'abstract_end.after_or_equal' => '초록 접수 종료는 시작 이후여야 합니다.',
        ];
    }
}

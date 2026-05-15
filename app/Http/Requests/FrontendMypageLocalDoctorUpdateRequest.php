<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrontendMypageLocalDoctorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'delete_photo' => $this->boolean('delete_photo'),
            'category_ids' => $this->input('category_ids', []),
            'functional_tests_selected' => $this->input('functional_tests_selected', []),
            'treatment_areas_selected' => $this->input('treatment_areas_selected', []),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $functionalIds = collect(config('local_doctor_form_options.functional_tests', []))->pluck('id')->all();
        $treatmentIds = collect(config('local_doctor_form_options.treatment_areas', []))->pluck('id')->all();

        return [
            'delete_photo' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'doctor_name' => ['required', 'string', 'max:100'],
            'license_no' => ['required', 'string', 'max:50'],
            'introduction' => ['nullable', 'string'],
            'hospital_name' => ['required', 'string', 'max:200'],
            'sido' => ['required', 'string', 'max:80'],
            'sigungu' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            'address_detail' => ['nullable', 'string', 'max:200'],
            'homepage' => ['nullable', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:80'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:doctor_categories,id'],
            'functional_tests_selected' => ['nullable', 'array'],
            'functional_tests_selected.*' => ['string', Rule::in($functionalIds)],
            'treatment_areas_selected' => ['nullable', 'array'],
            'treatment_areas_selected.*' => ['string', Rule::in($treatmentIds)],
            'other_symptoms' => ['nullable', 'string'],
            'diseases_text' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'doctor_name' => '선생님 성함',
            'license_no' => '면허번호',
            'hospital_name' => '병원명',
            'sido' => '시/도',
            'sigungu' => '시/군/구',
            'address' => '주소',
            'phone' => '전화번호',
            'category_ids' => '진료 카테고리',
        ];
    }
}

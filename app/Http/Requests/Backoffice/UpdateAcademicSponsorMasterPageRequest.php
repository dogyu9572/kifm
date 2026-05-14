<?php

namespace App\Http\Requests\Backoffice;

use App\Models\AcademicSponsorMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicSponsorMasterPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'logo' => ['nullable', 'image', 'max:5120'],
            'existing_logo' => ['nullable', 'string', 'max:500'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => '로고는 이미지 파일만 등록할 수 있습니다.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var AcademicSponsorMaster|null $master */
            $master = $this->route('academic_sponsor_master');
            $hasNew = (bool) $this->file('logo');
            $remove = (bool) $this->input('remove_logo');

            if ($remove && ! $hasNew) {
                $validator->errors()->add('logo', '로고를 삭제한 경우 새 이미지를 함께 등록해주세요.');
            }
            if (! $remove && ! $hasNew && $master && ! $master->logo_path) {
                $validator->errors()->add('logo', '스폰서 로고 이미지를 업로드해주세요.');
            }
        });
    }
}

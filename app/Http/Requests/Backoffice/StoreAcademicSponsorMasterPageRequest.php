<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicSponsorMasterPageRequest extends FormRequest
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
            'logo' => ['required', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => '스폰서 로고 이미지를 업로드해주세요.',
            'logo.image' => '로고는 이미지 파일만 등록할 수 있습니다.',
        ];
    }
}

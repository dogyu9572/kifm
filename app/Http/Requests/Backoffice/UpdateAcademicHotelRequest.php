<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('homepage_url') && trim((string) $this->input('homepage_url')) === '') {
            $this->merge(['homepage_url' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'phone' => ['required', 'string', 'max:30'],
            'distance' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'address_detail' => ['nullable', 'string', 'max:255'],
            'homepage_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'existing_image' => ['nullable', 'string', 'max:500'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => '숙소 이미지는 JPG 또는 PNG 파일만 등록할 수 있습니다.',
        ];
    }
}

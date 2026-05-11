<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocietyExecutiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_no' => ['required', 'integer', 'in:1,2,3'],
            'position' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'organization' => ['required', 'string', 'max:200'],
            'email' => ['nullable', 'email:rfc,dns', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'existing_photo' => ['nullable', 'string', 'max:255'],
            'remove_photo' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'group_no.required' => '그룹 선택은 필수입니다.',
            'group_no.in' => '그룹 값이 올바르지 않습니다.',
            'position.required' => '직책은 필수 입력 항목입니다.',
            'name.required' => '이름은 필수 입력 항목입니다.',
            'organization.required' => '소속은 필수 입력 항목입니다.',
            'email.email' => '이메일 형식이 올바르지 않습니다.',
            'sort_order.integer' => '정렬 순서는 숫자만 입력 가능합니다.',
            'sort_order.min' => '정렬 순서는 1 이상이어야 합니다.',
            'photo.image' => '사진은 이미지 파일만 업로드할 수 있습니다.',
            'photo.max' => '사진 파일은 5MB 이하만 업로드할 수 있습니다.',
            'is_active.required' => '사용 여부를 선택해주세요.',
        ];
    }
}

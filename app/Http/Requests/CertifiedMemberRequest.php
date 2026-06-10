<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertifiedMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:users,id'],
            'validity_start_date' => ['required', 'date'],
            'validity_end_date' => ['required', 'date', 'after_or_equal:validity_start_date'],
            'acquired_date' => ['required', 'date'],
            'acquired_validity_start' => ['required', 'date'],
            'acquired_validity_end' => ['required', 'date', 'after_or_equal:acquired_validity_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute을(를) 입력해주세요.',
            'integer' => ':attribute은(는) 숫자로 입력해주세요.',
            'date' => ':attribute은(는) 올바른 날짜로 입력해주세요.',
            'exists' => '선택한 :attribute 정보를 찾을 수 없습니다.',
            'validity_end_date.after_or_equal' => '인정의 종료일은 인정의 시작일 이후여야 합니다.',
            'acquired_validity_end.after_or_equal' => '취득 인정의 종료일은 취득 인정의 시작일 이후여야 합니다.',
        ];
    }

    public function attributes(): array
    {
        return [
            'member_id' => '회원',
            'validity_start_date' => '인정의 시작일',
            'validity_end_date' => '인정의 종료일',
            'acquired_date' => '취득일',
            'acquired_validity_start' => '취득 인정의 시작일',
            'acquired_validity_end' => '취득 인정의 종료일',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\CommunityCommittee;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrontendMemberRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $committeeIds = CommunityCommittee::query()
            ->pluck('id')
            ->map(static fn ($id): string => (string) $id)
            ->all();

        return [
            'login_id' => ['required', 'string', 'max:80', Rule::unique('users', 'login_id')],
            'password' => ['required', 'string', 'min:8', 'max:10', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'name' => ['required', 'string', 'max:20'],
            'name_en' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'regex:/^01[016789]\d{7,8}$/', Rule::unique('users', 'phone_number')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'job_type' => ['required', Rule::in(['specialist', 'resident', 'public_doctor', 'military_doctor', 'nurse', 'other'])],
            'license_number' => ['required', 'string', 'max:80'],
            'specialist_number' => ['required', 'string', 'max:80'],
            'specialty' => ['required', 'string', 'max:120'],
            'workplace_name' => ['required', 'string', 'max:200'],
            'workplace_phone' => ['required', 'string', 'max:40'],
            'workplace_zipcode' => ['nullable', 'string', 'max:20'],
            'workplace_address' => ['nullable', 'string'],
            'workplace_address_detail' => ['nullable', 'string'],
            'graduate_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'committee_codes' => ['nullable', 'array', 'max:3'],
            'committee_codes.*' => ['string', Rule::in($committeeIds)],
            'privacy_agreed' => ['accepted'],
            'terms_agreed' => ['accepted'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $license = trim((string) $this->input('license_number', ''));
            if ($license === '') {
                return;
            }
            $exists = User::query()
                ->where('role', 'user')
                ->whereNull('withdrawn_at')
                ->where('license_number', $license)
                ->exists();
            if ($exists) {
                $validator->errors()->add('license_number', '이미 등록된 의사면허번호입니다.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('committee_codes')) {
            $this->merge(['committee_codes' => []]);
        }
        $gy = $this->input('graduate_year');
        if ($gy === '' || $gy === null) {
            $this->merge(['graduate_year' => null]);
        }
        $this->merge([
            'phone_number' => User::normalizePhone((string) $this->input('phone_number', '')),
        ]);
    }

    public function messages(): array
    {
        return [
            'login_id.required' => '아이디를 입력해주세요.',
            'login_id.unique' => '이미 사용 중인 아이디입니다.',
            'password.required' => '비밀번호를 입력해주세요.',
            'password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'password.max' => '비밀번호는 최대 10자까지 입력 가능합니다.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password_confirmation.required' => '비밀번호 확인을 입력해주세요.',
            'name.required' => '한글 이름을 입력해주세요.',
            'name_en.required' => '영문 이름을 입력해주세요.',
            'phone_number.required' => '휴대폰 번호를 입력해주세요.',
            'phone_number.regex' => '휴대폰 번호 형식을 확인해주세요. (010 등 10~11자리)',
            'phone_number.unique' => '이미 사용 중인 휴대폰번호입니다.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
            'email.unique' => '이미 사용 중인 이메일입니다.',
            'job_type.required' => '구분을 선택해주세요.',
            'license_number.required' => '의사면허번호를 입력해주세요.',
            'specialist_number.required' => '전문의번호를 입력해주세요.',
            'specialty.required' => '전문과를 입력해주세요.',
            'workplace_name.required' => '직장명을 입력해주세요.',
            'workplace_phone.required' => '직장전화를 입력해주세요.',
            'committee_codes.max' => '위원회 참가 신청은 최대 3개까지 선택 가능합니다.',
            'privacy_agreed.accepted' => '개인정보 수집 및 이용에 동의해주세요.',
            'terms_agreed.accepted' => '이용약관에 동의해주세요.',
        ];
    }
}

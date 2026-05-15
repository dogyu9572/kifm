<?php

namespace App\Http\Requests;

use App\Models\CommunityCommittee;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BackofficeMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $member = $this->route('user');
        $memberId = $member instanceof User ? $member->id : $member;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $committeeCodes = CommunityCommittee::query()
            ->pluck('id')
            ->map(static fn ($id): string => (string) $id)
            ->all();

        $optionalWhenUpdate = $isUpdate ? 'sometimes|' : '';

        $rules = [
            'name' => 'required|string|max:20',
            'name_en' => 'nullable|string|max:100',
            'phone_number' => array_filter([
                'nullable',
                'string',
                $memberId ? Rule::unique('users', 'phone_number')->ignore($memberId) : Rule::unique('users', 'phone_number'),
            ]),
            'email' => 'nullable|email|unique:users,email' . ($isUpdate ? ',' . $memberId : ''),
            'birth_date' => 'nullable|string|regex:/^\d{8}$/',
            'school_name' => 'nullable|string|max:255',
            'is_school_representative' => array_filter(explode('|', $optionalWhenUpdate . 'boolean')),
            'email_marketing_consent' => array_filter(explode('|', $optionalWhenUpdate . 'boolean')),
            'kakao_marketing_consent' => array_filter(explode('|', $optionalWhenUpdate . 'boolean')),
            'address_postcode' => array_filter(explode('|', $optionalWhenUpdate . 'nullable|string')),
            'address_base' => array_filter(explode('|', $optionalWhenUpdate . 'nullable|string')),
            'address_detail' => array_filter(explode('|', $optionalWhenUpdate . 'nullable|string')),
            'member_level' => ['required', Rule::in(['pending', 'associate', 'regular', 'lifetime', 'senior'])],
            'job_type' => ['required', Rule::in(['specialist', 'resident', 'public_doctor', 'military_doctor', 'nurse', 'other'])],
            'license_number' => 'nullable|string|max:80',
            'specialist_number' => 'nullable|string|max:80',
            'specialty' => 'nullable|string|max:120',
            'medical_department' => 'nullable|string|max:100',
            'workplace_name' => 'nullable|string|max:200',
            'workplace_phone' => 'nullable|string|max:40',
            'workplace_zipcode' => 'nullable|string|max:20',
            'workplace_address' => 'nullable|string',
            'workplace_address_detail' => 'nullable|string',
            'graduate_year' => 'nullable|integer|min:1950|max:2100',
            'membership_fee_basis_at' => array_filter(explode('|', $optionalWhenUpdate . 'nullable|date')),
            'annual_fee_status' => array_merge(
                array_filter(explode('|', $optionalWhenUpdate . 'nullable')),
                [Rule::in(['none', 'paid', 'unpaid'])]
            ),
            'certified_instructor' => array_filter(explode('|', $optionalWhenUpdate . 'boolean')),
            'committee_codes' => 'nullable|array',
            'committee_codes.*' => ['string', Rule::in($committeeCodes)],
        ];

        if ($isUpdate) {
            $rules['password'] = 'nullable|string|min:8|max:10|confirmed';
            $rules['password_confirmation'] = 'nullable|string|same:password';
        } else {
            $rules['join_type'] = 'required|in:email,kakao,naver';
            $rules['login_id'] = 'required|string|unique:users,login_id';
            $rules['password'] = 'required|string|min:8|max:10|confirmed';
            $rules['password_confirmation'] = 'required|string|same:password';
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('committee_codes')) {
            $this->merge(['committee_codes' => []]);
        }

        if ($this->exists('phone_number')) {
            $normalizedPhone = User::normalizePhone((string) $this->input('phone_number', ''));
            $this->merge([
                'phone_number' => $normalizedPhone !== '' ? $normalizedPhone : null,
            ]);
        }

        if (! $this->isMethod('PUT') && ! $this->isMethod('PATCH')) {
            if (! $this->filled('join_type')) {
                $this->merge(['join_type' => 'email']);
            }
            $this->merge([
                'certified_instructor' => $this->boolean('certified_instructor'),
                'is_school_representative' => $this->boolean('is_school_representative'),
                'email_marketing_consent' => $this->boolean('email_marketing_consent'),
                'kakao_marketing_consent' => $this->boolean('kakao_marketing_consent'),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'member_level.required' => '회원 등급을 선택해주세요.',
            'job_type.required' => '구분을 선택해주세요.',
            'name.required' => '이름은 필수 입력 항목입니다.',
            'name.max' => '이름은 최대 20자까지 입력 가능합니다.',
            'phone_number.unique' => '이미 사용 중인 휴대폰번호입니다.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
            'email.unique' => '이미 사용 중인 이메일입니다.',
            'birth_date.regex' => '생년월일은 8자리 숫자(YYYYMMDD) 형식으로 입력해주세요.',
            'join_type.required' => '가입 구분은 필수 선택 항목입니다.',
            'join_type.in' => '가입 구분은 이메일, 카카오, 네이버 중 하나를 선택해야 합니다.',
            'login_id.required' => '아이디는 필수 입력 항목입니다.',
            'login_id.unique' => '이미 사용 중인 아이디입니다.',
            'password.required' => '비밀번호는 필수 입력 항목입니다.',
            'password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'password.max' => '비밀번호는 최대 10자까지 입력 가능합니다.',
            'password.confirmed' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
            'password_confirmation.same' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isRep = $this->boolean('is_school_representative');
            $schoolName = trim((string) $this->input('school_name', ''));
            if (! $isRep || $schoolName === '') {
                return;
            }

            $query = User::whereNull('withdrawn_at')
                ->where('role', 'user')
                ->where('school_name', $schoolName)
                ->where('is_school_representative', true);

            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $member = $this->route('user');
                $memberId = $member instanceof User ? $member->id : $member;
                if ($memberId) {
                    $query->where('id', '!=', $memberId);
                }
            }

            if ($query->exists()) {
                $validator->errors()->add('is_school_representative', '해당 학교의 대표자가 이미 등록되어 있습니다.');
            }
        });
    }
}

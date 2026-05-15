<?php

return [
    /**
     * 인증의 유지 — 학술대회(학술행사 사전등록) 참가로 인정할 최소 횟수 목표.
     */
    'certification_conference_required' => (int) env('MYPAGE_CERT_CONFERENCE_REQUIRED', 3),

    /**
     * 학술행사 참가 집계: 결제 완료된 사전등록만 카운트.
     */
    'certification_count_registration_payment_statuses' => ['completed'],

    /**
     * 연회비 무통장 안내(프론트 퍼블 기본값과 동일하게 유지, 운영에서 .env 로 덮어쓰기 가능).
     */
    'membership_bank_display_name' => env('MYPAGE_MEMBERSHIP_BANK_NAME', '국민은행'),
    'membership_bank_account_no' => env('MYPAGE_MEMBERSHIP_BANK_ACCOUNT', '287937-00-000083'),
    'membership_bank_holder' => env('MYPAGE_MEMBERSHIP_BANK_HOLDER', '대한기능의학회'),
];

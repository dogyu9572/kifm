<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MailformController extends Controller
{
    public function welcomeApproved(): View
    {
        return $this->renderMail('mail_welcome_approved', '회원 가입 승인 및 환영 안내', 'mail_welcome_approved');
    }

    public function passwordChanged(): View
    {
        return $this->renderMail('mail_password_changed', '비밀번호 변경 완료 안내', 'mail_password_changed');
    }

    public function passwordReset(): View
    {
        return $this->renderMail('mail_password_reset', '비밀번호 초기화 안내', 'mail_password_reset');
    }

    public function verificationExpired(): View
    {
        return $this->renderMail('mail_verification_expired', '인증의 만료 안내', 'mail_verification_expired');
    }

    public function dormantAccountRecovery(): View
    {
        return $this->renderMail('mail_dormant_account_recovery', '휴면 계정 해제 및 비밀번호 재설정을 안내', 'mail_dormant_account_recovery');
    }

    public function membershipFeePaidCard(): View
    {
        return $this->renderMail('mail_membership_fee_paid_card', '회비 납부 완료 안내', 'mail_membership_fee_paid_card');
    }

    public function membershipFeePaidOnline(): View
    {
        return $this->renderMail('mail_membership_fee_paid_online', '회비 납부 완료 안내', 'mail_membership_fee_paid_online');
    }

    public function preRegistrationComplete(): View
    {
        return $this->renderMail('mail_pre_registration_complete', '사전등록 완료 안내', 'mail_pre_registration_complete');
    }

    public function courseApplicationComplete(): View
    {
        return $this->renderMail('mail_course_application_complete', '연수강좌 신청 완료 안내', 'mail_course_application_complete');
    }

    public function applicationReceived(): View
    {
        return $this->renderMail('mail_application_received', '위원회 신청 접수 완료 안내', 'mail_application_received');
    }

    public function applicationRejected(): View
    {
        return $this->renderMail('mail_application_rejected', '위원회 신청 불가 안내', 'mail_application_rejected');
    }

    public function approvalResult(): View
    {
        return $this->renderMail('mail_approval_result', '위원회 승인 결과 안내', 'mail_approval_result');
    }

    private function renderMail(string $view, string $gName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = 'mailform';
        $gSlug = $slug;

        return view('mailform.' . $view, compact('page_type', 'gNum', 'gName', 'gSlug'));
    }
}

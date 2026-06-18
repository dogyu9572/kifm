<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\CertifiedMember;
use App\Models\CommunityCommitteeApplication;
use App\Models\EduCourseEnrollment;
use App\Models\EduTrainingPayment;
use App\Models\MembershipPayment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailformNotificationService
{
    public function sendPasswordChanged(User $user): void
    {
        $this->sendToUser($user, 'mailform.mail_password_changed', '비밀번호 변경 완료 안내');
    }

    public function sendDormantAccountRecovery(User $user): void
    {
        $this->sendToUser($user, 'mailform.mail_dormant_account_recovery', '휴면 계정 해제 및 비밀번호 재설정 안내');
    }

    public function sendCommitteeApplicationReceived(CommunityCommitteeApplication $application): void
    {
        $this->sendToApplication($application, 'mailform.mail_application_received', '위원회 신청 접수 완료 안내');
    }

    public function sendCommitteeApplicationRejected(CommunityCommitteeApplication $application): void
    {
        $this->sendToApplication($application, 'mailform.mail_application_rejected', '위원회 신청 결과 안내');
    }

    public function sendCommitteeApplicationApproved(CommunityCommitteeApplication $application): void
    {
        $this->sendToApplication($application, 'mailform.mail_approval_result', '위원회 승인 결과 안내');
    }

    public function sendTrainingCourseApplicationComplete(EduTrainingPayment $payment): void
    {
        $payment->loadMissing(['training', 'items', 'member']);
        $view = $payment->payment_method === 'bank_transfer'
            ? 'mailform.mail_lecture_registration_complete'
            : 'mailform.mail_course_application_complete';
        $this->send(
            (string) $payment->email,
            (string) ($payment->name ?: $payment->email),
            $view,
            '연수강좌 신청 완료 안내',
            ['payment' => $payment]
        );
    }

    public function sendMembershipFeePaid(MembershipPayment $payment): void
    {
        $payment->loadMissing(['member', 'plan']);
        $view = $payment->payment_method === 'card'
            ? 'mailform.mail_membership_fee_paid_card'
            : 'mailform.mail_membership_fee_paid_online';

        $this->send(
            (string) $payment->member?->email,
            (string) ($payment->member?->name ?: $payment->member?->email),
            $view,
            '회비 납부 안내',
            ['payment' => $payment]
        );
    }

    public function sendAcademicConferencePreRegistrationComplete(AcademicEventRegistration $registration): void
    {
        $registration->loadMissing(['event', 'items', 'member']);
        $view = $registration->payment_method === 'bank_transfer'
            ? 'mailform.mail_conference_registration_vbank'
            : 'mailform.mail_pre_registration_complete';
        $this->send(
            (string) ($registration->member?->email ?: $registration->email),
            (string) (($registration->member?->name ?: $registration->name) ?: $registration->email),
            $view,
            '학술대회 사전등록 완료 안내',
            ['registration' => $registration]
        );
    }

    public function sendOnlineAcademyRegistrationComplete(EduCourseEnrollment $enrollment): void
    {
        $enrollment->loadMissing(['course', 'member']);
        $member = $enrollment->member;
        $view = $enrollment->payment_method === 'bank_transfer'
            ? 'mailform.mail_academy_registration_vbank'
            : 'mailform.mail_academy_registration_card';

        $this->send(
            (string) $member?->email,
            (string) ($member?->name ?: $member?->email),
            $view,
            '온라인 아카데미 신청 완료 안내',
            ['enrollment' => $enrollment]
        );
    }

    public function sendCertifiedMemberExpiryReminder(CertifiedMember $certifiedMember): void
    {
        $certifiedMember->loadMissing('member');
        $member = $certifiedMember->member;
        if (! $member) {
            return;
        }

        $this->send(
            (string) $member->email,
            (string) ($member->name ?: $member->email),
            'mailform.mail_verification_expired',
            '인정의 유효기간 만료 예정 안내',
            [
                'member' => $member,
                'certifiedMember' => $certifiedMember,
            ]
        );
    }

    private function sendToUser(User $user, string $view, string $subject): void
    {
        $this->send((string) $user->email, (string) ($user->name ?: $user->email), $view, $subject, ['member' => $user]);
    }

    private function sendToApplication(CommunityCommitteeApplication $application, string $view, string $subject): void
    {
        $application->loadMissing(['committee', 'member']);
        $member = $application->member;
        $email = (string) ($member?->email ?: $application->email);
        $name = (string) (($member?->name ?: $application->applicant_name) ?: $email);

        $this->send(
            $email,
            $name,
            $view,
            $subject,
            ['application' => $application]
        );
    }

    /** @param array<string, mixed> $data */
    private function send(string $email, string $name, string $view, string $subject, array $data = []): void
    {
        if ($email === '') {
            return;
        }

        try {
            Mail::send($view, $data, function ($message) use ($email, $name, $subject): void {
                $message->to($email, $name)->subject($this->prefixedSubject($subject));
            });
        } catch (Throwable $e) {
            Log::warning('메일폼 발송 실패', [
                'view' => $view,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function prefixedSubject(string $subject): string
    {
        $subject = trim($subject);
        $prefix = '[대한기능의학회]';

        if ($subject === '') {
            return $prefix;
        }
        if (str_starts_with($subject, $prefix)) {
            return $subject;
        }

        return $prefix.' '.$subject;
    }
}

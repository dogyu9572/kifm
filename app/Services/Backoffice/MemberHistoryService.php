<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEventRegistration;
use App\Models\CertifiedMember;
use App\Models\EduCourseEnrollment;
use App\Models\EduTrainingPayment;
use App\Models\MemberExecutive;
use App\Models\MembershipPayment;
use App\Models\User;

class MemberHistoryService
{
    /**
     * 회원 상세(edit) 화면용 각종 이력 6종을 한 번에 반환한다.
     * - 학술대회 / 연수 / 온라인 / 인정의 / 임원 / 회비
     *
     * @return array<string, mixed>
     */
    public function forMember(User $member): array
    {
        $memberId = $member->id;

        return [
            'academicRegistrations' => AcademicEventRegistration::query()
                ->with(['event', 'items'])
                ->where('member_id', $memberId)
                ->orderByDesc('registered_at')
                ->get(),

            'eduTrainingPayments' => EduTrainingPayment::query()
                ->with(['training'])
                ->where('member_id', $memberId)
                ->orderByDesc('registered_at')
                ->get(),

            'eduCourseEnrollments' => EduCourseEnrollment::query()
                ->with(['course'])
                ->where('member_id', $memberId)
                ->orderByDesc('applied_at')
                ->get(),

            'certifiedMembers' => CertifiedMember::query()
                ->with(['renewals'])
                ->where('member_id', $memberId)
                ->orderByDesc('acquired_date')
                ->get(),

            'memberExecutives' => MemberExecutive::query()
                ->where('member_id', $memberId)
                ->orderByDesc('term_start_date')
                ->get(),

            'membershipPayments' => MembershipPayment::query()
                ->with(['plan'])
                ->where('member_id', $memberId)
                ->orderByDesc('paid_at')
                ->orderByDesc('requested_at')
                ->get(),
        ];
    }

    /**
     * 회원 상세 이력 표 라벨 일괄 반환.
     * 도메인 서비스의 정적 라벨 메서드를 재사용한다.
     *
     * @return array<string, array<string, string>>
     */
    public static function labels(): array
    {
        return [
            'academicSeason' => AcademicEventService::seasonLabels(),
            'academicRegType' => AcademicEventRegistrationService::regTypeLabels(),
            'academicPaymentMethod' => AcademicEventRegistrationService::paymentMethodLabels(),
            'academicPaymentStatus' => AcademicEventRegistrationService::paymentStatusLabels(),

            'eduTrainingSeason' => AcademicEventService::seasonLabels(),
            'eduTrainingRegType' => self::eduTrainingRegTypeLabels(),
            'eduTrainingPaymentMethod' => self::eduTrainingMethodLabels(),
            'eduTrainingPaymentStatus' => self::eduTrainingStatusLabels(),

            'eduCourseCategory' => self::eduCourseCategoryLabels(),
            'eduCourseStatus' => self::eduCourseStatusLabels(),
            'eduCourseCertificate' => self::eduCourseCertificateLabels(),

            'membershipPaymentMethod' => MembershipPaymentService::paymentMethodLabels(),
            'membershipPaymentStatus' => MembershipPaymentService::paymentStatusLabels(),

            'executiveRole' => MemberExecutive::roleLabels(),
        ];
    }

    /** @return array<string, string> */
    private static function eduTrainingRegTypeLabels(): array
    {
        return [
            'pre' => '사전등록',
            'onsite' => '현장등록',
        ];
    }

    /** @return array<string, string> */
    private static function eduTrainingMethodLabels(): array
    {
        return [
            'card' => '신용카드',
            'bank_transfer' => '무통장 입금',
        ];
    }

    /** @return array<string, string> */
    private static function eduTrainingStatusLabels(): array
    {
        return [
            'pending_payment' => '결제 대기',
            'pending' => '입금 대기',
            'completed' => '결제 완료',
            'cancel_requested' => '취소 요청',
            'cancelled' => '취소(환불) 완료',
        ];
    }

    /** @return array<string, string> */
    private static function eduCourseCategoryLabels(): array
    {
        return [
            'conference' => '학술대회',
            'training' => '연수강좌',
            'regular' => '일반교육',
            'required' => '필수 과정',
            'online_advanced' => '정기강좌',
        ];
    }

    /** @return array<string, string> */
    private static function eduCourseStatusLabels(): array
    {
        return [
            'in_progress' => '수강 중',
            'completed' => '수강 완료',
            'payment_pending' => '결제 대기',
            'expired' => '수강기간 만료',
        ];
    }

    /** @return array<string, string> */
    private static function eduCourseCertificateLabels(): array
    {
        return [
            'issued' => '발급완료',
            'not_issued' => '미발급',
        ];
    }
}

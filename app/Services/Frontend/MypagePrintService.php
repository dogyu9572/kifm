<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\EduCourseEnrollment;
use App\Models\EduTrainingPayment;
use App\Models\MemberExecutive;
use App\Models\MembershipPayment;
use App\Models\User;
use App\Services\Backoffice\MembershipPaymentService;
use App\Services\Frontend\PublicOnlineAcademyService;

class MypagePrintService
{
    public function membershipReceipt(User $user, ?int $paymentId = null): ?MembershipPayment
    {
        $query = MembershipPayment::query()
            ->with('plan')
            ->where('member_id', $user->id)
            ->where('payment_status', 'completed');

        if ($paymentId !== null) {
            return $query->whereKey($paymentId)->first();
        }

        return $query->orderByDesc('paid_at')->first();
    }

    public function registrationReceipt(User $user, int $registrationId): ?AcademicEventRegistration
    {
        return AcademicEventRegistration::query()
            ->with(['event', 'items'])
            ->where('member_id', $user->id)
            ->whereKey($registrationId)
            ->where('payment_status', 'completed')
            ->first();
    }

    public function courseReceipt(User $user, int $enrollmentId): ?EduCourseEnrollment
    {
        return EduCourseEnrollment::query()
            ->with('course')
            ->where('member_id', $user->id)
            ->whereKey($enrollmentId)
            ->whereIn('payment_status', PublicOnlineAcademyService::PAYMENT_COMPLETED_STATUSES)
            ->first();
    }

    public function trainingPaymentReceipt(User $user, int $paymentId): ?EduTrainingPayment
    {
        return EduTrainingPayment::query()
            ->with(['training', 'items'])
            ->where('member_id', $user->id)
            ->whereKey($paymentId)
            ->where('payment_status', 'completed')
            ->first();
    }

    public function participationCertificate(User $user, int $registrationId): ?AcademicEventRegistration
    {
        return AcademicEventRegistration::query()
            ->with('event')
            ->where('member_id', $user->id)
            ->whereKey($registrationId)
            ->where('payment_status', 'completed')
            ->whereNull('cancelled_at')
            ->first();
    }

    public function courseCompletion(User $user, int $enrollmentId): ?EduCourseEnrollment
    {
        return EduCourseEnrollment::query()
            ->with('course')
            ->where('member_id', $user->id)
            ->whereKey($enrollmentId)
            ->where('enrollment_status', 'completed')
            ->first();
    }

    public function executiveAppointment(User $user, int $executiveId): ?MemberExecutive
    {
        return MemberExecutive::query()
            ->where('member_id', $user->id)
            ->whereKey($executiveId)
            ->first();
    }

    /** @return array<string, string> */
    public function paymentMethodLabels(): array
    {
        return array_merge(MembershipPaymentService::paymentMethodLabels(), [
            'bank' => '무통장입금',
            'bank_transfer' => '무통장입금',
        ]);
    }
}

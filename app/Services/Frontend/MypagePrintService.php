<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\EduCourseEnrollment;
use App\Models\MemberExecutive;
use App\Models\MembershipPayment;
use App\Models\User;
use App\Services\Backoffice\MembershipPaymentService;

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
            ->with('event')
            ->where('member_id', $user->id)
            ->whereKey($registrationId)
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
            ->where('is_active', true)
            ->first();
    }

    /** @return array<string, string> */
    public function paymentMethodLabels(): array
    {
        return MembershipPaymentService::paymentMethodLabels();
    }
}

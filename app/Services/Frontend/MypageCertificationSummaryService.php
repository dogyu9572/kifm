<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\EduCourseEnrollment;
use App\Models\MembershipPayment;
use App\Models\User;

/**
 * 마이페이지 개인정보 상단 — 인증의 유지 위젯용 집계.
 */
class MypageCertificationSummaryService
{
    /**
     * @return array{
     *   conference_count: int,
     *   conference_required: int,
     *   conference_short: int,
     *   online_academy_completed: bool,
     *   membership_fee_paid: bool,
     *   progress_percent: float
     * }
     */
    public function summarize(User $user): array
    {
        $required = max(1, (int) config('mypage.certification_conference_required', 3));
        $statuses = config('mypage.certification_count_registration_payment_statuses', ['completed']);
        if (! is_array($statuses) || $statuses === []) {
            $statuses = ['completed'];
        }

        $conferenceCount = (int) AcademicEventRegistration::query()
            ->where('member_id', $user->id)
            ->whereNull('cancelled_at')
            ->whereIn('payment_status', $statuses)
            ->whereNotNull('academic_event_id')
            ->toBase()
            ->selectRaw('COUNT(DISTINCT academic_event_id) as cnt')
            ->value('cnt');

        $onlineCompleted = EduCourseEnrollment::query()
            ->where('member_id', $user->id)
            ->where('enrollment_status', 'completed')
            ->exists();

        $feePaid = ($user->annual_fee_status === 'paid')
            || MembershipPayment::query()
                ->where('member_id', $user->id)
                ->where('payment_status', 'completed')
                ->exists();

        $short = max(0, $required - $conferenceCount);
        $progressPercent = $required > 0 ? min(100.0, ($conferenceCount / $required) * 100.0) : 0.0;

        return [
            'conference_count' => $conferenceCount,
            'conference_required' => $required,
            'conference_short' => $short,
            'online_academy_completed' => $onlineCompleted,
            'membership_fee_paid' => $feePaid,
            'progress_percent' => $progressPercent,
        ];
    }
}

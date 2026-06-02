<?php

namespace App\Services\Frontend;

use App\Models\EduCourseEnrollment;
use App\Models\User;
use App\Services\Backoffice\MembershipPaymentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class MypageOnlineTrainingService
{
    public function paginate(User $user, Request $request): LengthAwarePaginator
    {
        $query = EduCourseEnrollment::query()
            ->with('course')
            ->where('member_id', $user->id)
            ->orderByDesc('applied_at')
            ->orderByDesc('id');

        if ($request->filled('year')) {
            $query->whereYear('applied_at', (int) $request->get('year'));
        }
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('enrollment_status', (string) $request->get('status'));
        }
        $keyword = trim((string) $request->get('keyword', ''));
        if ($keyword !== '') {
            $query->whereHas('course', function ($courseQuery) use ($keyword) {
                $courseQuery->where('title', 'like', '%'.$keyword.'%');
            });
        }

        return $query->paginate(20)->withQueryString();
    }

    public function findForMember(User $user, int $id): ?EduCourseEnrollment
    {
        return EduCourseEnrollment::query()
            ->with('course')
            ->where('member_id', $user->id)
            ->whereKey($id)
            ->first();
    }

    /** @return array<string, string> */
    public function enrollmentStatusLabels(): array
    {
        return [
            'in_progress' => '수강 중',
            'completed' => '수강 완료',
            'payment_pending' => '결제 대기',
            'expired' => '수강기간 만료',
        ];
    }

    /** @return array<string, string> */
    public function paymentStatusLabels(): array
    {
        return [
            'pending_payment' => '결제대기',
            'pending' => '입금대기',
            'completed' => '결제완료',
            'paid' => '결제완료',
            'cancel_requested' => '취소요청',
            'cancelled' => '취소',
        ];
    }

    /** @return array<string, string> */
    public function paymentMethodLabels(): array
    {
        return MembershipPaymentService::paymentMethodLabels();
    }
}

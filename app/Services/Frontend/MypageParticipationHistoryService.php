<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\User;
use App\Services\Backoffice\MembershipPaymentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class MypageParticipationHistoryService
{
    public function paginate(User $user, Request $request): LengthAwarePaginator
    {
        $perPage = 20;
        $query = AcademicEventRegistration::query()
            ->with('event')
            ->where('member_id', $user->id)
            ->orderByDesc('registered_at')
            ->orderByDesc('id');

        if ($request->filled('year')) {
            $year = (int) $request->get('year');
            $query->whereYear('registered_at', $year);
        }
        if ($request->filled('month')) {
            $month = (int) $request->get('month');
            $query->whereMonth('registered_at', $month);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findForMember(User $user, int $id): ?AcademicEventRegistration
    {
        return AcademicEventRegistration::query()
            ->with(['event', 'items'])
            ->where('member_id', $user->id)
            ->whereKey($id)
            ->first();
    }

    /** @return array<string, string> */
    public function paymentStatusLabels(): array
    {
        return [
            'pending_payment' => '결제대기',
            'pending' => '입금대기',
            'completed' => '등록완료',
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

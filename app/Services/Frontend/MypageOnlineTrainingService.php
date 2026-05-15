<?php

namespace App\Services\Frontend;

use App\Models\EduCourseEnrollment;
use App\Models\User;
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
}

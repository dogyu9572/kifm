<?php

namespace App\Services\Frontend;

use App\Models\MembershipPayment;
use App\Models\PaymentPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 프론트 마이페이지 연회비 결제 신청.
 */
class MypageMembershipPaymentService
{
    /**
     * @return array<int, array{id: int, label: string, amount: int}>
     */
    public function activePlanOptions(): array
    {
        return PaymentPlan::query()
            ->where('category', 'membership')
            ->where('use_status', 'active')
            ->orderByDesc('id')
            ->get()
            ->map(static fn (PaymentPlan $plan): array => [
                'id' => (int) $plan->id,
                'label' => (string) $plan->plan_name,
                'amount' => (int) ($plan->price ?? 0),
            ])
            ->all();
    }

    public function createPendingPayment(User $user, int $planId, string $paymentMethod): MembershipPayment
    {
        $plan = PaymentPlan::query()
            ->where('category', 'membership')
            ->where('use_status', 'active')
            ->findOrFail($planId);

        $hasCompleted = MembershipPayment::query()
            ->where('member_id', $user->id)
            ->where('payment_status', 'completed')
            ->exists();

        if ($hasCompleted) {
            throw new \RuntimeException('이미 연회비를 납부하셨습니다.');
        }

        $pending = MembershipPayment::query()
            ->where('member_id', $user->id)
            ->where('payment_status', 'pending')
            ->exists();

        if ($pending) {
            throw new \RuntimeException('이미 결제 대기 중인 신청이 있습니다.');
        }

        return DB::transaction(function () use ($user, $plan, $paymentMethod): MembershipPayment {
            return MembershipPayment::query()->create([
                'payment_no' => $this->generatePaymentNo(),
                'member_id' => $user->id,
                'membership_plan_id' => $plan->id,
                'amount' => (int) ($plan->price ?? 0),
                'member_grade' => $user->member_level,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'requested_at' => now(),
                'receipt_issue' => 'NO',
            ]);
        });
    }

    public function cancelPendingPayment(User $user, MembershipPayment $payment): void
    {
        if ((int) $payment->member_id !== (int) $user->id) {
            abort(403);
        }

        if ($payment->payment_status !== 'pending') {
            throw new \RuntimeException('취소할 수 없는 결제 상태입니다.');
        }

        $payment->update(['payment_status' => 'cancelled']);
    }

    private function generatePaymentNo(): string
    {
        do {
            $no = 'PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        } while (MembershipPayment::query()->where('payment_no', $no)->exists());

        return $no;
    }
}

<?php

namespace App\Services\Frontend;

use App\Models\MembershipPayment;
use App\Models\PaymentPlan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
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
    public function activePlanOptions(User $user): array
    {
        return PaymentPlan::query()
            ->with('grades')
            ->where('category', 'membership')
            ->where('member_status', 'member')
            ->where('use_status', 'active')
            ->orderBy('price')
            ->orderBy('id')
            ->get()
            ->filter(fn (PaymentPlan $plan): bool => $this->isEligiblePlanForUser($user, $plan))
            ->map(static fn (PaymentPlan $plan): array => [
                'id' => (int) $plan->id,
                'label' => (string) $plan->plan_name,
                'amount' => (int) ($plan->price ?? 0),
            ])
            ->values()
            ->all();
    }

    public function createPendingPayment(User $user, int $planId, string $paymentMethod): MembershipPayment
    {
        if ($this->isExemptMember($user)) {
            throw new \RuntimeException('이미 연회비를 납부하셨습니다.');
        }

        $plan = PaymentPlan::query()
            ->with('grades')
            ->where('category', 'membership')
            ->where('member_status', 'member')
            ->where('use_status', 'active')
            ->findOrFail($planId);

        if (! $this->isEligiblePlanForUser($user, $plan)) {
            throw new \RuntimeException('납부 가능한 연회비 항목을 선택해주세요.');
        }

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
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->first();

        if ($pending && $paymentMethod === 'card' && $pending->payment_method === 'card') {
            $pending->forceFill([
                'membership_plan_id' => $plan->id,
                'amount' => (int) ($plan->price ?? 0),
                'member_grade' => $user->member_level,
                'requested_at' => now(),
            ])->save();

            return $pending->refresh()->loadMissing('plan');
        }

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

    public function confirmTossPayment(string $orderId, string $paymentKey, int $amount, User $user): MembershipPayment
    {
        $payment = MembershipPayment::query()
            ->with('plan')
            ->where('member_id', $user->id)
            ->where('payment_no', $orderId)
            ->where('payment_method', 'card')
            ->first();

        if (! $payment) {
            throw new \RuntimeException('확인할 연회비 카드 결제 신청 내역이 없습니다.');
        }
        if ((int) $payment->amount !== $amount) {
            throw new \RuntimeException('결제 금액이 신청 금액과 일치하지 않습니다.');
        }
        if ($payment->payment_status === 'completed') {
            return $payment;
        }

        $payload = $this->requestTossConfirm($paymentKey, $orderId, $amount);
        $isCompleted = ($payload['status'] ?? null) === 'DONE';
        $legacy = is_array($payment->legacy_import_json) ? $payment->legacy_import_json : [];
        $legacy['toss_payment'] = $payload;
        $legacy['toss_payment_key'] = $paymentKey;

        $payment->update([
            'payment_status' => $isCompleted ? 'completed' : 'pending',
            'paid_at' => $isCompleted ? now() : null,
            'legacy_import_json' => $legacy,
        ]);

        if ($isCompleted) {
            $user->forceFill([
                'annual_fee_status' => 'paid',
                'membership_fee_basis_at' => now()->toDateString(),
            ])->save();
        }

        return $payment->refresh()->loadMissing('plan');
    }

    private function generatePaymentNo(): string
    {
        do {
            $no = 'PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        } while (MembershipPayment::query()->where('payment_no', $no)->exists());

        return $no;
    }

    private function isExemptMember(User $user): bool
    {
        return in_array($user->member_level, ['lifetime', 'senior'], true);
    }

    private function isEligiblePlanForUser(User $user, PaymentPlan $plan): bool
    {
        if ($this->isExemptMember($user)) {
            return false;
        }

        $targetGrade = $this->gradeForPlan($plan);
        if ($targetGrade === '') {
            return true;
        }

        return $this->gradeRank($targetGrade) >= $this->gradeRank((string) $user->member_level);
    }

    private function gradeForPlan(PaymentPlan $plan): string
    {
        $plan->loadMissing('grades');

        return (string) ($plan->grades->pluck('grade')->first() ?? '');
    }

    private function gradeRank(string $grade): int
    {
        return [
            'associate' => 1,
            'regular' => 2,
            'lifetime' => 3,
            'senior' => 4,
        ][$grade] ?? 0;
    }

    private function requestTossConfirm(string $paymentKey, string $orderId, int $amount): array
    {
        $secretKey = (string) config('services.toss.secret_key');
        if ($secretKey === '') {
            throw new \RuntimeException('토스페이먼츠 시크릿 키가 설정되어 있지 않습니다.');
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->post('https://api.tosspayments.com/v1/payments/confirm', [
                'paymentKey' => $paymentKey,
                'orderId' => $orderId,
                'amount' => $amount,
            ]);

        $payload = $response->json();
        if (! $response->successful()) {
            throw new \RuntimeException((string) ($payload['message'] ?? '토스페이먼츠 결제 승인에 실패했습니다.'));
        }

        return is_array($payload) ? $payload : [];
    }
}

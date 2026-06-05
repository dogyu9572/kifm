<?php

namespace App\Services\Backoffice;

use App\Models\MembershipPayment;
use App\Models\PaymentPlan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MembershipPaymentService
{
    /** @return array<string, string> */
    public static function paymentStatusLabels(): array
    {
        return [
            'pending' => '결제 대기',
            'completed' => '결제 완료',
            'cancelled' => '취소(환불) 완료',
        ];
    }

    /** @return array<string, string> */
    public static function paymentMethodLabels(): array
    {
        return [
            'card' => '신용카드',
            'bank_transfer' => '무통장',
        ];
    }

    /** @return array<string, string> */
    public static function keywordTypeLabels(): array
    {
        return [
            'memberId' => '아이디',
            'memberName' => '이름',
            'phone' => '핸드폰번호',
            'email' => '이메일주소',
            'license' => '면허번호',
        ];
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $query = MembershipPayment::query()
            ->with(['member', 'plan'])
            ->orderByDesc('requested_at')
            ->orderByDesc('id');

        $this->applyFilters($query, $request);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  Builder<MembershipPayment>  $query
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        $paymentStatus = (string) $request->get('payment_status', 'all');
        if ($paymentStatus !== '' && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        $dateFrom = $request->get('date_from');
        if (is_string($dateFrom) && $dateFrom !== '') {
            $query->whereDate('requested_at', '>=', $dateFrom);
        }
        $dateTo = $request->get('date_to');
        if (is_string($dateTo) && $dateTo !== '') {
            $query->whereDate('requested_at', '<=', $dateTo);
        }

        $keywordType = (string) $request->get('keyword_type', '');
        $keyword = trim((string) $request->get('keyword', ''));
        if ($keyword === '') {
            return;
        }

        $query->whereHas('member', function (Builder $memberQuery) use ($keywordType, $keyword) {
            $like = '%' . $keyword . '%';
            match ($keywordType) {
                'memberName' => $memberQuery->where('name', 'like', $like),
                'phone' => $memberQuery->where('phone_number', 'like', $like),
                'email' => $memberQuery->where('email', 'like', $like),
                'license' => $memberQuery->where('license_number', 'like', $like),
                default => $memberQuery->where('login_id', 'like', $like),
            };
        });
    }

    public function getById(int $id): MembershipPayment
    {
        return MembershipPayment::query()->with(['member', 'plan'])->findOrFail($id);
    }

    /**
     * @return array<int, string>
     */
    public function activeMembershipPlanOptions(): array
    {
        return PaymentPlan::query()
            ->where('category', 'membership')
            ->where('use_status', 'active')
            ->orderByDesc('id')
            ->pluck('plan_name', 'id')
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePayment(MembershipPayment $payment, array $data): MembershipPayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $payment->fill([
                'membership_plan_id' => $data['membership_plan_id'] ?? null,
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'depositor_name' => $data['depositor_name'] ?? null,
                'paid_at' => $this->nullableDateTime($data['paid_at'] ?? null),
                'receipt_issue' => $data['receipt_issue'],
                'receipt_type' => $data['receipt_type'] ?? null,
                'receipt_number' => $data['receipt_number'] ?? null,
                'refund_bank_name' => $data['refund_bank_name'] ?? null,
                'refund_account_no' => $data['refund_account_no'] ?? null,
                'refund_holder_name' => $data['refund_holder_name'] ?? null,
            ]);

            if (isset($data['membership_plan_id']) && $data['membership_plan_id']) {
                $plan = PaymentPlan::query()->find($data['membership_plan_id']);
                if (! $plan) {
                    throw (new ModelNotFoundException())->setModel(PaymentPlan::class, [$data['membership_plan_id']]);
                }
                $payment->amount = (int) ($plan->price ?? 0);
            }

            $this->enforceStatusRules($payment);
            $payment->save();

            $this->syncMemberLevelByStatus($payment->member, $payment->payment_status);

            return $payment->fresh(['member', 'plan']);
        });
    }

    public function destroy(MembershipPayment $payment): void
    {
        MembershipPayment::query()
            ->whereKey($payment->id)
            ->delete();
    }

    /**
     * 무통장 입금 확인 처리.
     */
    public function confirmDeposit(MembershipPayment $payment, string $depositorName, string $paidAt): MembershipPayment
    {
        return DB::transaction(function () use ($payment, $depositorName, $paidAt) {
            $payment->payment_method = 'bank_transfer';
            $payment->depositor_name = $depositorName;
            $payment->payment_status = 'completed';
            $payment->paid_at = $this->nullableDateTime($paidAt);

            $this->enforceStatusRules($payment);
            $payment->save();

            $this->syncMemberLevelByStatus($payment->member, 'completed');

            return $payment->fresh(['member', 'plan']);
        });
    }

    private function enforceStatusRules(MembershipPayment $payment): void
    {
        if ($payment->payment_status === 'completed') {
            if ($payment->paid_at === null) {
                $payment->paid_at = now();
            }

            $existsCompleted = MembershipPayment::query()
                ->where('member_id', $payment->member_id)
                ->where('payment_status', 'completed')
                ->where('id', '!=', $payment->id ?? 0)
                ->exists();
            if ($existsCompleted) {
                throw new \RuntimeException('이미 결제 완료된 회비 이력이 있어 중복 완료 처리할 수 없습니다.');
            }
        }

        if ($payment->payment_status === 'cancelled') {
            return;
        }
    }

    private function syncMemberLevelByStatus(User $member, string $paymentStatus): void
    {
        if ($paymentStatus === 'completed') {
            if ($member->member_level === 'associate') {
                $member->update(['member_level' => 'regular']);
            }

            return;
        }

        if ($paymentStatus === 'cancelled') {
            if ($member->member_level === 'regular') {
                $member->update(['member_level' => 'associate']);
            }
        }
    }

    private function nullableDateTime(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}

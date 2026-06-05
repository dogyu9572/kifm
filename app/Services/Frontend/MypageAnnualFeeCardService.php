<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\EduCourseEnrollment;
use App\Models\MembershipPayment;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * 마이페이지 개인정보 상단 — 연회비 납부 카드용 데이터.
 */
class MypageAnnualFeeCardService
{
    /**
     * @return array{
     *   mode: 'paid'|'pending_bank'|'unpaid',
     *   paid_at_formatted: string|null,
     *   receipt_available: bool,
     *   exempt_without_payment: bool,
     *   pending_payment: \App\Models\MembershipPayment|null,
     *   bank_name: string,
     *   bank_account_no: string,
     *   bank_holder: string
     * }
     */
    public function resolve(User $user): array
    {
        $bankName = (string) config('mypage.membership_bank_display_name');
        $bankAccount = (string) config('mypage.membership_bank_account_no');
        $bankHolder = (string) config('mypage.membership_bank_holder');

        $latestCompleted = MembershipPayment::query()
            ->where('member_id', $user->id)
            ->where('payment_status', 'completed')
            ->whereNotNull('paid_at')
            ->orderByDesc('paid_at')
            ->first();

        if ($latestCompleted !== null) {
            return [
                'mode' => 'paid',
                'paid_at_formatted' => $this->formatKoreanDateTime($latestCompleted->paid_at),
                'receipt_available' => true,
                'exempt_without_payment' => false,
                'pending_payment' => null,
                'bank_name' => $bankName,
                'bank_account_no' => $bankAccount,
                'bank_holder' => $bankHolder,
            ];
        }

        if ($this->isExemptMember($user)) {
            return [
                'mode' => 'paid',
                'paid_at_formatted' => null,
                'receipt_available' => false,
                'exempt_without_payment' => true,
                'pending_payment' => null,
                'bank_name' => $bankName,
                'bank_account_no' => $bankAccount,
                'bank_holder' => $bankHolder,
            ];
        }

        $pendingBank = MembershipPayment::query()
            ->where('member_id', $user->id)
            ->where('payment_status', 'pending')
            ->where('payment_method', 'bank_transfer')
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->first();

        if ($pendingBank !== null) {
            return [
                'mode' => 'pending_bank',
                'paid_at_formatted' => null,
                'receipt_available' => false,
                'exempt_without_payment' => false,
                'pending_payment' => $pendingBank,
                'bank_name' => $bankName,
                'bank_account_no' => $bankAccount,
                'bank_holder' => $bankHolder,
            ];
        }

        return [
            'mode' => 'unpaid',
            'paid_at_formatted' => null,
            'receipt_available' => false,
            'exempt_without_payment' => false,
            'pending_payment' => null,
            'bank_name' => $bankName,
            'bank_account_no' => $bankAccount,
            'bank_holder' => $bankHolder,
        ];
    }

    private function formatKoreanDateTime(?Carbon $dt): ?string
    {
        if ($dt === null) {
            return null;
        }

        return $dt->timezone(config('app.timezone'))->format('Y년 m월 d일 H:i:s');
    }

    private function isExemptMember(User $user): bool
    {
        return in_array($user->member_level, ['lifetime', 'senior'], true);
    }
}

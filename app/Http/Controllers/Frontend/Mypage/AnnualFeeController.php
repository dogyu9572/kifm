<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Http\Requests\FrontendMypageAnnualFeeStoreRequest;
use App\Models\MembershipPayment;
use App\Services\Backoffice\MemberService;
use App\Services\Frontend\MypageAnnualFeeCardService;
use App\Services\Frontend\MypageMembershipPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnualFeeController extends Controller
{
    use RendersMypageViews;

    public function __construct(
        private readonly MypageMembershipPaymentService $membershipPaymentService,
        private readonly MypageAnnualFeeCardService $annualFeeCardService,
    ) {}

    public function index(): View
    {
        $user = $this->currentMember();

        return $this->renderMypage('annual_fee', '01', '연회비 납부', 'annual_fee', [
            'user' => $user,
            'plans' => $this->membershipPaymentService->activePlanOptions(),
            'annualFeeCard' => $this->annualFeeCardService->resolve($user),
            'memberLevelLabel' => MemberService::memberLevelLabels()[$user->member_level] ?? $user->member_level,
        ]);
    }

    public function store(FrontendMypageAnnualFeeStoreRequest $request): RedirectResponse
    {
        $user = $this->currentMember();
        $validated = $request->validated();

        try {
            $payment = $this->membershipPaymentService->createPendingPayment(
                $user,
                (int) $validated['membership_plan_id'],
                (string) $validated['payment_method'],
            );

            if ($validated['payment_method'] === 'bank_transfer') {
                $payment->update([
                    'depositor_name' => $validated['depositor_name'] ?? $user->name,
                    'refund_bank_name' => $validated['refund_bank_name'] ?? null,
                    'refund_account_no' => $validated['refund_account_no'] ?? null,
                    'refund_holder_name' => $validated['refund_holder_name'] ?? null,
                ]);
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['membership_plan_id' => $e->getMessage()]);
        }

        return redirect()->route('mypage.annual_fee_end');
    }

    public function end(): View
    {
        return $this->renderMypage('annual_fee_end', '01', '연회비 납부', 'annual_fee_end', [
            'annualFeeCard' => $this->annualFeeCardService->resolve($this->currentMember()),
        ]);
    }

    public function cancelPending(): RedirectResponse
    {
        $user = $this->currentMember();
        $payment = MembershipPayment::query()
            ->where('member_id', $user->id)
            ->where('payment_status', 'pending')
            ->orderByDesc('id')
            ->first();

        if ($payment === null) {
            return redirect()->route('mypage.profile_edit')->with('error', '취소할 결제 신청이 없습니다.');
        }

        try {
            $this->membershipPaymentService->cancelPendingPayment($user, $payment);
        } catch (\RuntimeException $e) {
            return redirect()->route('mypage.profile_edit')->with('error', $e->getMessage());
        }

        return redirect()->route('mypage.profile_edit')->with('success', '결제 신청이 취소되었습니다.');
    }
}

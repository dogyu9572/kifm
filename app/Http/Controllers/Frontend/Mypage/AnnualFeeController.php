<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Http\Requests\FrontendMypageAnnualFeeStoreRequest;
use App\Models\MembershipPayment;
use App\Services\Backoffice\MemberService;
use App\Services\Frontend\MypageAnnualFeeCardService;
use App\Services\Frontend\MailformNotificationService;
use App\Services\Frontend\MypageMembershipPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnualFeeController extends Controller
{
    use RendersMypageViews;

    public function __construct(
        private readonly MypageMembershipPaymentService $membershipPaymentService,
        private readonly MypageAnnualFeeCardService $annualFeeCardService,
        private readonly MailformNotificationService $mailNotifier,
    ) {}

    public function index(): View
    {
        $user = $this->currentMember();

        return $this->renderMypage('annual_fee', '01', '연회비 납부', 'annual_fee', [
            'user' => $user,
            'plans' => $this->membershipPaymentService->activePlanOptions($user),
            'annualFeeCard' => $this->annualFeeCardService->resolve($user),
            'memberLevelLabel' => MemberService::memberLevelLabels()[$user->member_level] ?? $user->member_level,
        ]);
    }

    public function store(FrontendMypageAnnualFeeStoreRequest $request): JsonResponse|RedirectResponse
    {
        $user = $this->currentMember();
        $validated = $request->validated();

        try {
            if ($validated['payment_method'] === 'card' && ! $this->hasTossKeys()) {
                throw new \RuntimeException('토스페이먼츠 테스트 키가 설정되어 있지 않습니다.');
            }

            $payment = $this->membershipPaymentService->createPendingPayment(
                $user,
                (int) $validated['membership_plan_id'],
                (string) $validated['payment_method'],
            );

            if ($validated['payment_method'] === 'bank_transfer') {
                $legacy = is_array($payment->legacy_import_json) ? $payment->legacy_import_json : [];
                $legacy['deposit_expected_date'] = $validated['deposit_expected_date'] ?? null;

                $payment->update([
                    'depositor_name' => $validated['depositor_name'] ?? $user->name,
                    'receipt_issue' => $validated['receipt_issue'] ?? 'NO',
                    'receipt_type' => ($validated['receipt_issue'] ?? 'NO') === 'YES' ? ($validated['receipt_type'] ?? null) : null,
                    'receipt_number' => ($validated['receipt_issue'] ?? 'NO') === 'YES' ? ($validated['receipt_number'] ?? null) : null,
                    'refund_bank_name' => $validated['refund_bank_name'] ?? null,
                    'refund_account_no' => $validated['refund_account_no'] ?? null,
                    'refund_holder_name' => $validated['refund_holder_name'] ?? null,
                    'legacy_import_json' => $legacy,
                ]);
                $this->mailNotifier->sendMembershipFeePaid($payment->refresh());
            }
        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['payment' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput()->with('alert', $e->getMessage());
        }

        if ($validated['payment_method'] === 'card' && (int) $payment->amount > 0) {
            return response()->json($this->tossPaymentPayload($payment));
        }

        return redirect()->route('mypage.annual_fee_end');
    }

    public function confirmTossPayment(Request $request): RedirectResponse
    {
        $user = $this->currentMember();

        $validated = $request->validate([
            'paymentKey' => ['required', 'string'],
            'orderId' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $payment = $this->membershipPaymentService->confirmTossPayment(
                $validated['orderId'],
                $validated['paymentKey'],
                (int) $validated['amount'],
                $user,
            );
            if ($payment->payment_status === 'completed') {
                $this->mailNotifier->sendMembershipFeePaid($payment);
            }
        } catch (\RuntimeException $e) {
            return redirect()->route('mypage.annual_fee')
                ->with('alert', $e->getMessage());
        }

        return redirect()->route('mypage.annual_fee_end')
            ->with('success', '토스페이먼츠 결제가 완료되었습니다.');
    }

    public function failTossPayment(Request $request): RedirectResponse
    {
        $message = trim((string) $request->query('message'));

        return redirect()->route('mypage.annual_fee')
            ->with('alert', $message !== '' ? $message : '토스페이먼츠 결제가 취소되었거나 실패했습니다.');
    }

    public function end(): View
    {
        $user = $this->currentMember();
        $payment = MembershipPayment::query()
            ->with('plan')
            ->where('member_id', $user->id)
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->first();

        return $this->renderMypage('annual_fee_end', '01', '연회비 납부', 'annual_fee_end', [
            'user' => $user,
            'payment' => $payment,
            'annualFeeCard' => $this->annualFeeCardService->resolve($user),
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

    private function hasTossKeys(): bool
    {
        return (string) config('services.toss.client_key') !== ''
            && (string) config('services.toss.secret_key') !== '';
    }

    private function tossPaymentPayload(MembershipPayment $payment): array
    {
        $payment->loadMissing(['member', 'plan']);

        return [
            'success' => true,
            'clientKey' => (string) config('services.toss.client_key'),
            'orderId' => $payment->payment_no,
            'orderName' => mb_substr((string) ($payment->plan?->plan_name ?? '연회비 결제'), 0, 100),
            'amount' => (int) $payment->amount,
            'customerName' => $payment->member?->name,
            'customerEmail' => $payment->member?->email,
            'customerMobilePhone' => preg_replace('/\D+/', '', (string) $payment->member?->phone_number),
            'customerKey' => 'kifm-membership-member-' . $payment->member_id,
            'successUrl' => route('mypage.annual_fee.toss_success'),
            'failUrl' => route('mypage.annual_fee.toss_fail'),
        ];
    }
}

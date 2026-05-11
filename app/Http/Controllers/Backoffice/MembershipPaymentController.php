<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\MembershipPaymentRequest;
use App\Models\MembershipPayment;
use App\Services\Backoffice\MemberService;
use App\Services\Backoffice\MembershipPaymentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MembershipPaymentController extends Controller
{
    public function __construct(
        private readonly MembershipPaymentService $membershipPaymentService
    ) {
    }

    public function index(Request $request)
    {
        $payments = $this->membershipPaymentService->paginateFiltered($request);
        $perPage = $payments->perPage();
        $presetDateUrls = $this->buildPresetDateUrls($request);

        return view('backoffice.payment-memberships.index', [
            'payments' => $payments,
            'perPage' => $perPage,
            'paymentStatusLabels' => MembershipPaymentService::paymentStatusLabels(),
            'paymentMethodLabels' => MembershipPaymentService::paymentMethodLabels(),
            'keywordTypeLabels' => MembershipPaymentService::keywordTypeLabels(),
            'memberLevelLabels' => MemberService::memberLevelLabels(),
            'presetDateUrls' => $presetDateUrls,
        ]);
    }

    public function edit(MembershipPayment $payment)
    {
        $payment = $this->membershipPaymentService->getById($payment->id);

        return view('backoffice.payment-memberships.edit', [
            'payment' => $payment,
            'paymentStatusLabels' => MembershipPaymentService::paymentStatusLabels(),
            'paymentMethodLabels' => MembershipPaymentService::paymentMethodLabels(),
            'memberLevelLabels' => MemberService::memberLevelLabels(),
            'membershipPlanOptions' => $this->membershipPaymentService->activeMembershipPlanOptions(),
        ]);
    }

    public function update(MembershipPaymentRequest $request, MembershipPayment $payment): RedirectResponse
    {
        try {
            $this->membershipPaymentService->updatePayment($payment, $request->validated());
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['payment_status' => $e->getMessage()]);
        }

        return redirect()
            ->route('backoffice.payment-memberships.edit', $payment)
            ->with('success', '회비 납부 정보가 저장되었습니다.');
    }

    public function destroy(MembershipPayment $payment): RedirectResponse
    {
        $this->membershipPaymentService->destroy($payment);

        return redirect()
            ->route('backoffice.payment-memberships.index')
            ->with('success', '회비 납부 내역이 삭제되었습니다.');
    }

    public function confirmDeposit(Request $request, MembershipPayment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'depositor_name' => ['required', 'string', 'max:100'],
            'paid_at' => ['required', 'date'],
        ]);

        try {
            $this->membershipPaymentService->confirmDeposit(
                $payment,
                $validated['depositor_name'],
                $validated['paid_at']
            );
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['payment_status' => $e->getMessage()]);
        }

        return redirect()
            ->route('backoffice.payment-memberships.edit', $payment)
            ->with('success', '입금 확인이 완료되었습니다.');
    }

    /**
     * @return array<string, string>
     */
    private function buildPresetDateUrls(Request $request): array
    {
        $base = $request->except(['date_from', 'date_to', 'page']);
        $today = Carbon::today();

        return [
            'all' => route('backoffice.payment-memberships.index', $base),
            'today' => route('backoffice.payment-memberships.index', array_merge($base, [
                'date_from' => $today->format('Y-m-d'),
                'date_to' => $today->format('Y-m-d'),
            ])),
            'yesterday' => route('backoffice.payment-memberships.index', array_merge($base, [
                'date_from' => $today->copy()->subDay()->format('Y-m-d'),
                'date_to' => $today->copy()->subDay()->format('Y-m-d'),
            ])),
            'week' => route('backoffice.payment-memberships.index', array_merge($base, [
                'date_from' => $today->copy()->subDays(6)->format('Y-m-d'),
                'date_to' => $today->format('Y-m-d'),
            ])),
            'month' => route('backoffice.payment-memberships.index', array_merge($base, [
                'date_from' => $today->copy()->startOfMonth()->format('Y-m-d'),
                'date_to' => $today->format('Y-m-d'),
            ])),
            'year' => route('backoffice.payment-memberships.index', array_merge($base, [
                'date_from' => $today->copy()->startOfYear()->format('Y-m-d'),
                'date_to' => $today->format('Y-m-d'),
            ])),
        ];
    }
}


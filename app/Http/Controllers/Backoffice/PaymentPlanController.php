<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\PaymentPlanRequest;
use App\Models\PaymentPlan;
use App\Services\Backoffice\PaymentPlanService;
use Illuminate\Http\Request;

class PaymentPlanController extends Controller
{
    public function __construct(
        protected PaymentPlanService $paymentPlanService
    ) {}

    public function index(Request $request)
    {
        $plans = $this->paymentPlanService->paginateFiltered($request);

        return view('backoffice.payment_plans.index', [
            'plans' => $plans,
            'perPage' => $plans->perPage(),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
            'gradeLabels' => PaymentPlanService::gradeLabels(),
            'memberTypeLabels' => PaymentPlanService::memberTypeLabels(),
        ]);
    }

    public function create(Request $request)
    {
        return view('backoffice.payment_plans.create', [
            'plan' => null,
            'cancelUrl' => $this->paymentPlansCancelUrl($request),
            'selectedGrades' => old('grades', []),
            'selectedTypes' => old('member_types', []),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
            'gradeLabels' => PaymentPlanService::gradeLabels(),
            'memberTypeLabels' => PaymentPlanService::memberTypeLabels(),
        ]);
    }

    public function store(PaymentPlanRequest $request)
    {
        $this->paymentPlanService->createFromValidated($request->validated());

        return redirect()
            ->route('backoffice.payment-plans.index')
            ->with('success', '결제 항목이 등록되었습니다.');
    }

    public function edit(Request $request, PaymentPlan $paymentPlan)
    {
        $paymentPlan->load(['grades', 'types']);

        return view('backoffice.payment_plans.edit', [
            'plan' => $paymentPlan,
            'cancelUrl' => $this->paymentPlansCancelUrl($request),
            'selectedGrades' => old('grades', $paymentPlan->grades->pluck('grade')->all()),
            'selectedTypes' => old('member_types', $paymentPlan->types->pluck('member_type')->all()),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
            'gradeLabels' => PaymentPlanService::gradeLabels(),
            'memberTypeLabels' => PaymentPlanService::memberTypeLabels(),
        ]);
    }

    public function update(PaymentPlanRequest $request, PaymentPlan $paymentPlan)
    {
        $this->paymentPlanService->updateFromValidated($paymentPlan, $request->validated());

        return redirect()
            ->route('backoffice.payment-plans.index')
            ->with('success', '결제 항목이 수정되었습니다.');
    }

    public function destroy(PaymentPlan $paymentPlan)
    {
        $paymentPlan->delete();

        return redirect()
            ->route('backoffice.payment-plans.index')
            ->with('success', '결제 항목이 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'plan_ids' => ['required', 'array'],
            'plan_ids.*' => ['integer', 'exists:payment_plans,id'],
        ]);

        $deleted = $this->paymentPlanService->deletePlans($validated['plan_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'건의 결제 항목이 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * 목록/취소 이동용 URL. return_url은 백오피스 경로만 허용.
     */
    protected function paymentPlansCancelUrl(Request $request): string
    {
        $fallback = route('backoffice.payment-plans.index');
        $raw = $request->query('return_url');
        if (! is_string($raw) || $raw === '') {
            return $fallback;
        }

        $decoded = urldecode($raw);
        if (str_starts_with($decoded, '/backoffice/') && ! str_starts_with($decoded, '//')) {
            return $decoded;
        }

        $parts = parse_url($decoded);
        if (! empty($parts['path']) && str_starts_with($parts['path'], '/backoffice/')) {
            $query = isset($parts['query']) ? '?'.$parts['query'] : '';

            return $parts['path'].$query;
        }

        return $fallback;
    }
}

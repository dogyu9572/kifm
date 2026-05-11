<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\CouponRequest;
use App\Models\Coupon;
use App\Services\Backoffice\CouponService;
use App\Services\Backoffice\PaymentPlanService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        protected CouponService $couponService
    ) {}

    public function index(Request $request)
    {
        $coupons = $this->couponService->paginateFiltered($request);

        return view('backoffice.coupons.index', [
            'coupons' => $coupons,
            'perPage' => $coupons->perPage(),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
        ]);
    }

    public function create(Request $request)
    {
        return view('backoffice.coupons.create', [
            'coupon' => null,
            'cancelUrl' => $this->couponsCancelUrl($request),
            'selectedCategories' => old('payment_categories', []),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
        ]);
    }

    public function store(CouponRequest $request)
    {
        $this->couponService->createFromValidated($request->payloadForService());

        return redirect()
            ->route('backoffice.coupons.index')
            ->with('success', '쿠폰이 등록되었습니다.');
    }

    public function edit(Request $request, Coupon $coupon)
    {
        $coupon->load('paymentCategories');

        return view('backoffice.coupons.edit', [
            'coupon' => $coupon,
            'cancelUrl' => $this->couponsCancelUrl($request),
            'selectedCategories' => old('payment_categories', $coupon->paymentCategories->pluck('payment_category')->all()),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
        ]);
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $this->couponService->updateFromValidated($coupon, $request->payloadForService());

        return redirect()
            ->route('backoffice.coupons.index')
            ->with('success', '쿠폰이 수정되었습니다.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('backoffice.coupons.index')
            ->with('success', '쿠폰이 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'coupon_ids' => ['required', 'array'],
            'coupon_ids.*' => ['integer', 'exists:coupons,id'],
        ]);

        $deleted = $this->couponService->deleteCoupons($validated['coupon_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'건의 쿠폰이 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    public function generateCode()
    {
        $code = $this->couponService->generateUniqueCode();

        return response()->json(['coupon_code' => $code]);
    }

    protected function couponsCancelUrl(Request $request): string
    {
        $fallback = route('backoffice.coupons.index');
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

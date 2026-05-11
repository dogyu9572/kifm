<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Services\Backoffice\CouponUsageHistoryService;
use App\Services\Backoffice\PaymentPlanService;
use Illuminate\Http\Request;

class CouponUsageHistoryController extends Controller
{
    public function __construct(
        protected CouponUsageHistoryService $couponUsageHistoryService
    ) {}

    public function index(Request $request)
    {
        $histories = $this->couponUsageHistoryService->paginateFiltered($request);

        return view('backoffice.coupon-usage-history.index', [
            'histories' => $histories,
            'perPage' => $histories->perPage(),
            'categoryLabels' => PaymentPlanService::categoryLabels(),
        ]);
    }
}

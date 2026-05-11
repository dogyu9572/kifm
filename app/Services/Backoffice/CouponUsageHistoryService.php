<?php

namespace App\Services\Backoffice;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponUsageHistoryService
{
    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        return new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: $perPage,
            currentPage: LengthAwarePaginator::resolveCurrentPage(),
            options: [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }
}

<?php

namespace App\Services\Backoffice;

use App\Models\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $query = Coupon::query()->with('paymentCategories');

        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('payment_category')) {
            $cat = $request->input('payment_category');
            $query->whereHas('paymentCategories', function (Builder $q) use ($cat) {
                $q->where('payment_category', $cat);
            });
        }

        if ($request->filled('valid_from')) {
            $query->whereDate('valid_to', '>=', $request->input('valid_from'));
        }

        if ($request->filled('valid_to')) {
            $query->whereDate('valid_from', '<=', $request->input('valid_to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('keyword')) {
            $kw = trim((string) $request->input('keyword'));
            if ($kw !== '') {
                $query->where('coupon_name', 'like', '%'.$kw.'%');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data  coupon_name, coupon_code, discount_type, discount_value, valid_from, valid_to, status, payment_categories
     */
    public function createFromValidated(array $data): Coupon
    {
        return DB::transaction(function () use ($data) {
            $coupon = new Coupon;
            $this->fillCoupon($coupon, $data);
            $coupon->save();
            $this->syncCategories($coupon, $data['payment_categories'] ?? []);

            return $coupon->fresh(['paymentCategories']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateFromValidated(Coupon $coupon, array $data): Coupon
    {
        return DB::transaction(function () use ($coupon, $data) {
            $this->fillCoupon($coupon, $data);
            $coupon->save();
            $this->syncCategories($coupon, $data['payment_categories'] ?? []);

            return $coupon->fresh(['paymentCategories']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function fillCoupon(Coupon $coupon, array $data): void
    {
        $coupon->coupon_name = $data['coupon_name'];
        $coupon->coupon_code = strtoupper(trim((string) $data['coupon_code']));
        $coupon->discount_type = $data['discount_type'];
        $coupon->discount_value = (float) $data['discount_value'];
        $coupon->valid_from = $data['valid_from'];
        $coupon->valid_to = $data['valid_to'];
        $coupon->status = $data['status'];
    }

    /**
     * @param  list<string>  $categories
     */
    protected function syncCategories(Coupon $coupon, array $categories): void
    {
        $coupon->paymentCategories()->delete();
        foreach ($categories as $category) {
            $coupon->paymentCategories()->create(['payment_category' => $category]);
        }
    }

    public function generateUniqueCode(int $length = 8): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (Coupon::where('coupon_code', $code)->exists());

        return $code;
    }

    /**
     * @param  list<int>  $ids
     */
    public function deleteCoupons(array $ids): int
    {
        return Coupon::whereIn('id', $ids)->delete();
    }
}

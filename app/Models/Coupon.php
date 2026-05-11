<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_to',
        'usage_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'float',
            'valid_from' => 'date',
            'valid_to' => 'date',
            'usage_count' => 'integer',
        ];
    }

    public function paymentCategories(): HasMany
    {
        return $this->hasMany(CouponPaymentCategory::class, 'coupon_id');
    }
}

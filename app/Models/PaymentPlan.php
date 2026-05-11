<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    protected $fillable = [
        'plan_name',
        'category',
        'member_status',
        'executive',
        'price_early',
        'price_site',
        'price',
        'use_status',
    ];

    protected function casts(): array
    {
        return [
            'price_early' => 'integer',
            'price_site' => 'integer',
            'price' => 'integer',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(PaymentPlanGrade::class, 'plan_id');
    }

    public function types(): HasMany
    {
        return $this->hasMany(PaymentPlanType::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('use_status', 'active');
    }

    public function membershipPayments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class, 'membership_plan_id');
    }
}

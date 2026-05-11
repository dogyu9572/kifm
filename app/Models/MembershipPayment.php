<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipPayment extends Model
{
    protected $fillable = [
        'payment_no',
        'member_id',
        'membership_plan_id',
        'amount',
        'member_grade',
        'payment_method',
        'payment_status',
        'requested_at',
        'paid_at',
        'depositor_name',
        'receipt_issue',
        'receipt_type',
        'receipt_number',
        'refund_bank_name',
        'refund_account_no',
        'refund_holder_name',
        'legacy_import_json',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'requested_at' => 'datetime',
            'paid_at' => 'datetime',
            'legacy_import_json' => 'array',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'membership_plan_id');
    }
}


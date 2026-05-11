<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EduTrainingPayment extends Model
{
    protected $fillable = [
        'order_no',
        'edu_training_id',
        'member_id',
        'name',
        'license_no',
        'phone',
        'email',
        'reg_type',
        'payment_method',
        'payment_status',
        'total_amount',
        'registered_at',
        'applied_at',
        'paid_at',
        'bank_depositor',
        'bank_deposit_date',
        'admin_memo',
        'receipt_issue',
        'receipt_type',
        'receipt_number',
        'refund_bank',
        'refund_account',
        'refund_holder',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'registered_at' => 'datetime',
            'applied_at' => 'datetime',
            'paid_at' => 'datetime',
            'bank_deposit_date' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(EduTraining::class, 'edu_training_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EduTrainingPaymentItem::class, 'edu_training_payment_id');
    }
}


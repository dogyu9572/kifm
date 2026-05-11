<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EduTrainingPaymentItem extends Model
{
    protected $fillable = [
        'edu_training_payment_id',
        'payment_plan_id',
        'item_name',
        'category',
        'member_scope',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(EduTrainingPayment::class, 'edu_training_payment_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
    }
}


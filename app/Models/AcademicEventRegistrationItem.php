<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicEventRegistrationItem extends Model
{
    protected $fillable = [
        'academic_event_registration_id',
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

    public function registration(): BelongsTo
    {
        return $this->belongsTo(AcademicEventRegistration::class, 'academic_event_registration_id');
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
    }
}

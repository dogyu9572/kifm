<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicEventRegistration extends Model
{
    protected $fillable = [
        'academic_event_id',
        'member_id',
        'registration_no',
        'legacy_unique_no',
        'source_row_json',
        'reg_type',
        'payment_method',
        'payment_status',
        'total_amount',
        'name',
        'license_no',
        'phone',
        'email',
        'registered_at',
        'applied_at',
        'paid_at',
        'bank_depositor',
        'bank_deposit_date',
        'bank_account_text',
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
            'source_row_json' => 'array',
            'total_amount' => 'integer',
            'registered_at' => 'datetime',
            'applied_at' => 'datetime',
            'paid_at' => 'datetime',
            'bank_deposit_date' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(AcademicEvent::class, 'academic_event_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AcademicEventRegistrationItem::class, 'academic_event_registration_id');
    }
}

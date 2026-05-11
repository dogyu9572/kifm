<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_number',
        'recipient_type',
        'member_grade',
        'exclude_phones',
        'schedule_enabled',
        'scheduled_at',
        'sms_type',
        'subject',
        'body',
        'byte_size',
        'status',
        'recipient_count',
        'created_by',
        'sent_at',
    ];

    protected $casts = [
        'schedule_enabled' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'byte_size' => 'integer',
        'recipient_count' => 'integer',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(SmsRecipient::class, 'sms_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'sms_id',
        'source_type',
        'source_id',
        'recipient_name',
        'recipient_phone',
        'send_result',
        'error_message',
    ];

    public function sms(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'sms_id');
    }
}

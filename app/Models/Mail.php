<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_name',
        'sender_email',
        'recipient_type',
        'member_grade',
        'exclude_emails',
        'schedule_enabled',
        'scheduled_at',
        'mail_type',
        'subject',
        'body',
        'status',
        'recipient_count',
        'created_by',
        'sent_at',
    ];

    protected $casts = [
        'schedule_enabled' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'recipient_count' => 'integer',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(MailRecipient::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_id',
        'source_type',
        'source_id',
        'recipient_name',
        'recipient_email',
    ];

    public function mail(): BelongsTo
    {
        return $this->belongsTo(Mail::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberBookmark extends Model
{
    protected $fillable = [
        'user_id',
        'content_type',
        'content_id',
        'snapshot_title',
        'snapshot_menu_label',
        'snapshot_url',
        'bookmarked_at',
    ];

    protected function casts(): array
    {
        return [
            'content_id' => 'integer',
            'bookmarked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

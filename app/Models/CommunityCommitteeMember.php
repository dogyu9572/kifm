<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityCommitteeMember extends Model
{
    protected $fillable = [
        'community_committee_id',
        'user_id',
        'role',
    ];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(CommunityCommittee::class, 'community_committee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


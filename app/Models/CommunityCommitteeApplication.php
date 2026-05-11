<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityCommitteeApplication extends Model
{
    protected $fillable = [
        'community_committee_id',
        'user_id',
        'applicant_name',
        'email',
        'phone',
        'status',
        'reject_reason',
        'applied_at',
        'processed_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'community_committee_id' => 'integer',
            'user_id' => 'integer',
            'processed_by' => 'integer',
            'applied_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(CommunityCommittee::class, 'community_committee_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

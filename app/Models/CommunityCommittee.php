<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityCommittee extends Model
{
    protected $fillable = [
        'name',
        'description',
        'committee_type',
        'thumbnail_path',
        'banner_path',
        'pending_count',
        'member_count',
        'member_limit',
        'visibility_yn',
        'is_mandatory',
        'regulation',
        'protocol',
    ];

    protected function casts(): array
    {
        return [
            'pending_count' => 'integer',
            'member_count' => 'integer',
            'member_limit' => 'integer',
        ];
    }

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommunityCommitteeMember::class, 'community_committee_id')->orderBy('id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CommunityCommitteeApplication::class, 'community_committee_id')->orderByDesc('id');
    }
}


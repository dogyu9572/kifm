<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSponsorMaster extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
        'representative_name',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function eventSponsors(): HasMany
    {
        return $this->hasMany(AcademicEventSponsor::class, 'academic_sponsor_master_id');
    }
}

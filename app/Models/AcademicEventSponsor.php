<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicEventSponsor extends Model
{
    protected $fillable = [
        'academic_event_id',
        'academic_sponsor_master_id',
        'name',
        'logo_path',
        'level',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(AcademicEvent::class, 'academic_event_id');
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(AcademicSponsorMaster::class, 'academic_sponsor_master_id');
    }

    public function mainSlots(): HasMany
    {
        return $this->hasMany(AcademicEventMainSponsorSlot::class, 'academic_event_sponsor_id');
    }
}

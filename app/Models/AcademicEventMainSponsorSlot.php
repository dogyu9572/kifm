<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicEventMainSponsorSlot extends Model
{
    protected $fillable = [
        'academic_event_id',
        'academic_event_sponsor_id',
        'placement',
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

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(AcademicEventSponsor::class, 'academic_event_sponsor_id');
    }
}

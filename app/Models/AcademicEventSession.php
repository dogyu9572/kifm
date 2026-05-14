<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicEventSession extends Model
{
    protected $fillable = [
        'academic_event_id',
        'name',
        'category',
        'session_date',
        'start_time',
        'end_time',
        'description',
        'chair_speakers',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(AcademicEvent::class, 'academic_event_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicEventSessionItem extends Model
{
    protected $fillable = [
        'academic_event_session_id',
        'academic_event_abstract_id',
        'row_type',
        'start_time',
        'end_time',
        'title',
        'presenter',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicEventSession::class, 'academic_event_session_id');
    }

    public function abstractSubmission(): BelongsTo
    {
        return $this->belongsTo(AcademicEventAbstract::class, 'academic_event_abstract_id');
    }
}

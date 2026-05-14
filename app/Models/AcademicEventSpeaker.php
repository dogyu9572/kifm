<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicEventSpeaker extends Model
{
    protected $fillable = [
        'academic_event_id',
        'source',
        'member_id',
        'academic_event_abstract_id',
        'name',
        'affiliation',
        'position',
        'image_path',
        'abstract_title',
        'bio',
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

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function abstractSubmission(): BelongsTo
    {
        return $this->belongsTo(AcademicEventAbstract::class, 'academic_event_abstract_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicEventField extends Model
{
    protected $fillable = [
        'academic_event_id',
        'name',
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

    public function abstracts(): HasMany
    {
        return $this->hasMany(AcademicEventAbstract::class, 'academic_event_field_id');
    }
}

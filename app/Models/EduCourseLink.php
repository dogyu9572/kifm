<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EduCourseLink extends Model
{
    protected $fillable = [
        'edu_course_id',
        'link_event_id',
        'link_training_id',
        'link_round_use',
        'link_round_ids',
        'link_period_type',
        'link_duration_days',
        'link_period_start',
        'link_period_end',
    ];

    protected function casts(): array
    {
        return [
            'link_round_ids' => 'array',
            'link_duration_days' => 'integer',
            'link_period_start' => 'date',
            'link_period_end' => 'date',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(EduCourse::class, 'edu_course_id');
    }
}


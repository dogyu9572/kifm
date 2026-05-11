<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EduTrainingRound extends Model
{
    protected $fillable = [
        'edu_training_id',
        'round_order',
        'round_label',
        'training_method',
        'lecture_date',
        'location_link',
        'apply_start_date',
        'apply_end_date',
        'capacity',
        'is_capacity_unlimited',
        'duration_hours',
        'score',
        'grade_prices',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'lecture_date' => 'date',
            'apply_start_date' => 'date',
            'apply_end_date' => 'date',
            'is_capacity_unlimited' => 'boolean',
            'duration_hours' => 'float',
            'score' => 'float',
            'grade_prices' => 'array',
        ];
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(EduTraining::class, 'edu_training_id');
    }
}

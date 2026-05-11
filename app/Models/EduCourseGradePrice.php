<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EduCourseGradePrice extends Model
{
    protected $fillable = [
        'edu_course_id',
        'grade_code',
        'is_enabled',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'price' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(EduCourse::class, 'edu_course_id');
    }
}


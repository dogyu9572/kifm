<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EduCourseExamQuestion extends Model
{
    protected $fillable = [
        'edu_course_id',
        'question',
        'choices_json',
        'answer_index',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'choices_json' => 'array',
            'answer_index' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(EduCourse::class, 'edu_course_id');
    }
}


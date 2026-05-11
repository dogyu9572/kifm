<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EduCourse extends Model
{
    protected $fillable = [
        'course_type',
        'open_year',
        'linked_event_id',
        'keywords',
        'topics',
        'thumbnail_path',
        'title',
        'professor_name',
        'professor_org',
        'professor_member_id',
        'topic_detail',
        'content',
        'lecture_file_path',
        'video_url',
        'duration_min',
        'completion_score',
        'annual_fee_target',
        'free_yn',
        'free_start_date',
        'free_end_date',
        'period_type',
        'duration_days',
        'period_start',
        'period_end',
        'exam_yn',
        'expose_yn',
        'use_yn',
    ];

    protected function casts(): array
    {
        return [
            'open_year' => 'integer',
            'duration_min' => 'integer',
            'completion_score' => 'integer',
            'duration_days' => 'integer',
            'free_start_date' => 'date',
            'free_end_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function gradePrices(): HasMany
    {
        return $this->hasMany(EduCourseGradePrice::class, 'edu_course_id');
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(EduCourseExamQuestion::class, 'edu_course_id')->orderBy('sort_order');
    }

    public function linkSetting(): HasOne
    {
        return $this->hasOne(EduCourseLink::class, 'edu_course_id');
    }

    public function professorMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_member_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(EduCourseEnrollment::class, 'edu_course_id');
    }
}


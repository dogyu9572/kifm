<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EduCourseEnrollment extends Model
{
    protected $fillable = [
        'edu_course_id',
        'member_id',
        'member_name',
        'member_grade_at',
        'enrollment_status',
        'progress_rate',
        'exam_status',
        'exam_score',
        'total_study_min',
        'last_position_sec',
        'video_duration_sec',
        'last_studied_at',
        'completed_at',
        'certificate_status',
        'certificate_issued_at',
        'payment_no',
        'payment_status',
        'payment_method',
        'payment_item_name',
        'payment_amount',
        'paid_at',
        'bank_depositor',
        'bank_deposit_date',
        'receipt_issue',
        'receipt_type',
        'receipt_number',
        'refund_bank',
        'refund_account',
        'refund_holder',
        'admin_memo',
        'applied_at',
        'expire_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_rate' => 'integer',
            'exam_score' => 'integer',
            'total_study_min' => 'integer',
            'last_position_sec' => 'integer',
            'video_duration_sec' => 'integer',
            'payment_amount' => 'integer',
            'last_studied_at' => 'datetime',
            'completed_at' => 'datetime',
            'certificate_issued_at' => 'datetime',
            'paid_at' => 'datetime',
            'bank_deposit_date' => 'date',
            'applied_at' => 'datetime',
            'expire_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(EduCourse::class, 'edu_course_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}

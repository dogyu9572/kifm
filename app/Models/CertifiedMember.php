<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class CertifiedMember extends Model
{
    protected $fillable = [
        'member_id',
        'validity_start_date',
        'validity_end_date',
        'acquired_date',
        'acquired_validity_start',
        'acquired_validity_end',
        'winter_course_completed',
        'exam_passed',
    ];

    protected function casts(): array
    {
        return [
            'validity_start_date' => 'date',
            'validity_end_date' => 'date',
            'acquired_date' => 'date',
            'acquired_validity_start' => 'date',
            'acquired_validity_end' => 'date',
            'winter_course_completed' => 'boolean',
            'exam_passed' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(CertifiedMemberRenewal::class, 'certified_member_id')->orderBy('renewal_seq');
    }

    public function statusLabel(): string
    {
        $today = Carbon::today();
        if ($today->gt($this->validity_end_date)) {
            return '만료';
        }

        return '정상';
    }

    public function remainingDays(): int
    {
        return Carbon::today()->diffInDays($this->validity_end_date, false);
    }
}


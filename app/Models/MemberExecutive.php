<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class MemberExecutive extends Model
{
    protected $fillable = [
        'member_id',
        'executive_role',
        'term_start_date',
        'term_end_date',
        'is_indefinite',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'term_start_date' => 'date',
            'term_end_date' => 'date',
            'is_indefinite' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public static function roleLabels(): array
    {
        return [
            'president' => '회장',
            'vice_president' => '부회장',
            'secretary_general' => '총무이사',
            'academic_director' => '학술이사',
            'education_director' => '교육이사',
            'finance_director' => '재무이사',
            'ethics_director' => '윤리이사',
            'planning_director' => '기획이사',
            'auditor' => '감사',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function termStatusLabel(): string
    {
        $today = Carbon::today();
        $start = $this->term_start_date;
        $end = $this->term_end_date;

        if ($start instanceof Carbon && $today->lt($start)) {
            return '임기예정';
        }
        if ($this->is_indefinite) {
            return '임기중';
        }
        if ($end instanceof Carbon && $today->gt($end)) {
            return '임기만료';
        }

        return '임기중';
    }
}


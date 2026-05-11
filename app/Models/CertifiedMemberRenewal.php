<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertifiedMemberRenewal extends Model
{
    protected $fillable = [
        'certified_member_id',
        'renewal_seq',
        'renewal_date',
        'renewal_validity_start',
        'renewal_validity_end',
        'attendance_general',
        'attendance_winter',
    ];

    protected function casts(): array
    {
        return [
            'renewal_date' => 'date',
            'renewal_validity_start' => 'date',
            'renewal_validity_end' => 'date',
            'attendance_general' => 'integer',
            'attendance_winter' => 'integer',
        ];
    }

    public function certifiedMember(): BelongsTo
    {
        return $this->belongsTo(CertifiedMember::class, 'certified_member_id');
    }
}


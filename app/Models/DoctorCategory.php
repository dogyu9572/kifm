<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DoctorCategory extends Model
{
    protected $fillable = [
        'name',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function localDoctors(): BelongsToMany
    {
        return $this->belongsToMany(LocalDoctor::class, 'doctor_category_local_doctor');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

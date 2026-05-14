<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LocalDoctor extends Model
{
    protected $fillable = [
        'member_id',
        'allow_member_edit',
        'photo_path',
        'doctor_name',
        'license_no',
        'introduction',
        'hospital_name',
        'sido',
        'sigungu',
        'address',
        'address_detail',
        'homepage',
        'phone',
        'status',
        'view_count',
        'sort_order',
        'legacy_post_id',
        'legacy_csv_extras',
        'functional_tests_selected',
        'treatment_areas_selected',
        'other_symptoms',
        'diseases_text',
    ];

    protected function casts(): array
    {
        return [
            'allow_member_edit' => 'boolean',
            'view_count' => 'integer',
            'sort_order' => 'integer',
            'legacy_csv_extras' => 'array',
            'functional_tests_selected' => 'array',
            'treatment_areas_selected' => 'array',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function doctorCategories(): BelongsToMany
    {
        return $this->belongsToMany(DoctorCategory::class, 'doctor_category_local_doctor');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicEvent extends Model
{
    protected $fillable = [
        'legacy_post_id',
        'year',
        'season',
        'folder_name',
        'title',
        'event_material_path',
        'event_material_description',
        'event_type',
        'online_url',
        'is_public',
        'main_exposure',
        'venue',
        'start_at',
        'end_at',
        'start_time_omit',
        'end_time_omit',
        'greeting_title',
        'greeting_content',
        'greeting_image_path',
        'committee_content',
        'pc_banner_path',
        'thumbnail_path',
        'address',
        'address_detail',
        'address_lat',
        'address_lng',
        'walking_guide',
        'shuttle_guide',
        'pre_reg_start',
        'pre_reg_end',
        'onsite_reg_start',
        'onsite_reg_end',
        'onsite_reg_allowed',
        'pre_reg_guide',
        'reg_fee_guide',
        'cert_doc_guide',
        'reg_info_guide',
        'abstract_start',
        'abstract_end',
        'abstract_revision_end',
        'abstract_judging_end',
        'abstract_result_date',
        'abstract_book_path',
        'presentation_types',
        'submission_guide',
        'abstract_notes',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'legacy_post_id' => 'integer',
            'year' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'start_time_omit' => 'boolean',
            'end_time_omit' => 'boolean',
            'address_lat' => 'decimal:7',
            'address_lng' => 'decimal:7',
            'pre_reg_start' => 'date',
            'pre_reg_end' => 'date',
            'onsite_reg_start' => 'date',
            'onsite_reg_end' => 'date',
            'abstract_start' => 'date',
            'abstract_end' => 'date',
            'abstract_revision_end' => 'date',
            'abstract_judging_end' => 'date',
            'abstract_result_date' => 'date',
            'presentation_types' => 'array',
            'view_count' => 'integer',
        ];
    }

    public function venueFloors(): HasMany
    {
        return $this->hasMany(AcademicEventVenueFloor::class, 'academic_event_id')->orderBy('sort_order');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(AcademicEventField::class, 'academic_event_id')->orderBy('sort_order');
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(AcademicEventSponsor::class, 'academic_event_id')->orderBy('sort_order');
    }

    public function mainSponsorSlots(): HasMany
    {
        return $this->hasMany(AcademicEventMainSponsorSlot::class, 'academic_event_id')->orderBy('sort_order');
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(AcademicEventSpeaker::class, 'academic_event_id')->orderBy('sort_order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AcademicEventSession::class, 'academic_event_id')->orderBy('sort_order');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(AcademicEventRegistration::class, 'academic_event_id')->orderByDesc('registered_at');
    }

    public function abstracts(): HasMany
    {
        return $this->hasMany(AcademicEventAbstract::class, 'academic_event_id')->orderByDesc('submitted_at');
    }
}

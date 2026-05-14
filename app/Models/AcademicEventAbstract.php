<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicEventAbstract extends Model
{
    protected $fillable = [
        'academic_event_id',
        'member_id',
        'registered_by',
        'status',
        'file_receipt_status',
        'author_name',
        'author_name_en',
        'author_phone',
        'author_mobile',
        'author_email',
        'title',
        'presentation_type',
        'academic_event_field_id',
        'note',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (AcademicEventAbstract $abstract): void {
            $abstract->files()->get()->each(static fn (AcademicEventAbstractFile $file) => $file->delete());
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(AcademicEvent::class, 'academic_event_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(AcademicEventField::class, 'academic_event_field_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(AcademicEventAbstractFile::class, 'academic_event_abstract_id')->orderBy('id');
    }
}

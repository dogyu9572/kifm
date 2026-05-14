<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AcademicEventAbstractFile extends Model
{
    protected $fillable = [
        'academic_event_abstract_id',
        'original_name',
        'stored_path',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (AcademicEventAbstractFile $file): void {
            if ($file->stored_path && Storage::disk('public')->exists($file->stored_path)) {
                Storage::disk('public')->delete($file->stored_path);
            }
        });
    }

    public function abstractModel(): BelongsTo
    {
        return $this->belongsTo(AcademicEventAbstract::class, 'academic_event_abstract_id');
    }
}

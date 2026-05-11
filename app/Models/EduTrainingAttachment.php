<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EduTrainingAttachment extends Model
{
    protected $fillable = [
        'edu_training_id',
        'file_path',
        'original_name',
        'sort_order',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(EduTraining::class, 'edu_training_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (EduTrainingAttachment $attachment): void {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        });
    }
}

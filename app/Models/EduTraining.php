<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EduTraining extends Model
{
    protected $fillable = [
        'year',
        'season',
        'title',
        'use_round',
        'round_type',
        'training_method',
        'status',
        'overview',
        'program',
        'registration_info',
        'introduction',
        'textbook_file_path',
    ];

    protected function casts(): array
    {
        return [
            'use_round' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (EduTraining $training): void {
            $training->attachments()->get()->each->delete();
        });
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(EduTrainingRound::class, 'edu_training_id')->orderBy('round_order');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EduTrainingAttachment::class, 'edu_training_id')->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EduTrainingPayment::class, 'edu_training_id');
    }
}

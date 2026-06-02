<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'schedule_type',
        'start_date',
        'end_date',
        'is_single_day',
        'content',
        'link_url',
        'is_visible',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_single_day' => 'boolean',
        'is_visible' => 'boolean',
    ];
}

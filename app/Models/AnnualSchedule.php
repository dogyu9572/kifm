<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'is_single_day',
        'content',
        'is_visible',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_single_day' => 'boolean',
        'is_visible' => 'boolean',
    ];
}

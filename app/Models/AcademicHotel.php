<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicHotel extends Model
{
    protected $fillable = [
        'name',
        'status',
        'phone',
        'distance',
        'address',
        'address_detail',
        'homepage_url',
        'description',
        'image_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}

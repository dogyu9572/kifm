<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocietyExecutive extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_no',
        'position',
        'name',
        'organization',
        'email',
        'photo_path',
        'note',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'group_no' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}

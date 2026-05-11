<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddressBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'member_count',
    ];

    protected $casts = [
        'member_count' => 'integer',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(AddressBookMember::class);
    }
}

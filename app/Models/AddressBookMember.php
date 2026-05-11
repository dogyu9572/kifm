<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressBookMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_book_id',
        'member_id',
        'name',
        'login_id',
        'email',
        'phone',
        'source_type',
    ];

    public function addressBook(): BelongsTo
    {
        return $this->belongsTo(AddressBook::class);
    }
}

<?php

namespace App\Services\Frontend;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MypageProfileUpdateService
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function update(User $user, array $validated): void
    {
        $data = [
            'name' => $validated['name'],
            'name_en' => $validated['name_en'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'job_type' => $validated['job_type'],
            'license_number' => $validated['license_number'],
            'specialty' => $validated['specialty'],
            'workplace_name' => $validated['workplace_name'],
            'workplace_phone' => $validated['workplace_phone'],
            'workplace_zipcode' => $validated['workplace_zipcode'] ?? null,
            'workplace_address' => $validated['workplace_address'] ?? null,
            'workplace_address_detail' => $validated['workplace_address_detail'] ?? null,
            'graduate_year' => $validated['graduate_year'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
    }
}

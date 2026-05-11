<?php

namespace Database\Seeders;

use App\Models\CommunityCommittee;
use Illuminate\Database\Seeder;

class CommunityCommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name' => '학술위원회',
                'committee_type' => 'general',
                'pending_count' => 5,
                'member_count' => 12,
                'member_limit' => 20,
                'visibility_yn' => 'Y',
                'sort_order' => 1,
            ],
            [
                'name' => '편집위원회',
                'committee_type' => 'special',
                'pending_count' => 0,
                'member_count' => 8,
                'member_limit' => null,
                'visibility_yn' => 'N',
                'sort_order' => 2,
            ],
            [
                'name' => '홍보위원회',
                'committee_type' => 'general',
                'pending_count' => 3,
                'member_count' => 6,
                'member_limit' => 15,
                'visibility_yn' => 'Y',
                'sort_order' => 3,
            ],
        ];

        foreach ($rows as $row) {
            CommunityCommittee::query()->updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }
}


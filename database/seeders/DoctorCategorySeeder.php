<?php

namespace Database\Seeders;

use App\Models\DoctorCategory;
use Illuminate\Database\Seeder;

class DoctorCategorySeeder extends Seeder
{
    /**
     * 진료 과목(주치의 전용). CSV 이관 없음 — 프로토타입 상세 화면 순서 기준.
     */
    public function run(): void
    {
        $names = [
            '내과',
            '소아청소년과',
            '이비인후과',
            '가정의학과',
            '피부과',
            '성형외과',
            '산부인과',
            '신경과',
            '정신건강의학과',
        ];

        foreach ($names as $order => $name) {
            DoctorCategory::query()->updateOrCreate(
                ['name' => $name],
                [
                    'sort_order' => $order + 1,
                    'status' => 'active',
                ]
            );
        }
    }
}

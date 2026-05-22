<?php

use App\Support\CategoryOptions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        $now = now();
        $groupId = DB::table('categories')
            ->whereNull('parent_id')
            ->where('depth', 0)
            ->where('code', CategoryOptions::ABSTRACT_PRESENTATION_TYPE_GROUP_CODE)
            ->value('id');
        if (! $groupId) {
            $groupId = DB::table('categories')->insertGetId([
                'parent_id' => null,
                'code' => CategoryOptions::ABSTRACT_PRESENTATION_TYPE_GROUP_CODE,
                'name' => '초록 발표구분',
                'depth' => 0,
                'display_order' => 1000,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $items = [
            'oral' => ['name' => '구연 발표', 'display_order' => 50],
            'poster' => ['name' => '포스터 발표', 'display_order' => 40],
            'video' => ['name' => '비디오 발표', 'display_order' => 30],
            'designated_discussion' => ['name' => '지정 토론', 'display_order' => 20],
            'special' => ['name' => '특별 강연', 'display_order' => 10],
        ];

        foreach ($items as $code => $item) {
            $existingId = DB::table('categories')
                ->where('parent_id', $groupId)
                ->where('code', $code)
                ->value('id');

            $payload = [
                'parent_id' => $groupId,
                'code' => $code,
                'name' => $item['name'],
                'depth' => 1,
                'display_order' => $item['display_order'],
                'is_active' => true,
                'updated_at' => $now,
            ];

            if ($existingId) {
                DB::table('categories')->where('id', $existingId)->update($payload);
                continue;
            }

            DB::table('categories')->insert(array_merge($payload, [
                'created_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        $groupId = DB::table('categories')
            ->whereNull('parent_id')
            ->where('depth', 0)
            ->where('code', CategoryOptions::ABSTRACT_PRESENTATION_TYPE_GROUP_CODE)
            ->value('id');
        if (! $groupId) {
            return;
        }

        DB::table('categories')->where('parent_id', $groupId)->delete();
        DB::table('categories')->where('id', $groupId)->delete();
    }
};

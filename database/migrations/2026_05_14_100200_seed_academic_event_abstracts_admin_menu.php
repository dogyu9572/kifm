<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 학술 행사(93) 하위 — 초록 관리 메뉴(96) 및 메뉴 94·95와 동일 그룹 권한 부여.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('admin_menus')->where('id', 93)->exists()) {
            return;
        }

        $now = now();

        DB::table('admin_menus')->updateOrInsert(
            ['id' => 96],
            [
                'parent_id' => 93,
                'name' => '초록 관리',
                'url' => '/backoffice/academic-event-abstracts',
                'icon' => null,
                'order' => 3,
                'is_active' => true,
                'permission_key' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $groupIds = DB::table('admin_group_menu_permissions')
            ->where('menu_id', 94)
            ->where('granted', true)
            ->pluck('group_id');

        foreach ($groupIds as $groupId) {
            DB::table('admin_group_menu_permissions')->updateOrInsert(
                [
                    'group_id' => $groupId,
                    'menu_id' => 96,
                ],
                [
                    'granted' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('admin_group_menu_permissions')->where('menu_id', 96)->delete();
        DB::table('admin_menus')->where('id', 96)->delete();
    }
};

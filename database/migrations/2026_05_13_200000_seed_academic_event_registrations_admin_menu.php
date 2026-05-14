<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 학술 행사(93) 하위 — 학술행사 목록(94) 바로 아래 참가 및 결제 관리 메뉴(95)를 DB에 반영한다.
 * AdminMenuSeeder 미실행 환경에서도 사이드바에 노출되도록 하며,
 * 메뉴 94 접근 권한이 있는 관리자 그룹에 동일하게 95 권한을 부여한다.
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
            ['id' => 95],
            [
                'parent_id' => 93,
                'name' => '참가 및 결제 관리',
                'url' => '/backoffice/academic-event-registrations',
                'icon' => null,
                'order' => 2,
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
                    'menu_id' => 95,
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
        DB::table('admin_group_menu_permissions')->where('menu_id', 95)->delete();
        DB::table('admin_menus')->where('id', 95)->delete();
    }
};

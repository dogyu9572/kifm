<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->string('menu_scope', 20)->default('site')->after('title')->index();
            $table->foreignId('community_committee_id')->nullable()->after('menu_scope')
                ->constrained('community_committees')->restrictOnDelete();
            $table->string('target_board_slug', 80)->nullable()->after('community_committee_id');
            $table->index(['menu_scope', 'community_committee_id', 'target_board_slug'], 'popups_scope_committee_board_idx');
        });

        DB::table('popups')->update([
            'menu_scope' => 'site',
            'community_committee_id' => null,
            'target_board_slug' => null,
        ]);
    }

    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->dropIndex('popups_scope_committee_board_idx');
        });
        Schema::table('popups', function (Blueprint $table) {
            $table->dropForeign(['community_committee_id']);
        });
        Schema::table('popups', function (Blueprint $table) {
            $table->dropColumn(['menu_scope', 'community_committee_id', 'target_board_slug']);
        });
    }
};

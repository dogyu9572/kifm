<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('board_member_square_notices')) {
            return;
        }

        Schema::table('board_member_square_notices', function (Blueprint $table) {
            if (! Schema::hasColumn('board_member_square_notices', 'legacy_post_id')) {
                $table->unsignedBigInteger('legacy_post_id')->nullable()->after('id');
                $table->unique('legacy_post_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('board_member_square_notices')) {
            return;
        }

        Schema::table('board_member_square_notices', function (Blueprint $table) {
            if (Schema::hasColumn('board_member_square_notices', 'legacy_post_id')) {
                $table->dropUnique(['legacy_post_id']);
                $table->dropColumn('legacy_post_id');
            }
        });
    }
};

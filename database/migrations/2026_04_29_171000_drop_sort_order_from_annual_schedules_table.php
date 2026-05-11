<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('annual_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('annual_schedules', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annual_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('annual_schedules', 'sort_order')) {
                $table->integer('sort_order')->default(1)->after('is_visible');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annual_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('annual_schedules', 'schedule_type')) {
                $table->string('schedule_type', 30)->nullable()->after('title');
            }
            if (! Schema::hasColumn('annual_schedules', 'link_url')) {
                $table->string('link_url', 500)->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('annual_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('annual_schedules', 'link_url')) {
                $table->dropColumn('link_url');
            }
            if (Schema::hasColumn('annual_schedules', 'schedule_type')) {
                $table->dropColumn('schedule_type');
            }
        });
    }
};

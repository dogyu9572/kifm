<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('edu_courses', function (Blueprint $table) {
            $table->unsignedInteger('duration_sec')->default(0)->after('duration_min');
        });

        DB::table('edu_courses')->update([
            'duration_sec' => DB::raw('duration_min * 60'),
        ]);
    }

    public function down(): void
    {
        Schema::table('edu_courses', function (Blueprint $table) {
            $table->dropColumn('duration_sec');
        });
    }
};

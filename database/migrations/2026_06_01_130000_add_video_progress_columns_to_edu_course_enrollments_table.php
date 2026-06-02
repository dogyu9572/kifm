<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('edu_course_enrollments', function (Blueprint $table) {
            $table->unsignedInteger('last_position_sec')->default(0)->after('total_study_min');
            $table->unsignedInteger('video_duration_sec')->default(0)->after('last_position_sec');
            $table->dateTime('completed_at')->nullable()->after('last_studied_at');
        });
    }

    public function down(): void
    {
        Schema::table('edu_course_enrollments', function (Blueprint $table) {
            $table->dropColumn(['last_position_sec', 'video_duration_sec', 'completed_at']);
        });
    }
};

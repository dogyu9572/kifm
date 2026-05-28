<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('academic_events', 'exhibition_image_path')) {
            return;
        }

        Schema::table('academic_events', function (Blueprint $table) {
            $table->string('exhibition_image_path')->nullable()->after('thumbnail_path');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('academic_events', 'exhibition_image_path')) {
            return;
        }

        Schema::table('academic_events', function (Blueprint $table) {
            $table->dropColumn('exhibition_image_path');
        });
    }
};

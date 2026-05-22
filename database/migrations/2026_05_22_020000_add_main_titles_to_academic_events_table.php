<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_events', function (Blueprint $table) {
            $table->string('main_title_1')->nullable()->after('title');
            $table->string('main_title_2')->nullable()->after('main_title_1');
        });
    }

    public function down(): void
    {
        Schema::table('academic_events', function (Blueprint $table) {
            $table->dropColumn(['main_title_1', 'main_title_2']);
        });
    }
};

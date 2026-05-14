<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_sponsor_masters', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('representative_name');
            $table->unsignedInteger('sort_order')->default(0)->after('status');
            $table->index(['status', 'sort_order'], 'ae_sponsor_masters_status_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('academic_sponsor_masters', function (Blueprint $table) {
            $table->dropIndex('ae_sponsor_masters_status_sort_idx');
            $table->dropColumn(['status', 'sort_order']);
        });
    }
};

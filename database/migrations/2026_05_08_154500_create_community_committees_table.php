<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('community_committees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('committee_type', 20)->default('general');
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('pending_count')->default(0);
            $table->unsignedInteger('member_count')->default(0);
            $table->unsignedInteger('member_limit')->nullable();
            $table->string('visibility_yn', 1)->default('Y');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['committee_type', 'visibility_yn'], 'idx_community_committees_type_visibility');
            $table->index('sort_order', 'idx_community_committees_sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_committees');
    }
};


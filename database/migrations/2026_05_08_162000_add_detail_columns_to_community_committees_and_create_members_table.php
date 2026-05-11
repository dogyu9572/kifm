<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('community_committees', function (Blueprint $table) {
            $table->string('banner_path')->nullable()->after('thumbnail_path');
            $table->string('description', 500)->nullable()->after('name');
            $table->string('is_mandatory', 1)->default('N')->after('visibility_yn');
            $table->longText('regulation')->nullable()->after('is_mandatory');
            $table->longText('protocol')->nullable()->after('regulation');
        });

        Schema::create('community_committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_committee_id')->constrained('community_committees')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 20)->default('member');
            $table->timestamps();

            $table->unique(['community_committee_id', 'user_id'], 'uq_comm_committee_user');
            $table->index(['community_committee_id', 'role'], 'idx_comm_committee_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_committee_members');

        Schema::table('community_committees', function (Blueprint $table) {
            $table->dropColumn([
                'banner_path',
                'description',
                'is_mandatory',
                'regulation',
                'protocol',
            ]);
        });
    }
};


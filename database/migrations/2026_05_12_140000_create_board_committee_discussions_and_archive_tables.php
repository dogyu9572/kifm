<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['community_committee_discussions', 'community_committee_archive'] as $suffix) {
            $table = 'board_'.$suffix;
            if (Schema::hasTable($table)) {
                continue;
            }
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('user_id')->nullable();
                $t->string('title');
                $t->text('content');
                $t->string('author_name');
                $t->string('password')->nullable();
                $t->boolean('is_notice')->default(false);
                $t->boolean('is_secret')->default(false);
                $t->string('category')->nullable();
                $t->json('attachments')->nullable();
                $t->integer('view_count')->default(0);
                $t->integer('sort_order')->default(0)->comment('정렬 순서');
                $t->json('custom_fields')->nullable();
                $t->string('thumbnail')->nullable();
                $t->boolean('is_active')->default(true)->comment('게시물 노출 여부');
                $t->timestamps();
                $t->softDeletes();
                $t->index(['is_notice', 'created_at']);
                $t->index(['category', 'created_at']);
                $t->index(['user_id', 'created_at']);
                $t->index(['thumbnail']);
                $t->index(['sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('board_community_committee_discussions');
        Schema::dropIfExists('board_community_committee_archive');
    }
};

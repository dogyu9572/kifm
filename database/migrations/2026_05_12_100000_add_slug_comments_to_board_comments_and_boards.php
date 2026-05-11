<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->boolean('enable_comments')->default(false)->after('permission_comment');
        });

        Schema::table('board_comments', function (Blueprint $table) {
            // 기존 행이 있으면 빈 문자열로 채워진 뒤 신규 저장 시 항상 slug를 넣도록 애플리케이션에서 검증
            $table->string('board_slug', 191)->default('')->after('id');
            $table->json('attachments')->nullable()->after('content');
            $table->index(['board_slug', 'post_id', 'created_at'], 'board_comments_slug_post_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('board_comments', function (Blueprint $table) {
            $table->dropIndex('board_comments_slug_post_created_idx');
            $table->dropColumn(['board_slug', 'attachments']);
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn('enable_comments');
        });
    }
};

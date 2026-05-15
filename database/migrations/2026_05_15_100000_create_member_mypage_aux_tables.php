<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('member_menu_favorites')) {
            Schema::create('member_menu_favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('menu_code', 80);
                $table->string('menu_name_snapshot', 200)->nullable();
                $table->string('menu_url_snapshot', 500)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(1);
                $table->timestamps();

                $table->unique(['user_id', 'menu_code']);
                $table->index(['user_id', 'sort_order']);
            });
        }

        if (! Schema::hasTable('member_bookmarks')) {
            Schema::create('member_bookmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('content_type', 40);
                $table->unsignedBigInteger('content_id');
                $table->string('snapshot_title', 500)->nullable();
                $table->string('snapshot_menu_label', 120)->nullable();
                $table->string('snapshot_url', 500)->nullable();
                $table->timestamp('bookmarked_at')->useCurrent();
                $table->timestamps();

                $table->unique(['user_id', 'content_type', 'content_id']);
                $table->index(['user_id', 'bookmarked_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('member_bookmarks');
        Schema::dropIfExists('member_menu_favorites');
    }
};

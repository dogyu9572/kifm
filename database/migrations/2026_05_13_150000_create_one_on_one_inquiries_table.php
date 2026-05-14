<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('one_on_one_inquiries')) {
            return;
        }

        Schema::create('one_on_one_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete()
                ->comment('문의 회원');
            $table->string('member_name', 100)->nullable()
                ->comment('레거시/비회원 표시용 이름');
            $table->string('member_email', 190)->nullable();
            $table->string('title', 200);
            $table->longText('content')->nullable();
            $table->string('content_format', 10)->default('html')
                ->comment('html | text');
            $table->string('answer_status', 20)->default('PENDING')
                ->comment('PENDING | DONE');
            $table->longText('answer_content')->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->foreignId('answered_by')->nullable()
                ->constrained('users')->nullOnDelete()
                ->comment('답변 작성 관리자');
            $table->string('client_ip', 45)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->json('attachments')->nullable();
            $table->json('answer_attachments')->nullable();
            $table->unsignedBigInteger('legacy_post_id')->nullable();
            $table->unsignedBigInteger('legacy_thread_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('answer_status');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('legacy_thread_id');
            $table->unique('legacy_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('one_on_one_inquiries');
    }
};

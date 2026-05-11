<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name', 100)->nullable();
            $table->string('sender_email', 255)->nullable();
            $table->string('recipient_type', 30)->default('all');
            $table->string('member_grade', 30)->nullable();
            $table->text('exclude_emails')->nullable();
            $table->boolean('schedule_enabled')->default(false);
            $table->dateTime('scheduled_at')->nullable();
            $table->string('mail_type', 30)->default('bulk');
            $table->string('subject', 200)->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 30)->default('DRAFT');
            $table->unsignedInteger('recipient_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
            $table->index('recipient_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};

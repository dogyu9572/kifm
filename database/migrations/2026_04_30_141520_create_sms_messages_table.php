<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_number', 30)->nullable();
            $table->string('recipient_type', 30)->default('all');
            $table->string('member_grade', 30)->nullable();
            $table->text('exclude_phones')->nullable();
            $table->boolean('schedule_enabled')->default(false);
            $table->dateTime('scheduled_at')->nullable();
            $table->string('sms_type', 10)->default('SMS');
            $table->string('subject', 200)->nullable();
            $table->text('body')->nullable();
            $table->unsignedInteger('byte_size')->default(0);
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
        Schema::dropIfExists('sms_messages');
    }
};

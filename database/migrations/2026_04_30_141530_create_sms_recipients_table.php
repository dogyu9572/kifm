<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_id')->constrained('sms_messages')->cascadeOnDelete();
            $table->string('source_type', 30)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('recipient_name', 100)->nullable();
            $table->string('recipient_phone', 30);
            $table->string('send_result', 20)->nullable();
            $table->string('error_message', 255)->nullable();
            $table->timestamps();

            $table->index(['sms_id', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_recipients');
    }
};

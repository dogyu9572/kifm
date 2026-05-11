<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_id')->constrained('mails')->cascadeOnDelete();
            $table->string('source_type', 30)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('recipient_name', 100)->nullable();
            $table->string('recipient_email', 255);
            $table->timestamps();

            $table->index(['mail_id', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_recipients');
    }
};

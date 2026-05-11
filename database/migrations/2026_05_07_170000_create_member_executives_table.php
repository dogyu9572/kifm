<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_executives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->string('executive_role', 40);
            $table->date('term_start_date');
            $table->date('term_end_date')->nullable();
            $table->boolean('is_indefinite')->default(false);
            $table->string('note', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['member_id', 'is_active']);
            $table->index(['executive_role', 'is_active']);
            $table->index('term_start_date');
            $table->index('term_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_executives');
    }
};


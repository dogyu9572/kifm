<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certified_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->date('validity_start_date');
            $table->date('validity_end_date');
            $table->date('acquired_date');
            $table->date('acquired_validity_start');
            $table->date('acquired_validity_end');
            $table->boolean('winter_course_completed')->default(false);
            $table->boolean('exam_passed')->default(false);
            $table->timestamps();

            $table->index('validity_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_members');
    }
};


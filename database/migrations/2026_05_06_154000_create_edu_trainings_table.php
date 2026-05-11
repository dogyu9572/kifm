<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edu_trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('season', 20);
            $table->string('title', 200);
            $table->string('round_type', 50)->nullable();
            $table->string('training_method', 20);
            $table->string('status', 20);
            $table->timestamps();

            $table->index('year');
            $table->index('season');
            $table->index('training_method');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_trainings');
    }
};

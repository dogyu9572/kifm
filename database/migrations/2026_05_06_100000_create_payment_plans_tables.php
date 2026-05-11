<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name', 200);
            $table->string('category', 30);
            $table->string('member_status', 20);
            $table->string('executive', 20)->nullable();
            $table->unsignedInteger('price_early')->nullable();
            $table->unsignedInteger('price_site')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->string('use_status', 20);
            $table->timestamps();

            $table->unique(['category', 'plan_name']);
        });

        Schema::create('payment_plan_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('payment_plans')->cascadeOnDelete();
            $table->string('grade', 20);
            $table->timestamps();

            $table->unique(['plan_id', 'grade']);
        });

        Schema::create('payment_plan_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('payment_plans')->cascadeOnDelete();
            $table->string('member_type', 20);
            $table->timestamps();

            $table->unique(['plan_id', 'member_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plan_types');
        Schema::dropIfExists('payment_plan_grades');
        Schema::dropIfExists('payment_plans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('status', 20)->default('active');
            $table->string('phone', 30);
            $table->string('distance', 100)->nullable();
            $table->string('address');
            $table->string('address_detail')->nullable();
            $table->string('homepage_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['status', 'sort_order'], 'academic_hotels_status_sort_idx');
            $table->index('name', 'academic_hotels_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_hotels');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('society_executives', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('group_no')->default(1);
            $table->string('position', 100);
            $table->string('name', 100);
            $table->string('organization', 200);
            $table->string('email', 150)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('note')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group_no', 'sort_order']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('society_executives');
    }
};

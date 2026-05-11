<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('edu_training_rounds') && ! Schema::hasColumn('edu_training_rounds', 'grade_prices')) {
            Schema::table('edu_training_rounds', function (Blueprint $table) {
                $table->json('grade_prices')->nullable()->after('score');
            });
        }

        if (! Schema::hasTable('edu_training_attachments')) {
            Schema::create('edu_training_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('edu_training_id')->constrained('edu_trainings')->cascadeOnDelete();
                $table->string('file_path');
                $table->string('original_name', 255)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['edu_training_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_training_attachments');

        if (Schema::hasTable('edu_training_rounds') && Schema::hasColumn('edu_training_rounds', 'grade_prices')) {
            Schema::table('edu_training_rounds', function (Blueprint $table) {
                $table->dropColumn('grade_prices');
            });
        }
    }
};

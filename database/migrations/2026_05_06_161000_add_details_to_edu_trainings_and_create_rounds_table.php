<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('edu_trainings', 'use_round')
            || ! Schema::hasColumn('edu_trainings', 'overview')
            || ! Schema::hasColumn('edu_trainings', 'registration_info')
            || ! Schema::hasColumn('edu_trainings', 'introduction')
            || ! Schema::hasColumn('edu_trainings', 'textbook_file_path')) {
            Schema::table('edu_trainings', function (Blueprint $table) {
                if (! Schema::hasColumn('edu_trainings', 'use_round')) {
                    $table->boolean('use_round')->default(true)->after('title');
                }
                if (! Schema::hasColumn('edu_trainings', 'overview')) {
                    $table->text('overview')->nullable()->after('status');
                }
                if (! Schema::hasColumn('edu_trainings', 'registration_info')) {
                    $table->text('registration_info')->nullable()->after('overview');
                }
                if (! Schema::hasColumn('edu_trainings', 'introduction')) {
                    $table->text('introduction')->nullable()->after('registration_info');
                }
                if (! Schema::hasColumn('edu_trainings', 'textbook_file_path')) {
                    $table->string('textbook_file_path')->nullable()->after('introduction');
                }
            });
        }

        if (! Schema::hasTable('edu_training_rounds')) {
            Schema::create('edu_training_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('edu_training_id')->constrained('edu_trainings')->cascadeOnDelete();
                $table->unsignedSmallInteger('round_order');
                $table->string('round_label', 30);
                $table->string('training_method', 20);
                $table->date('lecture_date');
                $table->string('location_link', 255);
                $table->date('apply_start_date')->nullable();
                $table->date('apply_end_date')->nullable();
                $table->unsignedInteger('capacity')->nullable();
                $table->boolean('is_capacity_unlimited')->default(false);
                $table->decimal('duration_hours', 5, 1)->default(0);
                $table->decimal('score', 5, 1)->default(0);
                $table->string('status', 20)->default('PUBLIC');
                $table->timestamps();

                $table->unique(['edu_training_id', 'round_order']);
                $table->index(['training_method', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_training_rounds');

        if (Schema::hasTable('edu_trainings')) {
            Schema::table('edu_trainings', function (Blueprint $table) {
                $dropColumns = [];
                foreach (['use_round', 'overview', 'registration_info', 'introduction', 'textbook_file_path'] as $column) {
                    if (Schema::hasColumn('edu_trainings', $column)) {
                        $dropColumns[] = $column;
                    }
                }
                if (count($dropColumns) > 0) {
                    $table->dropColumn($dropColumns);
                }
            });
        }
    }
};

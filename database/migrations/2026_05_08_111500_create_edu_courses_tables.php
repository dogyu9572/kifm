<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edu_courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_type', 30);
            $table->unsignedSmallInteger('open_year');
            $table->unsignedBigInteger('linked_event_id')->nullable();
            $table->string('keywords', 500)->nullable();
            $table->string('topics', 500)->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('title');
            $table->string('professor_name');
            $table->string('professor_org')->nullable();
            $table->unsignedBigInteger('professor_member_id')->nullable();
            $table->text('topic_detail')->nullable();
            $table->longText('content')->nullable();
            $table->string('lecture_file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedSmallInteger('duration_min')->default(0);
            $table->unsignedInteger('duration_sec')->default(0);
            $table->unsignedSmallInteger('completion_score')->default(0);
            $table->string('annual_fee_target', 20)->default('all');
            $table->string('free_yn', 1)->default('N');
            $table->date('free_start_date')->nullable();
            $table->date('free_end_date')->nullable();
            $table->string('period_type', 20)->default('days');
            $table->unsignedSmallInteger('duration_days')->nullable()->default(30);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('exam_yn', 1)->default('N');
            $table->string('expose_yn', 1)->default('Y');
            $table->string('use_yn', 1)->default('Y');
            $table->timestamps();

            $table->index(['course_type', 'open_year']);
            $table->index(['use_yn', 'expose_yn']);
            $table->index('professor_member_id');
        });

        Schema::create('edu_course_grade_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edu_course_id')->constrained('edu_courses')->cascadeOnDelete();
            $table->string('grade_code', 20);
            $table->boolean('is_enabled')->default(false);
            $table->unsignedInteger('price')->nullable();
            $table->timestamps();

            $table->unique(['edu_course_id', 'grade_code']);
        });

        Schema::create('edu_course_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edu_course_id')->constrained('edu_courses')->cascadeOnDelete();
            $table->text('question');
            $table->json('choices_json')->nullable();
            $table->unsignedTinyInteger('answer_index')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('edu_course_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edu_course_id')->constrained('edu_courses')->cascadeOnDelete();
            $table->unsignedBigInteger('link_event_id')->nullable();
            $table->unsignedBigInteger('link_training_id')->nullable();
            $table->string('link_round_use', 1)->default('N');
            $table->json('link_round_ids')->nullable();
            $table->string('link_period_type', 20)->default('days');
            $table->unsignedSmallInteger('link_duration_days')->nullable()->default(30);
            $table->date('link_period_start')->nullable();
            $table->date('link_period_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_course_links');
        Schema::dropIfExists('edu_course_exam_questions');
        Schema::dropIfExists('edu_course_grade_prices');
        Schema::dropIfExists('edu_courses');
    }
};

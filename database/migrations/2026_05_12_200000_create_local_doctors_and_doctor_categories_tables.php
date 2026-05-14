<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 우리동네주치의: 주치의(local_doctors), 진료 과목(doctor_categories), 피벗.
     */
    public function up(): void
    {
        Schema::create('doctor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('local_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('allow_member_edit')->default(true);
            $table->string('photo_path')->nullable();
            $table->string('doctor_name', 100);
            $table->string('license_no', 50)->nullable();
            $table->longText('introduction')->nullable();
            $table->string('hospital_name', 200);
            $table->string('sido', 80)->nullable();
            $table->string('sigungu', 120)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('address_detail', 200)->nullable();
            $table->string('homepage', 500)->nullable();
            $table->string('phone', 80)->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('view_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('legacy_post_id')->nullable()->unique();
            $table->json('legacy_csv_extras')->nullable();
            $table->json('functional_tests_selected')->nullable();
            $table->json('treatment_areas_selected')->nullable();
            $table->text('other_symptoms')->nullable();
            $table->text('diseases_text')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_category_local_doctor', function (Blueprint $table) {
            $table->foreignId('local_doctor_id')->constrained('local_doctors')->cascadeOnDelete();
            $table->foreignId('doctor_category_id')->constrained('doctor_categories')->cascadeOnDelete();
            $table->primary(['local_doctor_id', 'doctor_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_category_local_doctor');
        Schema::dropIfExists('local_doctors');
        Schema::dropIfExists('doctor_categories');
    }
};

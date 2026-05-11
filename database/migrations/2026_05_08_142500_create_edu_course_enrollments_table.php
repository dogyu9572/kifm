<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('edu_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edu_course_id')->constrained('edu_courses')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('member_name', 100);
            $table->string('member_grade_at', 30)->nullable();

            $table->string('enrollment_status', 30)->default('payment_pending');
            $table->unsignedTinyInteger('progress_rate')->default(0);
            $table->string('exam_status', 30)->nullable();
            $table->unsignedTinyInteger('exam_score')->nullable();
            $table->unsignedInteger('total_study_min')->default(0);
            $table->dateTime('last_studied_at')->nullable();

            $table->string('certificate_status', 30)->default('not_issued');
            $table->dateTime('certificate_issued_at')->nullable();

            $table->string('payment_no', 40)->nullable();
            $table->string('payment_status', 30)->default('pending');
            $table->string('payment_method', 20)->nullable();
            $table->string('payment_item_name', 200)->nullable();
            $table->unsignedInteger('payment_amount')->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->string('bank_depositor', 100)->nullable();
            $table->date('bank_deposit_date')->nullable();

            $table->string('receipt_issue', 3)->default('NO');
            $table->string('receipt_type', 30)->nullable();
            $table->string('receipt_number', 100)->nullable();

            $table->string('refund_bank', 100)->nullable();
            $table->string('refund_account', 100)->nullable();
            $table->string('refund_holder', 100)->nullable();

            $table->text('admin_memo')->nullable();
            $table->dateTime('applied_at')->nullable();
            $table->dateTime('expire_at')->nullable();

            $table->timestamps();

            $table->index(['edu_course_id', 'enrollment_status'], 'idx_edu_course_enrollment_course_status');
            $table->index(['member_id', 'applied_at'], 'idx_edu_course_enrollment_member_applied');
            $table->index('payment_status', 'idx_edu_course_enrollment_payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_course_enrollments');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edu_training_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 30)->unique();
            $table->foreignId('edu_training_id')->constrained('edu_trainings')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 100);
            $table->string('license_no', 80)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('reg_type', 20)->default('pre');
            $table->string('payment_method', 20)->default('card');
            $table->string('payment_status', 30)->default('pending_payment');
            $table->unsignedInteger('total_amount')->default(0);
            $table->dateTime('registered_at');
            $table->dateTime('applied_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('bank_depositor', 100)->nullable();
            $table->date('bank_deposit_date')->nullable();
            $table->text('admin_memo')->nullable();
            $table->string('receipt_issue', 10)->default('NO');
            $table->string('receipt_type', 20)->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->string('refund_bank', 100)->nullable();
            $table->string('refund_account', 100)->nullable();
            $table->string('refund_holder', 100)->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['edu_training_id', 'payment_status']);
            $table->index(['reg_type', 'registered_at']);
        });

        Schema::create('edu_training_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edu_training_payment_id')->constrained('edu_training_payments')->cascadeOnDelete();
            $table->foreignId('payment_plan_id')->nullable()->constrained('payment_plans')->nullOnDelete();
            $table->string('item_name', 150);
            $table->string('category', 50)->nullable();
            $table->string('member_scope', 20)->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_training_payment_items');
        Schema::dropIfExists('edu_training_payments');
    }
};


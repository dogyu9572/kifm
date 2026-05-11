<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no', 50)->unique();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_plan_id')->nullable()->constrained('payment_plans')->nullOnDelete();
            $table->unsignedInteger('amount')->default(0);
            $table->string('member_grade', 20)->nullable();
            $table->string('payment_method', 20)->default('bank_transfer'); // card | bank_transfer
            $table->string('payment_status', 20)->default('pending'); // pending | completed | cancelled
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('depositor_name', 100)->nullable();

            // 현금영수증
            $table->string('receipt_issue', 10)->default('NO'); // NO | YES
            $table->string('receipt_type', 20)->nullable(); // PERSONAL | CARD
            $table->string('receipt_number', 120)->nullable();

            // 환불 정보
            $table->string('refund_bank_name', 100)->nullable();
            $table->string('refund_account_no', 120)->nullable();
            $table->string('refund_holder_name', 120)->nullable();

            $table->json('legacy_import_json')->nullable();
            $table->timestamps();

            $table->index(['payment_status', 'requested_at']);
            $table->index(['member_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_payments');
    }
};


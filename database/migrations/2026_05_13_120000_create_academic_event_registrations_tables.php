<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_reg_evt_fk')
                ->cascadeOnDelete();
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('users', 'id', 'ae_reg_mbr_fk')
                ->nullOnDelete();
            $table->string('registration_no', 40)->unique();
            $table->string('legacy_unique_no', 40)->nullable();
            $table->json('source_row_json')->nullable();
            $table->string('reg_type', 20)->default('pre');
            $table->string('payment_method', 20)->default('bank_transfer');
            $table->string('payment_status', 30)->default('pending_payment');
            $table->unsignedInteger('total_amount')->default(0);
            $table->string('name', 100);
            $table->string('license_no', 80)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->dateTime('registered_at');
            $table->dateTime('applied_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('bank_depositor', 100)->nullable();
            $table->date('bank_deposit_date')->nullable();
            $table->text('bank_account_text')->nullable();
            $table->text('admin_memo')->nullable();
            $table->string('receipt_issue', 10)->default('NO');
            $table->string('receipt_type', 20)->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->string('refund_bank', 100)->nullable();
            $table->string('refund_account', 100)->nullable();
            $table->string('refund_holder', 100)->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['academic_event_id', 'payment_status'], 'ae_reg_evt_status_idx');
            $table->index(['academic_event_id', 'registered_at'], 'ae_reg_evt_reg_at_idx');
            $table->unique(['academic_event_id', 'legacy_unique_no'], 'ae_reg_evt_legacy_uq');
        });

        Schema::create('academic_event_registration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_registration_id')
                ->constrained('academic_event_registrations', 'id', 'ae_reg_item_reg_fk')
                ->cascadeOnDelete();
            $table->foreignId('payment_plan_id')
                ->nullable()
                ->constrained('payment_plans', 'id', 'ae_reg_item_plan_fk')
                ->nullOnDelete();
            $table->string('item_name', 255);
            $table->string('category', 50)->nullable();
            $table->string('member_scope', 20)->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_event_registration_items');
        Schema::dropIfExists('academic_event_registrations');
    }
};

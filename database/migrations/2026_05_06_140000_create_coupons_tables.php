<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_name', 200);
            $table->string('coupon_code', 50);
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 10, 2);
            $table->date('valid_from');
            $table->date('valid_to');
            $table->unsignedInteger('usage_count')->default(0);
            $table->string('status', 20);
            $table->timestamps();

            $table->unique('coupon_code');
        });

        Schema::create('coupon_payment_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->string('payment_category', 50);
            $table->timestamps();

            $table->unique(['coupon_id', 'payment_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_payment_categories');
        Schema::dropIfExists('coupons');
    }
};

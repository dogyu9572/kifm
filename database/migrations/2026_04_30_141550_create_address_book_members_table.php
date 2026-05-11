<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_book_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_book_id')->constrained('address_books')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 100);
            $table->string('login_id', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('source_type', 20)->default('SEARCH');
            $table->timestamps();

            $table->index(['address_book_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_book_members');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certified_member_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_member_id')->constrained('certified_members')->cascadeOnDelete();
            $table->unsignedInteger('renewal_seq');
            $table->date('renewal_date');
            $table->date('renewal_validity_start');
            $table->date('renewal_validity_end');
            $table->unsignedTinyInteger('attendance_general')->default(0);
            $table->unsignedTinyInteger('attendance_winter')->default(0);
            $table->timestamps();

            $table->unique(['certified_member_id', 'renewal_seq']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_member_renewals');
    }
};


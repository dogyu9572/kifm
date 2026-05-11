<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('board_annual_schedule');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // board 기반으로 되돌리는 경우에만 수동으로 복구합니다.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('annual_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_single_day')->default(false);
            $table->text('content')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index('is_visible');
            $table->index(['start_date', 'end_date']);
        });

        if (! Schema::hasTable('board_annual_schedule')) {
            return;
        }

        $legacyRows = DB::table('board_annual_schedule')
            ->select(['title', 'content', 'custom_fields', 'is_active', 'sort_order', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->get();

        foreach ($legacyRows as $row) {
            $customFields = json_decode((string) ($row->custom_fields ?? '{}'), true) ?: [];
            $startDate = $customFields['schedule_start_date'] ?? null;
            $endDate = $customFields['schedule_end_date'] ?? null;
            $isSingleDay = $customFields['schedule_single_day'] ?? null;

            if (! $startDate) {
                $startDate = optional($row->created_at)->format('Y-m-d') ?? now()->toDateString();
            }

            if (! $endDate) {
                $endDate = $startDate;
            }

            DB::table('annual_schedules')->insert([
                'title' => (string) $row->title,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_single_day' => in_array($isSingleDay, ['1', 1, true, 'Y'], true),
                'content' => $row->content,
                'is_visible' => (bool) ($row->is_active ?? true),
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_schedules');
    }
};

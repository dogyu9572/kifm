<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_event_session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_session_id')
                ->constrained('academic_event_sessions', 'id', 'ae_sess_items_session_fk')
                ->cascadeOnDelete();
            $table->foreignId('academic_event_abstract_id')
                ->nullable()
                ->constrained('academic_event_abstracts', 'id', 'ae_sess_items_abs_fk')
                ->nullOnDelete();
            $table->string('row_type', 20)->default('abstract');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('title', 500)->nullable();
            $table->string('presenter', 150)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['academic_event_session_id', 'sort_order'], 'ae_sess_items_session_sort_idx');
            $table->index(['academic_event_session_id', 'start_time'], 'ae_sess_items_session_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_event_session_items');
    }
};

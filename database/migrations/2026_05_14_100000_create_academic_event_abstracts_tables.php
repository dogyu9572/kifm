<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_event_abstracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'aea_evt_fk')
                ->cascadeOnDelete();
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('users', 'id', 'aea_usr_fk')
                ->nullOnDelete();
            $table->string('registered_by', 20);
            $table->string('status', 20);
            $table->string('file_receipt_status', 30);
            $table->string('author_name', 100);
            $table->string('author_name_en', 100)->nullable();
            $table->string('author_phone', 30)->nullable();
            $table->string('author_mobile', 30)->nullable();
            $table->string('author_email', 200)->nullable();
            $table->string('title', 500);
            $table->string('presentation_type', 20);
            $table->foreignId('academic_event_field_id')
                ->nullable()
                ->constrained('academic_event_fields', 'id', 'aea_field_fk')
                ->nullOnDelete();
            $table->text('note')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->index(['academic_event_id', 'status'], 'aea_evt_status_idx');
            $table->index(['academic_event_id', 'presentation_type'], 'aea_evt_pres_idx');
            $table->index(['academic_event_id', 'submitted_at'], 'aea_evt_submitted_idx');
        });

        Schema::create('academic_event_abstract_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_abstract_id')
                ->constrained('academic_event_abstracts', 'id', 'aeaf_abs_fk')
                ->cascadeOnDelete();
            $table->string('original_name', 255);
            $table->string('stored_path', 500);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();

            $table->index('academic_event_abstract_id', 'aeaf_abs_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_event_abstract_files');
        Schema::dropIfExists('academic_event_abstracts');
    }
};

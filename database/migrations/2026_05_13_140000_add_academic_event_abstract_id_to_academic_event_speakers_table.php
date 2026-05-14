<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_event_speakers', function (Blueprint $table) {
            if (! Schema::hasColumn('academic_event_speakers', 'academic_event_abstract_id')) {
                $table->foreignId('academic_event_abstract_id')
                    ->nullable()
                    ->after('member_id')
                    ->constrained('academic_event_abstracts', 'id', 'ae_spk_abs_fk')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('academic_event_speakers', function (Blueprint $table) {
            if (Schema::hasColumn('academic_event_speakers', 'academic_event_abstract_id')) {
                $table->dropForeign(['academic_event_abstract_id']);
                $table->dropColumn('academic_event_abstract_id');
            }
        });
    }
};

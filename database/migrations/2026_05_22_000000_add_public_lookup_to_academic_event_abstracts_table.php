<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_event_abstracts', function (Blueprint $table) {
            if (! Schema::hasColumn('academic_event_abstracts', 'abstract_no')) {
                $table->string('abstract_no', 80)->nullable()->after('member_id');
            }
            if (! Schema::hasColumn('academic_event_abstracts', 'lookup_password')) {
                $table->string('lookup_password')->nullable()->after('abstract_no');
            }
        });

        $events = DB::table('academic_events')->get(['id', 'folder_name']);
        foreach ($events as $event) {
            $rows = DB::table('academic_event_abstracts')
                ->where('academic_event_id', $event->id)
                ->orderBy('submitted_at')
                ->orderBy('id')
                ->get(['id', 'abstract_no']);

            $seq = 1;
            foreach ($rows as $row) {
                if (! empty($row->abstract_no)) {
                    if (preg_match('/-(\d+)$/', (string) $row->abstract_no, $m)) {
                        $seq = max($seq, ((int) $m[1]) + 1);
                    }
                    continue;
                }

                DB::table('academic_event_abstracts')
                    ->where('id', $row->id)
                    ->update([
                        'abstract_no' => sprintf('%s-ABS-%05d', $event->folder_name, $seq),
                    ]);
                $seq++;
            }
        }

        Schema::table('academic_event_abstracts', function (Blueprint $table) {
            $table->unique('abstract_no', 'aea_abstract_no_unique');
        });
    }

    public function down(): void
    {
        Schema::table('academic_event_abstracts', function (Blueprint $table) {
            if (Schema::hasColumn('academic_event_abstracts', 'abstract_no')) {
                $table->dropUnique('aea_abstract_no_unique');
            }
        });

        Schema::table('academic_event_abstracts', function (Blueprint $table) {
            if (Schema::hasColumn('academic_event_abstracts', 'lookup_password')) {
                $table->dropColumn('lookup_password');
            }
            if (Schema::hasColumn('academic_event_abstracts', 'abstract_no')) {
                $table->dropColumn('abstract_no');
            }
        });
    }
};

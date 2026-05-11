<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('community_committee_applications')) {
            Schema::create('community_committee_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('community_committee_id')->constrained('community_committees')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('applicant_name', 100);
                $table->string('email', 150)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('status', 20)->default('PENDING');
                $table->text('reject_reason')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        $databaseName = DB::getDatabaseName();
        $tableName = 'community_committee_applications';

        $addIndexIfMissing = static function (string $indexName, string $indexSql) use ($databaseName, $tableName): void {
            $exists = DB::table('information_schema.statistics')
                ->where('table_schema', $databaseName)
                ->where('table_name', $tableName)
                ->where('index_name', $indexName)
                ->exists();

            if (! $exists) {
                DB::statement($indexSql);
            }
        };

        $addIndexIfMissing(
            'cca_committee_status_idx',
            'CREATE INDEX cca_committee_status_idx ON community_committee_applications (community_committee_id, status)'
        );
        $addIndexIfMissing(
            'cca_status_applied_idx',
            'CREATE INDEX cca_status_applied_idx ON community_committee_applications (status, applied_at)'
        );
        $addIndexIfMissing(
            'cca_user_status_idx',
            'CREATE INDEX cca_user_status_idx ON community_committee_applications (user_id, status)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('community_committee_applications');
    }
};

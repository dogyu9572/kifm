<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('society_executives')) {
            Schema::table('society_executives', function (Blueprint $table): void {
                if (! Schema::hasColumn('society_executives', 'name_en')) {
                    $table->string('name_en', 100)->nullable()->after('name');
                }
                if (! Schema::hasColumn('society_executives', 'position_en')) {
                    $table->string('position_en', 100)->nullable()->after('position');
                }
                if (! Schema::hasColumn('society_executives', 'organization_en')) {
                    $table->string('organization_en', 200)->nullable()->after('organization');
                }
            });
        }

        if (Schema::hasTable('academic_events')) {
            Schema::table('academic_events', function (Blueprint $table): void {
                if (! Schema::hasColumn('academic_events', 'title_en')) {
                    $table->string('title_en')->nullable()->after('title');
                }
            });
        }

        if (Schema::hasTable('board_academic_journals')) {
            Schema::table('board_academic_journals', function (Blueprint $table): void {
                if (! Schema::hasColumn('board_academic_journals', 'title_en')) {
                    $table->string('title_en')->nullable()->after('title');
                }
            });
        }

        foreach (['board_member_square_notices', 'board_society_history'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'title_en')) {
                    $table->string('title_en')->nullable()->after('title');
                }
                if (! Schema::hasColumn($tableName, 'content_en')) {
                    $table->text('content_en')->nullable()->after('content');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['board_member_square_notices', 'board_society_history'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'content_en')) {
                    $table->dropColumn('content_en');
                }
                if (Schema::hasColumn($tableName, 'title_en')) {
                    $table->dropColumn('title_en');
                }
            });
        }

        if (Schema::hasTable('board_academic_journals')) {
            Schema::table('board_academic_journals', function (Blueprint $table): void {
                if (Schema::hasColumn('board_academic_journals', 'title_en')) {
                    $table->dropColumn('title_en');
                }
            });
        }

        if (Schema::hasTable('academic_events')) {
            Schema::table('academic_events', function (Blueprint $table): void {
                if (Schema::hasColumn('academic_events', 'title_en')) {
                    $table->dropColumn('title_en');
                }
            });
        }

        if (Schema::hasTable('society_executives')) {
            Schema::table('society_executives', function (Blueprint $table): void {
                foreach (['organization_en', 'position_en', 'name_en'] as $column) {
                    if (Schema::hasColumn('society_executives', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

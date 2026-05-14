<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 레거시 CSV에 행사 식별자가 없는 데이터 이관을 위해 academic_event_id 와 registration_no 를 nullable 로 변경한다.
        Schema::table('academic_event_registrations', function (Blueprint $table) {
            $table->dropForeign('ae_reg_evt_fk');
        });

        Schema::table('academic_event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_event_id')->nullable()->change();
            $table->string('registration_no', 40)->nullable()->change();
        });

        Schema::table('academic_event_registrations', function (Blueprint $table) {
            $table->foreign('academic_event_id', 'ae_reg_evt_fk')
                ->references('id')->on('academic_events')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // 데이터 정합성상 NULL 행을 NOT NULL 로 되돌릴 수 없으므로 down 은 컬럼 정의만 환원한다.
        Schema::table('academic_event_registrations', function (Blueprint $table) {
            $table->dropForeign('ae_reg_evt_fk');
        });

        Schema::table('academic_event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_event_id')->nullable(false)->change();
            $table->string('registration_no', 40)->nullable(false)->change();
        });

        Schema::table('academic_event_registrations', function (Blueprint $table) {
            $table->foreign('academic_event_id', 'ae_reg_evt_fk')
                ->references('id')->on('academic_events')
                ->cascadeOnDelete();
        });
    }
};

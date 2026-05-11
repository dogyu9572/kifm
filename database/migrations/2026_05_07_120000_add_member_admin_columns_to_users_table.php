<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('member_level', 32)->nullable()->after('join_type')->comment('pending|associate|regular|lifetime|senior');
            $table->string('job_type', 32)->nullable()->after('member_level')->comment('전문의 등 구분 코드');
            $table->string('member_status_raw', 255)->nullable()->after('job_type')->comment('CSV 회원상태 원문');
            $table->string('name_en', 100)->nullable()->after('name');
            $table->string('license_number', 80)->nullable()->after('contact');
            $table->string('specialist_number', 80)->nullable()->after('license_number');
            $table->string('specialty', 120)->nullable()->after('specialist_number');
            $table->string('medical_department', 100)->nullable()->after('specialty');
            $table->string('workplace_name', 200)->nullable()->after('medical_department');
            $table->string('workplace_phone', 40)->nullable()->after('workplace_name');
            $table->string('workplace_zipcode', 20)->nullable()->after('workplace_phone');
            $table->string('workplace_address')->nullable()->after('workplace_zipcode');
            $table->string('workplace_address_detail')->nullable()->after('workplace_address');
            $table->unsignedSmallInteger('graduate_year')->nullable()->after('school_name');
            $table->date('membership_fee_basis_at')->nullable()->after('graduate_year')->comment('회비납부기준일');
            $table->string('annual_fee_status', 20)->nullable()->after('membership_fee_basis_at')->comment('none|paid|unpaid');
            $table->boolean('certified_instructor')->default(false)->after('annual_fee_status');
            $table->json('committee_codes')->nullable()->after('certified_instructor');
            $table->json('legacy_import_json')->nullable()->after('committee_codes');
            $table->unsignedInteger('legacy_csv_no')->nullable()->after('legacy_import_json');

            $table->index(['role', 'withdrawn_at', 'member_level'], 'users_role_withdrawn_level_idx');
            $table->index('last_login_at');
            $table->index('membership_fee_basis_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_withdrawn_level_idx');
            $table->dropIndex(['last_login_at']);
            $table->dropIndex(['membership_fee_basis_at']);

            $table->dropColumn([
                'member_level',
                'job_type',
                'member_status_raw',
                'name_en',
                'license_number',
                'specialist_number',
                'specialty',
                'medical_department',
                'workplace_name',
                'workplace_phone',
                'workplace_zipcode',
                'workplace_address',
                'workplace_address_detail',
                'graduate_year',
                'membership_fee_basis_at',
                'annual_fee_status',
                'certified_instructor',
                'committee_codes',
                'legacy_import_json',
                'legacy_csv_no',
            ]);
        });
    }
};

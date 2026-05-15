<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('doctor_categories')) {
            Schema::create('doctor_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('doctor_category_local_doctor')) {
            Schema::create('doctor_category_local_doctor', function (Blueprint $table) {
                $table->foreignId('local_doctor_id')->constrained('local_doctors')->cascadeOnDelete();
                $table->foreignId('doctor_category_id')->constrained('doctor_categories')->cascadeOnDelete();
                $table->primary(['local_doctor_id', 'doctor_category_id']);
            });
        }

        $this->migrateFromCategoryPivot();

        if (Schema::hasTable('category_local_doctor')) {
            Schema::dropIfExists('category_local_doctor');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('category_local_doctor')) {
            Schema::create('category_local_doctor', function (Blueprint $table) {
                $table->foreignId('local_doctor_id')->constrained('local_doctors')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->primary(['local_doctor_id', 'category_id']);
            });
        }

        if (Schema::hasTable('doctor_category_local_doctor')) {
            Schema::dropIfExists('doctor_category_local_doctor');
        }
        if (Schema::hasTable('doctor_categories')) {
            Schema::dropIfExists('doctor_categories');
        }
    }

    private function migrateFromCategoryPivot(): void
    {
        if (! Schema::hasTable('category_local_doctor') || ! Schema::hasTable('categories')) {
            return;
        }

        $pivotRows = DB::table('category_local_doctor')->get();
        if ($pivotRows->isEmpty()) {
            return;
        }

        $categoryNames = DB::table('categories')
            ->whereIn('id', $pivotRows->pluck('category_id')->unique())
            ->pluck('name', 'id');

        $categoryIdToDoctorCategoryId = [];

        foreach ($categoryNames as $categoryId => $name) {
            $existing = DB::table('doctor_categories')->where('name', $name)->first();
            if ($existing) {
                $categoryIdToDoctorCategoryId[$categoryId] = $existing->id;

                continue;
            }

            $categoryIdToDoctorCategoryId[$categoryId] = DB::table('doctor_categories')->insertGetId([
                'name' => $name,
                'sort_order' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($pivotRows as $row) {
            $doctorCategoryId = $categoryIdToDoctorCategoryId[$row->category_id] ?? null;
            if ($doctorCategoryId === null) {
                continue;
            }

            DB::table('doctor_category_local_doctor')->insertOrIgnore([
                'local_doctor_id' => $row->local_doctor_id,
                'doctor_category_id' => $doctorCategoryId,
            ]);
        }
    }
};

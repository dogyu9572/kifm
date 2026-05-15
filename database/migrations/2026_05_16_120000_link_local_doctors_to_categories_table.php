<?php

use App\Models\Category;
use App\Support\LocalDoctorCategories;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('category_local_doctor')) {
            Schema::create('category_local_doctor', function (Blueprint $table) {
                $table->foreignId('local_doctor_id')->constrained('local_doctors')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->primary(['local_doctor_id', 'category_id']);
            });
        }

        $this->migrateLegacyDoctorCategories();

        if (Schema::hasTable('doctor_category_local_doctor')) {
            Schema::dropIfExists('doctor_category_local_doctor');
        }
        if (Schema::hasTable('doctor_categories')) {
            Schema::dropIfExists('doctor_categories');
        }
    }

    public function down(): void
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

        Schema::dropIfExists('category_local_doctor');
    }

    private function migrateLegacyDoctorCategories(): void
    {
        if (! Schema::hasTable('doctor_category_local_doctor') || ! Schema::hasTable('doctor_categories')) {
            return;
        }

        $groupId = LocalDoctorCategories::groupId();
        if ($groupId === null) {
            return;
        }

        $legacyRows = DB::table('doctor_categories')->get();
        $nameToCategoryId = Category::query()
            ->where('parent_id', $groupId)
            ->pluck('id', 'name');

        foreach ($legacyRows as $legacy) {
            $categoryId = $nameToCategoryId[$legacy->name] ?? null;
            if ($categoryId === null) {
                $categoryId = Category::query()->create([
                    'parent_id' => $groupId,
                    'code' => null,
                    'name' => $legacy->name,
                    'depth' => 1,
                    'display_order' => (int) ($legacy->sort_order ?? 0),
                    'is_active' => ($legacy->status ?? 'active') === 'active',
                ])->id;
                $nameToCategoryId[$legacy->name] = $categoryId;
            }

            $doctorIds = DB::table('doctor_category_local_doctor')
                ->where('doctor_category_id', $legacy->id)
                ->pluck('local_doctor_id');

            foreach ($doctorIds as $doctorId) {
                DB::table('category_local_doctor')->insertOrIgnore([
                    'local_doctor_id' => $doctorId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
};

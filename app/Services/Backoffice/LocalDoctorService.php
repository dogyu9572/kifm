<?php

namespace App\Services\Backoffice;

use App\Models\LocalDoctor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LocalDoctorService
{
    public static function statusLabels(): array
    {
        return [
            'active' => '운영중',
            'inactive' => '미운영',
        ];
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        $query = LocalDoctor::query()->with('doctorCategories');

        if ($request->filled('sido')) {
            $sido = trim((string) $request->input('sido'));
            if ($sido !== '') {
                $norm = LocalDoctorRegionNormalizer::normalizeSido($sido);
                $sido = $norm['sido'] !== '' ? $norm['sido'] : $sido;
                $query->where('sido', $sido);
            }
        }
        if ($request->filled('sigungu')) {
            $query->where('sigungu', (string) $request->input('sigungu'));
        }
        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }
        if ($request->filled('doctor_category_id')) {
            $categoryId = (int) $request->input('doctor_category_id');
            $query->whereHas('doctorCategories', function (Builder $q) use ($categoryId): void {
                $q->where('doctor_categories.id', $categoryId);
            });
        }
        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));
            if ($keyword !== '') {
                $query->where(function (Builder $q) use ($keyword): void {
                    $q->where('hospital_name', 'like', '%' . $keyword . '%')
                        ->orWhere('doctor_name', 'like', '%' . $keyword . '%');
                });
            }
        }

        // 백오피스 목록에 정렬 UI 없음 → 최신 등록(id) 기준만 사용 (sort_order 컬럼은 이관·수정 폼 호환용으로 유지)
        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function create(Request $request, array $validated): LocalDoctor
    {
        return DB::transaction(function () use ($request, $validated) {
            $doctor = new LocalDoctor;
            $this->fillDoctor($doctor, $validated);
            if ($request->hasFile('photo')) {
                $doctor->photo_path = $this->storePhoto($request->file('photo'));
            }
            $doctor->save();
            $this->syncCategories($doctor, $validated['category_ids'] ?? []);

            return $doctor->fresh(['doctorCategories']);
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function update(Request $request, LocalDoctor $localDoctor, array $validated): void
    {
        DB::transaction(function () use ($request, $localDoctor, $validated): void {
            $this->fillDoctor($localDoctor, $validated);
            if ($request->hasFile('photo')) {
                $this->deleteStoredPhoto($localDoctor->photo_path);
                $localDoctor->photo_path = $this->storePhoto($request->file('photo'));
            } elseif ($request->boolean('delete_photo')) {
                $this->deleteStoredPhoto($localDoctor->photo_path);
                $localDoctor->photo_path = null;
            }
            $localDoctor->save();
            $this->syncCategories($localDoctor, $validated['category_ids'] ?? []);
        });
    }

    public function destroy(LocalDoctor $localDoctor): void
    {
        $this->deleteStoredPhoto($localDoctor->photo_path);
        $localDoctor->delete();
    }

    /**
     * @param  list<int>  $ids
     */
    public function deleteMany(array $ids): int
    {
        $doctors = LocalDoctor::query()->whereIn('id', $ids)->get();
        foreach ($doctors as $doctor) {
            $this->destroy($doctor);
        }

        return $doctors->count();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function fillDoctor(LocalDoctor $doctor, array $validated): void
    {
        $doctor->member_id = $validated['member_id'] ?? null;
        $doctor->allow_member_edit = (bool) ($validated['allow_member_edit'] ?? true);
        $doctor->doctor_name = (string) $validated['doctor_name'];
        $doctor->license_no = $validated['license_no'] ?? null;
        $doctor->introduction = $validated['introduction'] ?? null;
        $doctor->hospital_name = (string) $validated['hospital_name'];
        $sidoRaw = trim((string) ($validated['sido'] ?? ''));
        $norm = LocalDoctorRegionNormalizer::normalizeSido($sidoRaw);
        $doctor->sido = $norm['sido'] !== '' ? $norm['sido'] : $sidoRaw;
        $doctor->sigungu = $validated['sigungu'] ?? null;
        $doctor->address = $validated['address'] ?? null;
        $doctor->address_detail = $validated['address_detail'] ?? null;
        $doctor->homepage = $validated['homepage'] ?? null;
        $doctor->phone = $validated['phone'] ?? null;
        $doctor->status = (string) $validated['status'];
        $doctor->view_count = (int) ($validated['view_count'] ?? $doctor->view_count ?? 0);
        $doctor->sort_order = (int) ($validated['sort_order'] ?? $doctor->sort_order ?? 0);
        $doctor->functional_tests_selected = $validated['functional_tests_selected'] ?? [];
        $doctor->treatment_areas_selected = $validated['treatment_areas_selected'] ?? [];
        $doctor->other_symptoms = $validated['other_symptoms'] ?? null;
        $doctor->diseases_text = $validated['diseases_text'] ?? null;
    }

    /**
     * @param  list<int|string>  $categoryIds
     */
    protected function syncCategories(LocalDoctor $doctor, array $categoryIds): void
    {
        $ids = array_values(array_unique(array_map('intval', $categoryIds)));
        $doctor->doctorCategories()->sync($ids);
    }

    protected function storePhoto(UploadedFile $file): string
    {
        $dir = 'local_doctors/photos';

        return $file->store($dir, 'public');
    }

    protected function deleteStoredPhoto(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

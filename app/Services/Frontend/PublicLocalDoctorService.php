<?php

namespace App\Services\Frontend;

use App\Models\DoctorCategory;
use App\Models\LocalDoctor;
use App\Services\Backoffice\LocalDoctorRegionNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicLocalDoctorService
{
    public function listActiveFiltered(Request $request): Collection
    {
        $limit = (int) config('local_doctor_map.list_limit', 200);

        return $this->applyFilters(LocalDoctor::query()->with('doctorCategories'), $request)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{
     *     categories: Collection<int, DoctorCategory>,
     *     sidos: list<string>,
     *     sigungu_by_sido: array<string, list<string>>,
     *     filters: array<string, mixed>,
     *     map_local_names: array<string, string>,
     *     selected_map_index: string|null
     * }
     */
    public function filterOptions(Request $request): array
    {
        $sido = $this->normalizedSidoFromRequest($request);
        $sigunguBySido = config('local_doctor_regions.sigungu_by_sido', []);

        return [
            'categories' => DoctorCategory::query()
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'sidos' => config('local_doctor_regions.sidos', []),
            'sigungu_by_sido' => is_array($sigunguBySido) ? $sigunguBySido : [],
            'filters' => [
                'doctor_category_id' => (string) $request->input('doctor_category_id', ''),
                'sido' => $sido,
                'sigungu' => (string) $request->input('sigungu', ''),
                'keyword' => trim((string) $request->input('keyword', '')),
                'map_index' => (string) $request->input('map_index', ''),
            ],
            'map_local_names' => config('local_doctor_map.local_names', []),
            'selected_map_index' => $this->resolveMapIndexForSido($sido),
        ];
    }

    public function findActiveForPopup(int $id): ?LocalDoctor
    {
        $doctor = LocalDoctor::query()
            ->with('doctorCategories')
            ->where('status', 'active')
            ->whereKey($id)
            ->first();

        if ($doctor === null) {
            return null;
        }

        $doctor->increment('view_count');

        return $doctor->fresh(['doctorCategories']);
    }

    /**
     * @return array<string, mixed>
     */
    public function popupPayload(LocalDoctor $doctor): array
    {
        return [
            'id' => $doctor->id,
            'hospital_name' => $doctor->hospital_name,
            'doctor_name' => $doctor->doctor_name,
            'category_label' => $this->primaryCategoryLabel($doctor),
            'full_address' => $this->fullAddress($doctor),
            'phone' => $doctor->phone,
            'phone_href' => $this->phoneHref($doctor->phone),
            'homepage' => $doctor->homepage,
            'photo_url' => $this->photoUrl($doctor),
            'introduction_html' => $this->introductionText($doctor->introduction),
            'map' => [
                'lat' => $doctor->map_lat,
                'lng' => $doctor->map_lng,
                'javascript_key' => config('local_doctor_map.kakao.javascript_key'),
            ],
            'roughmap' => [
                'timestamp' => config('local_doctor_map.roughmap.timestamp'),
                'key' => config('local_doctor_map.roughmap.key'),
            ],
        ];
    }

    public function sidoForMapIndex(string $mapIndex): ?string
    {
        $index = str_pad(preg_replace('/\D/', '', $mapIndex) ?? '', 2, '0', STR_PAD_LEFT);
        $map = config('local_doctor_map.index_to_sido', []);

        return is_array($map) ? ($map[$index] ?? null) : null;
    }

    public function primaryCategoryLabel(LocalDoctor $doctor): string
    {
        $first = $doctor->doctorCategories->first();

        return $first !== null ? (string) $first->name : '';
    }

    public function fullAddress(LocalDoctor $doctor): string
    {
        $parts = array_filter([
            trim((string) ($doctor->sido ?? '')),
            trim((string) ($doctor->sigungu ?? '')),
            trim((string) ($doctor->address ?? '')),
            trim((string) ($doctor->address_detail ?? '')),
        ]);

        return implode(' ', $parts);
    }

    public function photoUrl(LocalDoctor $doctor): string
    {
        if (! $doctor->photo_path) {
            return asset('images/img_sample_doctor.jpg');
        }

        if (str_starts_with((string) $doctor->photo_path, 'http')) {
            return (string) $doctor->photo_path;
        }

        return Storage::disk('public')->url($doctor->photo_path);
    }

    protected function applyFilters(Builder $query, Request $request): Builder
    {
        $sido = $this->normalizedSidoFromRequest($request);
        if ($sido !== '') {
            $query->where('sido', $sido);
        }

        if ($request->filled('sigungu')) {
            $query->where('sigungu', (string) $request->input('sigungu'));
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

        return $query;
    }

    protected function normalizedSidoFromRequest(Request $request): string
    {
        if ($request->filled('map_index')) {
            $fromMap = $this->sidoForMapIndex((string) $request->input('map_index'));
            if ($fromMap !== null && $fromMap !== '') {
                return $fromMap;
            }
        }

        if (! $request->filled('sido')) {
            return '';
        }

        $sido = trim((string) $request->input('sido'));
        if ($sido === '') {
            return '';
        }

        $norm = LocalDoctorRegionNormalizer::normalizeSido($sido);

        return $norm['sido'] !== '' ? $norm['sido'] : $sido;
    }

    protected function resolveMapIndexForSido(string $sido): ?string
    {
        if ($sido === '') {
            return null;
        }

        $indexMap = config('local_doctor_map.index_to_sido', []);
        if (! is_array($indexMap)) {
            return null;
        }

        foreach ($indexMap as $index => $canonical) {
            if ($canonical === $sido) {
                return (string) $index;
            }
        }

        return null;
    }

    protected function phoneHref(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return $digits !== '' ? 'tel:' . $digits : '#';
    }

    private function introductionText(?string $introduction): string
    {
        $decoded = html_entity_decode((string) $introduction, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(strip_tags($decoded));

        return preg_replace("/[ \t]+/", ' ', $text) ?? $text;
    }
}

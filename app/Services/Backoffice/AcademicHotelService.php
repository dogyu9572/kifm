<?php

namespace App\Services\Backoffice;

use App\Models\AcademicHotel;
use App\Support\BackofficeFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AcademicHotelService
{
    public const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function resolvePerPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 20);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20;
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = $this->resolvePerPage($request);
        $status = (string) $request->query('status', 'all');
        $date = $request->query('registered_date');
        $keyword = trim((string) $request->query('name', ''));

        return AcademicHotel::query()
            ->when($status === 'active', fn ($q) => $q->where('status', 'active'))
            ->when($status === 'inactive', fn ($q) => $q->where('status', 'inactive'))
            ->when($date, fn ($q) => $q->whereDate('created_at', $date))
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', '%' . $keyword . '%'))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?UploadedFile $imageFile = null): AcademicHotel
    {
        $path = null;
        if ($imageFile) {
            $path = BackofficeFile::storeWithOriginalName($imageFile, 'backoffice/academic-hotels/images', 'public');
        }

        return AcademicHotel::create([
            'name' => $data['name'],
            'status' => $data['status'],
            'phone' => $data['phone'],
            'distance' => $data['distance'] ?? null,
            'address' => $data['address'],
            'address_detail' => $data['address_detail'] ?? null,
            'homepage_url' => $data['homepage_url'] ?? null,
            'description' => $data['description'] ?? null,
            'image_path' => $path,
            'sort_order' => (int) $data['sort_order'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(AcademicHotel $hotel, array $data, ?UploadedFile $imageFile = null, bool $removeImage = false): void
    {
        $updates = [
            'name' => $data['name'],
            'status' => $data['status'],
            'phone' => $data['phone'],
            'distance' => $data['distance'] ?? null,
            'address' => $data['address'],
            'address_detail' => $data['address_detail'] ?? null,
            'homepage_url' => $data['homepage_url'] ?? null,
            'description' => $data['description'] ?? null,
        ];
        if ($imageFile) {
            if ($hotel->image_path) {
                Storage::disk('public')->delete($hotel->image_path);
            }
            $updates['image_path'] = BackofficeFile::storeWithOriginalName($imageFile, 'backoffice/academic-hotels/images', 'public');
        } elseif ($removeImage) {
            if ($hotel->image_path) {
                Storage::disk('public')->delete($hotel->image_path);
            }
            $updates['image_path'] = null;
        }
        $hotel->update($updates);
    }

    public function delete(AcademicHotel $hotel): void
    {
        if ($hotel->image_path) {
            Storage::disk('public')->delete($hotel->image_path);
        }
        $hotel->delete();
    }

    /**
     * @param  array<int|string>  $ids
     */
    public function deleteMany(array $ids): int
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if ($ids === []) {
            return 0;
        }
        $rows = AcademicHotel::query()->whereIn('id', $ids)->get();
        $count = 0;
        foreach ($rows as $hotel) {
            $this->delete($hotel);
            $count++;
        }

        return $count;
    }
}

<?php

namespace App\Services\Backoffice;

use App\Models\AcademicSponsorMaster;
use App\Support\BackofficeFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AcademicSponsorMasterService
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

        return AcademicSponsorMaster::query()
            ->when($status === 'active', fn ($q) => $q->where('status', 'active'))
            ->when($status === 'inactive', fn ($q) => $q->where('status', 'inactive'))
            ->when($date, fn ($q) => $q->whereDate('created_at', $date))
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', '%' . $keyword . '%'))
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array{name: string, status: string, sort_order: int, representative_name?: string|null}  $data
     */
    public function store(array $data, UploadedFile $logoFile): AcademicSponsorMaster
    {
        $path = BackofficeFile::storeWithOriginalName($logoFile, 'backoffice/academic-sponsor-masters/logos', 'public');

        return AcademicSponsorMaster::create([
            'name' => $data['name'],
            'representative_name' => $data['representative_name'] ?? null,
            'logo_path' => $path,
            'status' => $data['status'],
            'sort_order' => (int) $data['sort_order'],
        ]);
    }

    /**
     * @param  array{name: string, status: string, representative_name?: string|null}  $data
     */
    public function update(AcademicSponsorMaster $master, array $data, ?UploadedFile $logoFile = null, bool $removeLogo = false): void
    {
        $updates = [
            'name' => $data['name'],
            'status' => $data['status'],
        ];
        if (array_key_exists('representative_name', $data)) {
            $updates['representative_name'] = $data['representative_name'];
        }
        if ($logoFile) {
            if ($master->logo_path) {
                Storage::disk('public')->delete($master->logo_path);
            }
            $updates['logo_path'] = BackofficeFile::storeWithOriginalName($logoFile, 'backoffice/academic-sponsor-masters/logos', 'public');
        } elseif ($removeLogo) {
            if ($master->logo_path) {
                Storage::disk('public')->delete($master->logo_path);
            }
            $updates['logo_path'] = null;
        }
        $master->update($updates);
    }

    public function delete(AcademicSponsorMaster $master): void
    {
        if ($master->logo_path) {
            Storage::disk('public')->delete($master->logo_path);
        }
        $master->delete();
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
        $masters = AcademicSponsorMaster::query()->whereIn('id', $ids)->get();
        $count = 0;
        foreach ($masters as $master) {
            $this->delete($master);
            $count++;
        }

        return $count;
    }
}

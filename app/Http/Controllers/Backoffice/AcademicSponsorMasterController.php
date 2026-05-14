<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\StoreAcademicSponsorMasterPageRequest;
use App\Http\Requests\Backoffice\UpdateAcademicSponsorMasterPageRequest;
use App\Models\AcademicSponsorMaster;
use App\Services\Backoffice\AcademicSponsorMasterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicSponsorMasterController extends Controller
{
    public function __construct(
        private readonly AcademicSponsorMasterService $sponsorMasterService
    ) {}

    public function index(Request $request): View
    {
        $sponsors = $this->sponsorMasterService->paginateFiltered($request);
        $perPage = $this->sponsorMasterService->resolvePerPage($request);

        return view('backoffice.academic-sponsor-masters.index', [
            'sponsors' => $sponsors,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('backoffice.academic-sponsor-masters.create', [
            'master' => new AcademicSponsorMaster([
                'status' => 'active',
            ]),
        ]);
    }

    public function store(StoreAcademicSponsorMasterPageRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $nextOrder = (int) (AcademicSponsorMaster::query()->max('sort_order') ?? 0) + 1;
        $validated['sort_order'] = $nextOrder;
        $this->sponsorMasterService->store($validated, $request->file('logo'));

        return redirect()->route('backoffice.academic-sponsor-masters.index')
            ->with('success', '스폰서가 등록되었습니다.');
    }

    public function edit(AcademicSponsorMaster $academic_sponsor_master): View
    {
        return view('backoffice.academic-sponsor-masters.edit', [
            'master' => $academic_sponsor_master,
        ]);
    }

    public function update(UpdateAcademicSponsorMasterPageRequest $request, AcademicSponsorMaster $academic_sponsor_master): RedirectResponse
    {
        $validated = $request->validated();
        $removeLogo = (bool) ($validated['remove_logo'] ?? false);
        if ($request->hasFile('logo')) {
            $removeLogo = false;
        }
        unset($validated['remove_logo'], $validated['existing_logo']);
        $this->sponsorMasterService->update(
            $academic_sponsor_master,
            $validated,
            $request->file('logo'),
            $removeLogo
        );

        return redirect()->route('backoffice.academic-sponsor-masters.index')
            ->with('success', '스폰서가 수정되었습니다.');
    }

    public function destroy(AcademicSponsorMaster $academic_sponsor_master): RedirectResponse
    {
        $this->sponsorMasterService->delete($academic_sponsor_master);

        return redirect()->route('backoffice.academic-sponsor-masters.index')
            ->with('success', '삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sponsor_ids' => ['required', 'array'],
            'sponsor_ids.*' => ['integer', 'exists:academic_sponsor_masters,id'],
        ]);

        $deleted = $this->sponsorMasterService->deleteMany($validated['sponsor_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    public function updateSortOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'updates' => ['required', 'array'],
            'updates.*.post_id' => ['required', 'integer', 'exists:academic_sponsor_masters,id'],
            'updates.*.sort_order' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['updates'] as $update) {
                AcademicSponsorMaster::query()
                    ->where('id', (int) $update['post_id'])
                    ->update([
                        'sort_order' => (int) $update['sort_order'],
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => '정렬 순서가 저장되었습니다.',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $rows = AcademicSponsorMaster::query()
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', '%' . $keyword . '%'))
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'representative_name', 'logo_path']);

        return response()->json([
            'data' => $rows->map(fn (AcademicSponsorMaster $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'representative_name' => $m->representative_name,
                'logo_path' => $m->logo_path,
            ]),
        ]);
    }

    /**
     * 학술행사 폼 내 스폰서 검색 모달용 빠른 등록 (JSON).
     */
    public function quickStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'representative_name' => ['nullable', 'string', 'max:200'],
            'logo' => ['nullable', 'file', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('backoffice/academic-sponsor-masters/logos', 'public');
        }

        $nextOrder = (int) (AcademicSponsorMaster::query()->max('sort_order') ?? 0) + 1;

        $m = AcademicSponsorMaster::create([
            'name' => $data['name'],
            'representative_name' => $data['representative_name'] ?? null,
            'logo_path' => $path,
            'status' => 'active',
            'sort_order' => $nextOrder,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $m->id,
                'name' => $m->name,
                'representative_name' => $m->representative_name,
                'logo_path' => $m->logo_path,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\LocalDoctorRequest;
use App\Models\DoctorCategory;
use App\Models\LocalDoctor;
use App\Models\User;
use App\Services\Backoffice\LocalDoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocalDoctorController extends Controller
{
    public function __construct(
        protected LocalDoctorService $localDoctorService
    ) {}

    public function index(Request $request): View
    {
        $doctors = $this->localDoctorService->paginateFiltered($request);
        $sidos = config('local_doctor_regions.sidos', []);
        $sigunguBySido = config('local_doctor_regions.sigungu_by_sido', []);

        return view('backoffice.local-doctors.index', [
            'doctors' => $doctors,
            'perPage' => $doctors->perPage(),
            'statusLabels' => LocalDoctorService::statusLabels(),
            'categories' => DoctorCategory::query()->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(),
            'sidos' => $sidos,
            'sigunguBySido' => $sigunguBySido,
        ]);
    }

    public function create(Request $request): View
    {
        return view('backoffice.local-doctors.create', [
            'localDoctor' => null,
            'cancelUrl' => $this->cancelUrl($request),
            'statusLabels' => LocalDoctorService::statusLabels(),
            'categories' => DoctorCategory::query()->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(),
            'sidos' => config('local_doctor_regions.sidos', []),
            'sigunguBySido' => config('local_doctor_regions.sigungu_by_sido', []),
            'functionalTests' => config('local_doctor_form_options.functional_tests', []),
            'treatmentAreas' => config('local_doctor_form_options.treatment_areas', []),
        ]);
    }

    public function store(LocalDoctorRequest $request): RedirectResponse
    {
        $this->localDoctorService->create($request, $request->validated());

        return redirect()
            ->route('backoffice.local-doctors.index')
            ->with('success', '주치의가 등록되었습니다.');
    }

    public function edit(Request $request, LocalDoctor $localDoctor): View
    {
        $localDoctor->load(['doctorCategories', 'member']);

        return view('backoffice.local-doctors.edit', [
            'localDoctor' => $localDoctor,
            'cancelUrl' => $this->cancelUrl($request),
            'statusLabels' => LocalDoctorService::statusLabels(),
            'categories' => DoctorCategory::query()->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(),
            'sidos' => config('local_doctor_regions.sidos', []),
            'sigunguBySido' => config('local_doctor_regions.sigungu_by_sido', []),
            'functionalTests' => config('local_doctor_form_options.functional_tests', []),
            'treatmentAreas' => config('local_doctor_form_options.treatment_areas', []),
        ]);
    }

    public function update(LocalDoctorRequest $request, LocalDoctor $localDoctor): RedirectResponse
    {
        $this->localDoctorService->update($request, $localDoctor, $request->validated());

        return redirect()
            ->route('backoffice.local-doctors.index')
            ->with('success', '주치의 정보가 수정되었습니다.');
    }

    public function destroy(LocalDoctor $localDoctor): RedirectResponse
    {
        $this->localDoctorService->destroy($localDoctor);

        return redirect()
            ->route('backoffice.local-doctors.index')
            ->with('success', '주치의가 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'local_doctor_ids' => ['required', 'array'],
            'local_doctor_ids.*' => ['integer', 'exists:local_doctors,id'],
        ]);

        $deleted = $this->localDoctorService->deleteMany($validated['local_doctor_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건의 주치의가 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $searchField = (string) $request->get('search_field', 'all');
        $perPage = 10;

        $query = User::query()
            ->select(['id', 'name', 'login_id', 'email', 'phone_number', 'license_number'])
            ->notWithdrawn();

        if ($keyword !== '' && $searchField !== 'all') {
            $query->where(function ($q) use ($keyword, $searchField): void {
                if ($searchField === 'name') {
                    $q->where('name', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'id') {
                    $q->where('login_id', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'email') {
                    $q->where('email', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'phone') {
                    $q->where('phone_number', 'like', '%' . $keyword . '%');
                }
            });
        } elseif ($keyword !== '') {
            $query->where(function ($q) use ($keyword): void {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('login_id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone_number', 'like', '%' . $keyword . '%');
            });
        }

        $members = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $members->getCollection()->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'login_id' => $member->login_id,
                'email' => $member->email,
                'phone_number' => $member->phone_number,
                'license_number' => $member->license_number,
            ]),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'total' => $members->total(),
                'per_page' => $members->perPage(),
            ],
        ]);
    }

    protected function cancelUrl(Request $request): string
    {
        return $request->query('return_url', route('backoffice.local-doctors.index'));
    }
}

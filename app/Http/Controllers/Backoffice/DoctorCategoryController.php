<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\DoctorCategoryRequest;
use App\Models\DoctorCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 20;

        $categories = DoctorCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('backoffice.doctor-categories.index', [
            'categories' => $categories,
            'perPage' => $categories->perPage(),
            'statusLabels' => [
                'active' => '사용중',
                'inactive' => '미사용',
            ],
        ]);
    }

    public function create(): View
    {
        return view('backoffice.doctor-categories.create', [
            'category' => new DoctorCategory([
                'status' => 'active',
                'sort_order' => 0,
            ]),
            'cancelUrl' => route('backoffice.doctor-categories.index'),
        ]);
    }

    public function store(DoctorCategoryRequest $request): RedirectResponse
    {
        DoctorCategory::query()->create($request->validated());

        return redirect()
            ->route('backoffice.doctor-categories.index')
            ->with('success', '진료 과목이 등록되었습니다.');
    }

    public function edit(DoctorCategory $doctorCategory): View
    {
        return view('backoffice.doctor-categories.edit', [
            'category' => $doctorCategory,
            'cancelUrl' => route('backoffice.doctor-categories.index'),
        ]);
    }

    public function update(DoctorCategoryRequest $request, DoctorCategory $doctorCategory): RedirectResponse
    {
        $doctorCategory->update($request->validated());

        return redirect()
            ->route('backoffice.doctor-categories.index')
            ->with('success', '진료 과목이 수정되었습니다.');
    }

    public function destroy(DoctorCategory $doctorCategory): RedirectResponse
    {
        if ($doctorCategory->localDoctors()->exists()) {
            return redirect()
                ->route('backoffice.doctor-categories.index')
                ->with('error', '연결된 주치의가 있어 삭제할 수 없습니다.');
        }
        $doctorCategory->delete();

        return redirect()
            ->route('backoffice.doctor-categories.index')
            ->with('success', '진료 과목이 삭제되었습니다.');
    }
}

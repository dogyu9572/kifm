<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\StoreAcademicHotelRequest;
use App\Http\Requests\Backoffice\UpdateAcademicHotelRequest;
use App\Models\AcademicHotel;
use App\Services\Backoffice\AcademicHotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicHotelController extends Controller
{
    public function __construct(
        private readonly AcademicHotelService $hotelService
    ) {}

    public function index(Request $request): View
    {
        $hotels = $this->hotelService->paginateFiltered($request);
        $perPage = $this->hotelService->resolvePerPage($request);

        return view('backoffice.academic-hotels.index', [
            'hotels' => $hotels,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('backoffice.academic-hotels.create', [
            'hotel' => new AcademicHotel([
                'status' => 'active',
            ]),
        ]);
    }

    public function store(StoreAcademicHotelRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $nextOrder = (int) (AcademicHotel::query()->max('sort_order') ?? 0) + 1;
        $validated['sort_order'] = $nextOrder;
        $this->hotelService->store($validated, $request->file('image'));

        return redirect()->route('backoffice.academic-hotels.index')
            ->with('success', '숙박 정보가 등록되었습니다.');
    }

    public function edit(AcademicHotel $academic_hotel): View
    {
        return view('backoffice.academic-hotels.edit', [
            'hotel' => $academic_hotel,
        ]);
    }

    public function update(UpdateAcademicHotelRequest $request, AcademicHotel $academic_hotel): RedirectResponse
    {
        $validated = $request->validated();
        $removeImage = (bool) ($validated['remove_image'] ?? false);
        if ($request->hasFile('image')) {
            $removeImage = false;
        }
        unset($validated['remove_image'], $validated['existing_image']);
        $this->hotelService->update($academic_hotel, $validated, $request->file('image'), $removeImage);

        return redirect()->route('backoffice.academic-hotels.index')
            ->with('success', '숙박 정보가 수정되었습니다.');
    }

    public function destroy(AcademicHotel $academic_hotel): RedirectResponse
    {
        $this->hotelService->delete($academic_hotel);

        return redirect()->route('backoffice.academic-hotels.index')
            ->with('success', '삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hotel_ids' => ['required', 'array'],
            'hotel_ids.*' => ['integer', 'exists:academic_hotels,id'],
        ]);

        $deleted = $this->hotelService->deleteMany($validated['hotel_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }
}

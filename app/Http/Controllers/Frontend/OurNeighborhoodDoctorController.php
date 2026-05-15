<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LocalDoctor;
use App\Services\Frontend\PublicLocalDoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OurNeighborhoodDoctorController extends Controller
{
    public function __construct(
        private readonly PublicLocalDoctorService $publicLocalDoctorService,
    ) {}

    public function index(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '06';
        $sNum = '01';
        $gName = '우리동네 주치의';
        $sName = '우리동네 주치의';
        $geName = 'Our Neighborhood Doctor';
        $gSlug = 'our_neighborhood_doctor';

        $filterOptions = $this->publicLocalDoctorService->filterOptions($request);
        $doctors = $this->publicLocalDoctorService->listActiveFiltered($request);

        $selectedMapIndex = $filterOptions['selected_map_index'];
        $mapLocalNames = $filterOptions['map_local_names'];
        $localMapLabel = $selectedMapIndex && isset($mapLocalNames[$selectedMapIndex])
            ? $mapLocalNames[$selectedMapIndex]
            : '서울';

        return view('our_neighborhood_doctor.index', array_merge(
            compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'doctors'),
            $filterOptions,
            ['local_map_label' => $localMapLabel],
        ));
    }

    public function popup(LocalDoctor $localDoctor): JsonResponse
    {
        if (! $localDoctor->isActive()) {
            abort(404);
        }

        $localDoctor->load('doctorCategories');

        $localDoctor->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $this->publicLocalDoctorService->popupPayload($localDoctor),
        ]);
    }
}

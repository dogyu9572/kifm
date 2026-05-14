<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OurNeighborhoodDoctorController extends Controller
{
    public function index(): View
    {
        $page_type = 'professional';
        $gNum = '06';
        $sNum = '01';
        $gName = '우리동네 주치의';
        $sName = '우리동네 주치의';
        $geName = 'Our Neighborhood Doctor';
        $gSlug = 'our_neighborhood_doctor';

        return view('our_neighborhood_doctor.index', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

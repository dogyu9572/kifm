<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MainController extends Controller
{
    public function index(): View
    {
        $page_type = 'academic_conference';
        $gNum = 'main';
        $gName = '학술대회';
        $sName = '학술대회 메인';
        $gSlug = 'academic_conference_index';

        return view('academic_conference.index', compact('page_type', 'gNum', 'gName', 'sName', 'gSlug'));
    }
}

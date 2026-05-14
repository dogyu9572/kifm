<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EtcController extends Controller
{
    public function sponsors(): View
    {
        $page_type = 'academic_conference';
        $gNum = '06';
        $gName = 'Sponsors';
        $sName = 'Sponsors';
        $gSlug = 'academic_conference_sponsors';

        return view('academic_conference.etc.sponsors', compact('page_type', 'gNum', 'gName', 'sName', 'gSlug'));
    }

    public function exhibition(): View
    {
        $page_type = 'academic_conference';
        $gNum = '07';
        $gName = 'Exhibition';
        $sName = 'Exhibition';
        $gSlug = 'academic_conference_exhibition';

        return view('academic_conference.etc.exhibition', compact('page_type', 'gNum', 'gName', 'sName', 'gSlug'));
    }
}

<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function program(): View
    {
        return $this->renderProgram('program', '01', 'Program', 'academic_conference_program');
    }

    public function speakers(): View
    {
        return $this->renderProgram('speakers', '02', 'Speakers', 'academic_conference_speakers');
    }

    private function renderProgram(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'academic_conference';
        $gNum = '02';
        $gName = 'Program';
        $gSlug = $slug;

        return view('academic_conference.program.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}

<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KifmController extends Controller
{
    public function invitation(): View
    {
        return $this->renderKifm('invitation', '01', '초대의 글', 'academic_conference_invitation');
    }

    public function committee(): View
    {
        return $this->renderKifm('committee', '02', '조직위원회', 'academic_conference_committee');
    }

    public function venue(): View
    {
        return $this->renderKifm('venue', '03', '행사장 안내', 'academic_conference_venue');
    }

    private function renderKifm(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'academic_conference';
        $gNum = '01';
        $gName = 'KIFM';
        $gSlug = $slug;

        return view('academic_conference.kifm.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}

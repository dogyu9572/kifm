<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        return $this->render('notice', 'academic_conference_notice');
    }

    public function show(): View
    {
        return $this->render('notice_view', 'academic_conference_notice_view');
    }

    private function render(string $view, string $slug): View
    {
        $page_type = 'academic_conference';
        $gNum = '05';
        $sNum = '01';
        $gName = 'Notice';
        $sName = 'Notice';
        $gSlug = $slug;

        return view('academic_conference.notice.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}

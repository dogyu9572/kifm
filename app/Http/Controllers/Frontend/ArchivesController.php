<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ArchivesController extends Controller
{
    public function general(): View
    {
        return $this->renderArchive('general', '01', '일반 자료실', 'archives_general');
    }

    public function generalView(): View
    {
        return $this->renderArchive('general_view', '01', '일반 자료실', 'archives_general_view');
    }

    public function academic(): View
    {
        return $this->renderArchive('academic', '02', '학술 자료실', 'archives_academic');
    }

    public function academicView(): View
    {
        return $this->renderArchive('academic_view', '02', '학술 자료실', 'archives_academic_view');
    }

    public function members(): View
    {
        return $this->renderArchive('members', '03', '회원 자료실', 'archives_members');
    }

    public function membersView(): View
    {
        return $this->renderArchive('members_view', '03', '회원 자료실', 'archives_members_view');
    }

    private function renderArchive(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '04';
        $gName = '학회 자료실';
        $geName = 'archives';
        $gSlug = $slug;

        return view('archives.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AbstractController extends Controller
{
    public function info(): View
    {
        return $this->render('abstract_info', '01', 'Information', 'academic_conference_abstract_info');
    }

    public function submit(): View
    {
        return $this->render('abstract_submit', '02', '초록 접수', 'academic_conference_abstract_submit');
    }

    public function formMember(): View
    {
        return $this->render('abstract_form_member', '02', '초록 접수', 'academic_conference_abstract_form_member');
    }

    public function formNonMember(): View
    {
        return $this->render('abstract_form_non_member', '02', '초록 접수', 'academic_conference_abstract_form_non_member');
    }

    public function complete(): View
    {
        return $this->render('abstract_complete', '02', '초록 접수', 'academic_conference_abstract_complete');
    }

    public function check(): View
    {
        return $this->render('abstract_check', '03', '학술대회 초록등록 확인', 'academic_conference_abstract_check');
    }

    public function modify(): View
    {
        return $this->render('abstract_modify', '03', '학술대회 초록등록 확인', 'academic_conference_abstract_modify');
    }

    public function list(): View
    {
        return $this->render('abstract_list', '03', '초록 접수 리스트', 'academic_conference_abstract_list');
    }

    private function render(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'academic_conference';
        $gNum = '04';
        $gName = 'Abstract';
        $gSlug = $slug;
        $event = null;
        $currentMember = auth()->user();
        $abstracts = null;
        $abstractPresentationTypes = [];
        $conferenceBaseUrl = url('/academic_conference');

        $viewName = 'academic_conference.abstract.' . $view;
        if ($view === 'abstract_list') {
            $viewName = 'academic_conference.default.abstract.' . $view;
        }

        return view($viewName, compact(
            'event',
            'currentMember',
            'abstracts',
            'abstractPresentationTypes',
            'conferenceBaseUrl',
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'gSlug',
        ));
    }
}

<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OnsiteController extends Controller
{
    public function intro(): View
    {
        $page_type = 'academic_conference';
        $gNum = 'intro';
        $gName = '현장등록';
        $sName = '회원등록 안내';
        $gSlug = 'academic_conference_onsite';

        return view('academic_conference.onsite.index', compact('page_type', 'gNum', 'gName', 'sName', 'gSlug'));
    }

    public function info(): View
    {
        return $this->render('onsite_info', '01', '학술대회 현장 등록', 'academic_conference_onsite_info');
    }

    public function memberReg(): View
    {
        return $this->render('onsite_member_registration', '02', '회원 등록', 'academic_conference_onsite_member_reg');
    }

    public function nonMemberReg(): View
    {
        return $this->render('onsite_non_member_registration', '02', '비회원 등록', 'academic_conference_onsite_non_member_reg');
    }

    public function welcomeApproved(): View
    {
        return $this->render('onsite_welcome_approved', '02', '현장등록 완료', 'academic_conference_onsite_welcome_approved');
    }

    public function checkRegistration(): View
    {
        return $this->render('onsite_check_registration', '03', '학술대회 현장등록 조회', 'academic_conference_onsite_check_registration');
    }

    public function checkNonRegistration(): View
    {
        return $this->render('onsite_check_non_registration', '03', '학술대회 현장등록 조회', 'academic_conference_onsite_check_non_registration');
    }

    public function confirmationComplete(): View
    {
        return $this->render('onsite_confirmation_complete', '03', '현장 등록 확인 완료', 'academic_conference_onsite_confirmation_complete');
    }

    private function render(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'academic_conference';
        $gNum = 'academic_conference';
        $gName = '현장등록';
        $gSlug = $slug;

        return view('academic_conference.onsite.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}

<?php

namespace App\Http\Controllers\Frontend\AcademicConference;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function info(): View
    {
        return $this->render('info', '01', 'Information', 'academic_conference_info');
    }

    public function reg(): View
    {
        return $this->render('registration', '02', '학술대회 사전등록', 'academic_conference_reg');
    }

    public function regForm(): View
    {
        return $this->render('registration_form', '02', '학술대회 사전등록 정보입력', 'academic_conference_reg_form');
    }

    public function regFormNonMember(): View
    {
        return $this->render('registration_form_non_member', '02', '학술대회 사전등록 정보입력(비회원)', 'academic_conference_reg_form_non_member');
    }

    public function regEnd(): View
    {
        return $this->render('registration_end', '02', '학술대회 사전등록 결제 완료', 'academic_conference_reg_end');
    }

    public function regCheckMember(): View
    {
        return $this->render('registration_check_member', '03', '학술대회 등록 확인', 'academic_conference_reg_check_member');
    }

    public function regCheckNonMember(): View
    {
        return $this->render('registration_check_non_member', '03', '학술대회 등록 확인(비회원)', 'academic_conference_reg_check_non_member');
    }

    public function regResult(): View
    {
        return $this->render('registration_result', '03', '학술대회 등록 확인 완료', 'academic_conference_reg_result');
    }

    private function render(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'academic_conference';
        $gNum = '03';
        $gName = 'Registration';
        $gSlug = $slug;

        return view('academic_conference.registration.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}

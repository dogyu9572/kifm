<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return $this->render('profile_edit', '01', '개인정보 관리', 'profile_edit');
    }

    public function secession(): View
    {
        return $this->render('secession', '01', '개인정보 관리', 'secession');
    }

    public function hospitalInformation(): View
    {
        return $this->render('hospital_information', '07', '병원 정보 관리하기', 'hospital_information');
    }

    public function executiveActivities(): View
    {
        return $this->render('executive_activities', '08', '회원 활동(임원)', 'executive_activities');
    }

    public function committeeParticipation(): View
    {
        return $this->render('committee_participation', '09', '위원회 참여 현황', 'committee_participation');
    }

    public function committeeParticipationAdmin(): View
    {
        return $this->render('committee_participation_admin', '10', '위원회 참여 현황', 'committee_participation_admin');
    }

    private function render(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '99';
        $gName = '마이페이지';
        $geName = 'My Page';
        $gSlug = $slug;

        return view('mypage.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

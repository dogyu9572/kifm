<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function participation(): View
    {
        return $this->render('participation_history', '02', '참가내역 관리', 'participation_history');
    }

    public function participationView(): View
    {
        return $this->render('participation_history_view', '02', '참가내역 관리', 'participation_history_view');
    }

    public function onlineTraining(): View
    {
        return $this->render('online_training', '03', '온라인 교육 수강내역', 'online_training');
    }

    public function onlineTrainingView(): View
    {
        return $this->render('online_training_view', '03', '온라인 교육 수강내역', 'online_training_view');
    }

    public function favoriteMenu(): View
    {
        return $this->render('favorite', '05', '즐겨찾는 메뉴', 'favorite_menu');
    }

    public function bookmark(): View
    {
        return $this->render('bookmark', '06', '북마크', 'bookmark');
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

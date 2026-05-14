<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MemberPlazaController extends Controller
{
    public function societyNotices(): View
    {
        return $this->render('society_notices', '01', '학회 공지', 'member_plaza_society_notices');
    }

    public function societyNoticesView(): View
    {
        return $this->render('society_notices_view', '01', '학회 공지', 'member_plaza_society_notices_view');
    }

    public function otherNotices(): View
    {
        return $this->render('other_notices', '02', '기타 공지', 'member_plaza_other_notices');
    }

    public function otherNoticesView(): View
    {
        return $this->render('other_notices_view', '02', '기타 공지', 'member_plaza_other_notices_view');
    }

    public function societyAlbum(): View
    {
        return $this->render('society_album', '03', '학회 앨범', 'member_plaza_society_album');
    }

    public function societyAlbumView(): View
    {
        return $this->render('society_album_view', '03', '학회 앨범', 'member_plaza_society_album_view');
    }

    public function feePaymentGuide(): View
    {
        return $this->render('fee_payment_guide', '04', '회비 납부 안내', 'member_plaza_fee_payment_guide');
    }

    private function render(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '05';
        $gName = '회원광장';
        $geName = 'Member Plaza';
        $gSlug = $slug;

        return view('member_plaza.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

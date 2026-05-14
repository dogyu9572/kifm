<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(): View
    {
        return $this->render('inquiry', 'inquiry_list');
    }

    public function show(): View
    {
        return $this->render('inquiry_view', 'inquiry_view');
    }

    public function create(): View
    {
        return $this->render('inquiry_write', 'inquiry_write');
    }

    private function render(string $view, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '99';
        $sNum = '04';
        $gName = '마이페이지';
        $sName = '1:1 문의';
        $geName = 'My Page';
        $gSlug = $slug;

        return view('mypage.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

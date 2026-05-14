<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AnnualFeeController extends Controller
{
    public function index(): View
    {
        return $this->render('annual_fee', '연회비 납부', 'annual_fee');
    }

    public function end(): View
    {
        return $this->render('annual_fee_end', '연회비 납부', 'annual_fee_end');
    }

    private function render(string $view, string $sName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '99';
        $sNum = '01';
        $gName = '마이페이지';
        $geName = 'My Page';
        $gSlug = $slug;

        return view('mypage.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

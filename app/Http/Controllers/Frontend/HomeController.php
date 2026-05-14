<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $page_type = 'professional';
        $gNum = 'main';
        $gName = '대한기능의학회 메인';
        $popups = collect();
        $banners = collect();

        return view('home.index', compact('page_type', 'gNum', 'gName', 'popups', 'banners'));
    }

    public function intro(): View
    {
        $page_type = 'professional';
        $gNum = 'intro';
        $gName = '대한기능의학회';

        return view('intro.index', compact('page_type', 'gNum', 'gName'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OnlineAcademyController extends Controller
{
    public function index(): View
    {
        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '00';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $geName = 'Online Academy';
        $gSlug = 'online_academy';

        return view('online_academy.index', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function view(): View
    {
        return $this->renderInner('view', 'online_academy_view');
    }

    public function test(): View
    {
        return $this->renderInner('test', 'online_academy_test');
    }

    public function end(): View
    {
        return $this->renderInner('end', 'online_academy_end');
    }

    private function renderInner(string $view, string $slug): View
    {
        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '01';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $gSlug = $slug;

        return view('online_academy.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}

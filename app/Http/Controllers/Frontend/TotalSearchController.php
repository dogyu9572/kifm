<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TotalSearchController extends Controller
{
    public function index(): View
    {
        $page_type = 'professional';
        $gNum = 'total_search';
        $gName = '통합검색';
        $gSlug = 'total_search';

        return view('total_search.index', compact('page_type', 'gNum', 'gName', 'gSlug'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\TotalSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TotalSearchController extends Controller
{
    public function __construct(private readonly TotalSearchService $totalSearchService) {}

    public function index(Request $request): View
    {
        $page_type = 'professional';
        $gNum = 'total_search';
        $gName = '통합검색';
        $gSlug = 'total_search';
        $keyword = trim((string) ($request->query('q', $request->query('keyword', ''))));
        $contentGroups = $this->totalSearchService->contentGroups($keyword);
        $boardGroups = $this->totalSearchService->boardGroups($keyword);
        $totalCount = $this->totalSearchService->total($contentGroups) + $this->totalSearchService->total($boardGroups);

        return view('total_search.index', compact('page_type', 'gNum', 'gName', 'gSlug', 'keyword', 'contentGroups', 'boardGroups', 'totalCount'));
    }
}

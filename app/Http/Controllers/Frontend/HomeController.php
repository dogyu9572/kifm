<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\FrontendHomeService;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly FrontendHomeService $frontendHomeService) {}

    public function index(): View
    {
        return view('home.index', array_merge(
            $this->frontendHomeService->viewData(auth()->user()),
            ['loginPopup' => Session::pull('member_login_popup')],
        ));
    }

    public function intro(): View
    {
        $page_type = 'professional';
        $gNum = 'intro';
        $gName = '대한기능의학회';

        return view('intro.index', compact('page_type', 'gNum', 'gName'));
    }
}

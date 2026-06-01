<?php

namespace App\Http\Controllers;

use App\Services\Frontend\FrontendHomeService;

class HomeController extends Controller
{
    public function __construct(private readonly FrontendHomeService $frontendHomeService) {}

    public function index()
    {
        return view('home.index', $this->frontendHomeService->viewData(auth()->user()));
    }
}

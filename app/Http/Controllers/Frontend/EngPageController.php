<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PublicBoardService;
use App\Services\Frontend\TermsContentService;
use App\Services\Frontend\PublicLocalDoctorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EngPageController extends Controller
{
   public function __construct(
        private readonly PublicBoardService $publicBoardService,
        private readonly TermsContentService $termsContentService,
        private readonly PublicLocalDoctorService $publicLocalDoctorService
    ) {}
    
    public function index(): View
    {
        $page_type = 'eng';
        $gNum = 'main';
        $gName = '영문 메인';
        $gSlug = 'eng_main';
        return view('eng.index', compact('page_type', 'gNum', 'gName', 'gSlug'));
    }

    public function greeting(): View
    {
        $page_type = 'eng';
        $gNum = '01';
        $sNum = '01';
        $gName = '소개';
        $sName = '인사말';
        $geName = 'Wecome Message';
        $gSlug = 'eng_greeting';
        $post = $this->publicBoardService->findSingle('greetings');
        return view('eng.greeting', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'post'));
    }

    public function history(): View
    {
        $page_type = 'eng';
        $gNum = '01';
        $sNum = '02';
        $gName = '소개';
        $sName = '연혁';
        $geName = 'History';
        $gSlug = 'eng_history';
        return view('eng.history', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function organization(): View
    {
        $page_type = 'eng';
        $gNum = '01';
        $sNum = '03';
        $gName = '소개';
        $sName = '조직도';
        $geName = 'Organization';
        $gSlug = 'eng_organization';
        return view('eng.organization', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function academicEvents(): View
    {
        $page_type = 'eng';
        $gNum = '02';
        $sNum = '01';
        $gName = '행사';
        $sName = '학술행사';
        $geName = 'Academin Events';
        $gSlug = 'events_academic';
        return view('eng.academic_events', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function news(): View
    {
        $page_type = 'eng';
        $gNum = '03';
        $sNum = '01';
        $gName = '뉴스';
        $sName = '공지사항';
        $geName = 'News';
        $gSlug = 'news_notice';
        return view('eng.news', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function newsView(Request $request): View
    {
        $page_type = 'eng';
        $gNum = '03';
        $sNum = '01';
        $gName = '뉴스';
        $sName = '공지사항';
        $geName = 'News';
        $gSlug = 'news_notice_view';
        return view('eng.news_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function contactUs(): View
    {
        $page_type = 'eng';
        $gNum = '04';
        $sNum = '01';
        $gName = '문의';
        $sName = '문의하기';
        $geName = 'Contact us';
        $gSlug = 'contact_us';
        return view('eng.contact_us', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}
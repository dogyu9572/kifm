<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class IntroductionController extends Controller
{
    public function overview(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '01';
        $gName = '학회소개';
        $sName = '학회개요';
        $geName = 'introduction';
        $gSlug = 'introduction_overview';

        return view('introduction.overview', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function greeting(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '02';
        $gName = '학회소개';
        $sName = '인사말';
        $geName = 'introduction';
        $gSlug = 'introduction_greeting';

        return view('introduction.greeting', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function history(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '03';
        $gName = '학회소개';
        $sName = '학회 연혁';
        $geName = 'introduction';
        $gSlug = 'introduction_history';

        return view('introduction.history', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function bylaws(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '04';
        $dNum = '01';
        $gName = '학회소개';
        $sName = '회칙';
        $dName = '대한기능의학회 회칙';
        $geName = 'introduction';
        $gSlug = 'introduction_bylaws';

        return view('introduction.bylaws', compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug'));
    }

    public function bylawsOperation(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '04';
        $dNum = '02';
        $gName = '학회소개';
        $sName = '회칙';
        $dName = '업무 및 운영 내규';
        $geName = 'introduction';
        $gSlug = 'introduction_bylaws_operation';

        return view('introduction.bylaws_operation', compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug'));
    }

    public function bylawsProtocol(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '04';
        $dNum = '03';
        $gName = '학회소개';
        $sName = '회칙';
        $dName = '업무 프로토콜';
        $geName = 'introduction';
        $gSlug = 'introduction_bylaws_protocol';

        return view('introduction.bylaws_protocol', compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug'));
    }

    public function officers(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '05';
        $gName = '학회소개';
        $sName = '임원진';
        $geName = 'introduction';
        $gSlug = 'introduction_officers';

        return view('introduction.officers', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function location(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '06';
        $gName = '학회소개';
        $sName = '오시는 길';
        $geName = 'introduction';
        $gSlug = 'introduction_location';

        return view('introduction.location', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

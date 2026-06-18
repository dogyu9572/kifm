<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PublicBoardService;
use App\Services\Frontend\TermsContentService;
use App\Services\Frontend\PublicLocalDoctorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralPageController extends Controller
{
    public function __construct(
        private readonly PublicBoardService $publicBoardService,
        private readonly TermsContentService $termsContentService,
        private readonly PublicLocalDoctorService $publicLocalDoctorService
    ) {}
    
    public function index(): View
    {
        $page_type = 'general';
        $gNum = 'main';
        $gName = '일반 메인';
        $gSlug = 'general_main';
        return view('general_page.index', compact('page_type', 'gNum', 'gName', 'gSlug'));
    }

    /* =========================================================================
     * 01. 학회소개 그룹
     * ========================================================================= */
    public function greeting(): View
    {
        $page_type = 'general';
        $gNum = '01';
        $sNum = '01';
        $gName = '학회소개';
        $sName = '인사말';
        $geName = 'introduction';
        $gSlug = 'introduction_greeting';
        $post = $this->publicBoardService->findSingle('greetings');
        return view('general_page.introduction.greeting', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'post'));
    }

    public function whatIsFm(): View
    {
        $page_type = 'general';
        $gNum = '01';
        $sNum = '02';
        $gName = '학회소개';
        $sName = '기능의학이란';
        $geName = 'introduction';
        $gSlug = 'introduction_what_is_fm';
        
        // ⭕ 경로 일관성을 위해 general_page 하위로 통일 처리합니다.
        return view('general_page.introduction.what_is_fm', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function fmTree(): View
    {
        $page_type = 'general';
        $gNum = '01';
        $sNum = '03';
        $gName = '학회소개';
        $sName = '기능의학 나무 소개';
        $geName = 'introduction';
        $gSlug = 'introduction_fm_tree';
        return view('general_page.introduction.fm_tree', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function clinicalImbalances(): View
    {
        $page_type = 'general';
        $gNum = '01';
        $sNum = '04';
        $gName = '학회소개';
        $sName = '7가지 핵심 임상 불균형';
        $geName = 'introduction';
        $gSlug = 'introduction_clinical_imbalances';
        return view('general_page.introduction.clinical_imbalances', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function process(): View
    {
        $page_type = 'general';
        $gNum = '01';
        $sNum = '05';
        $gName = '학회소개';
        $sName = '기능의학적 진료 과정';
        $geName = 'introduction';
        $gSlug = 'introduction_process';
        return view('general_page.introduction.process', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    /* =========================================================================
     * 02. 건강 알아가기 그룹
     * ========================================================================= */
    public function examination(): View
    {
        $page_type = 'general';
        $gNum = '02';
        $sNum = '01';
        $gName = '건강 알아가기';
        $sName = '기능의학 검사 이해';
        $geName = 'content';
        $gSlug = 'content_examination';
        return view('general_page.content.examination', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function videoAfterrain(): View
    {
        $page_type = 'general';
        $gNum = '02';
        $sNum = '02';
        $gName = '건강 알아가기';
        $sName = '영상 콘텐츠(비온뒤)';
        $geName = 'content';
        $gSlug = 'content_video_afterrain';
        return view('general_page.content.video_afterrain', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
	
    public function videoAfterrainView(): View
    {
        $page_type = 'general';
        $gNum = '02';
        $sNum = '02';
        $gName = '건강 알아가기';
        $sName = '영상 콘텐츠(비온뒤)';
        $geName = 'content';
        $gSlug = 'content_video_afterrain';
        return view('general_page.content.video_afterrain_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function faq(): View
    {
        $page_type = 'general';
        $gNum = '02';
        $sNum = '04';
        $gName = '건강 알아가기';
        $sName = '자주 묻는 질문';
        $geName = 'content';
        $gSlug = 'content_faq';
        return view('general_page.content.faq', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
    
    /* =========================================================================
     * 03. 진료 고민된다면
     * ========================================================================= */
    public function doctorIndex(Request $request): View
    {
        $page_type = 'general';
        $gNum = '03';
        $sNum = '01';
        $gName = '진료 고민된다면';
        $sName = '우리동네 주치의';
        $geName = 'our_neighborhood_doctor';
        $gSlug = 'doctor_index';
        $doctors = $this->publicLocalDoctorService->getPaginatedList($request->all());
        return view('general_page.our_neighborhood_doctor.index', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'doctors'));
    }

    public function popup($local_doctor): View
    {
        $doctor = $this->publicLocalDoctorService->findOrFail($local_doctor);
        return view('general_page.our_neighborhood_doctor.popup', compact('doctor'));
    }
    public function patientStoryIndex(Request $request): View
    {
        $page_type = 'general';
        $gNum = '03';
        $sNum = '01';
        $gName = '진료 고민된다면';
        $sName = '환자 이야기';
        $geName = 'patient_story';
        $gSlug = 'doctor_patient_story';
        return view('general_page.our_neighborhood_doctor.patient_story', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
    public function patientStoryView(Request $request): View
    {
        $page_type = 'general';
        $gNum = '03';
        $sNum = '01';
        $gName = '진료 고민된다면';
        $sName = '환자 이야기';
        $geName = 'patient_story';
        $gSlug = 'doctor_patient_story_view';
        return view('general_page.our_neighborhood_doctor.patient_story_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    /* =========================================================================
     * 04. 학회 뉴스 그룹
     * ========================================================================= */
    public function notices(): View
    {
        $page_type = 'general';
        $gNum = '05';
        $sNum = '01';
        $gName = '학회 뉴스';
        $sName = '학회 소식';
        $geName = 'news';
        $gSlug = 'news_notices';
        return view('general_page.news.notices', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
	
    public function noticesView(): View
    {
        $page_type = 'general';
        $gNum = '05';
        $sNum = '01';
        $gName = '학회 뉴스';
        $sName = '학회 소식';
        $geName = 'news';
        $gSlug = 'news_notices';
        return view('general_page.news.notices_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function pressColumns(): View
    {
        $page_type = 'general';
        $gNum = '05';
        $sNum = '02';
        $gName = '학회 뉴스';
        $sName = '보도자료&칼럼';
        $geName = 'news';
        $gSlug = 'news_press_columns';
        return view('general_page.news.press_columns', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function pressColumnsView(): View
    {
        $page_type = 'general';
        $gNum = '05';
        $sNum = '02';
        $gName = '학회 뉴스';
        $sName = '보도자료&칼럼';
        $geName = 'news';
        $gSlug = 'news_press_columns';
        return view('general_page.news.press_columns_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function mediaEvents(): View
    {
        $page_type = 'general';
        $gNum = '05';
        $sNum = '03';
        $gName = '학회 뉴스';
        $sName = '미디어&행사';
        $geName = 'news';
        $gSlug = 'news_media_events';
        return view('general_page.news.media_events', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function mediaEventsView(): View
    {
        $page_type = 'general';
        $gNum = '05';
        $sNum = '03';
        $gName = '학회 뉴스';
        $sName = '미디어&행사';
        $geName = 'news';
        $gSlug = 'news_media_events';
        return view('general_page.news.media_events_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    /* =========================================================================
     * 이용안내 및 약관 렌더러
     * ========================================================================= */
    public function privacyPolicy(): View
    {
        return $this->renderGeneralTerm('privacy', '01', '개인정보처리방침', 'privacy_policy', [
            'privacyPolicyPost' => $this->termsContentService->getSinglePageContent('privacy_policy'),
        ]);
    }

    public function emailCollectionRefusal(): View
    {
        return $this->renderGeneralTerm('email_collection_refusal', '02', '이메일무단수집거부', 'email_collection_refusal');
    }

    private function renderGeneralTerm(string $view, string $sNum, string $sName, string $slug, array $with = []): View
    {
        $page_type = 'general';
        $gNum = '98';
        $gName = '이용안내';
        $geName = 'Subcommittee';
        $gSlug = $slug;
        return view('terms.' . $view, array_merge(
            compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'),
            $with
        ));
    }
}
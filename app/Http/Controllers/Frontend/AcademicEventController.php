<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PublicAcademicEventService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicEventController extends Controller
{
    public function __construct(
        private readonly PublicAcademicEventService $academicEventService,
    ) {}

    public function conference(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '01';
        $gName = '학술행사';
        $sName = '학술대회';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_conference';
        $featuredConference = $this->academicEventService->featuredConference();
        $conferences = $this->academicEventService->paginateConferences($request);
        $statusLabels = $this->academicEventService->statusLabels();
        $yearOptions = $this->academicEventService->yearOptions();
        $filters = $this->academicEventService->filters($request);

        return view('academic_event.conference', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'featuredConference',
            'conferences',
            'statusLabels',
            'yearOptions',
            'filters',
        ));
    }

    public function conferenceView(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '01';
        $gName = '학술행사';
        $sName = '학술대회';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_conference_view';

        return view('academic_event.conference_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function trainingCourse(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '02';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course';

        return view('academic_event.training_course', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function trainingCourseView(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '02';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course_view';

        return view('academic_event.training_course_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function trainingCourseEnd(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '02';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course_end';

        return view('academic_event.training_course_end', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

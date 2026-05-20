<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\EduCourse;
use App\Services\Frontend\PublicOnlineAcademyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnlineAcademyController extends Controller
{
    public function __construct(
        private readonly PublicOnlineAcademyService $onlineAcademyService,
    ) {}

    public function index(Request $request): View
    {
        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '00';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $geName = 'Online Academy';
        $gSlug = 'online_academy';
        $courses = $this->onlineAcademyService->paginateVisible($request);
        $featuredCourses = $this->onlineAcademyService->featuredCourses();
        $courseTypeLabels = $this->onlineAcademyService->courseTypeLabels();
        $searchFieldLabels = $this->onlineAcademyService->searchFieldLabels();
        $yearOptions = $this->onlineAcademyService->yearOptions();
        $keywordOptions = $this->onlineAcademyService->keywordOptions();
        $filters = $this->onlineAcademyService->filters($request);

        return view('online_academy.index', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'courses',
            'featuredCourses',
            'courseTypeLabels',
            'searchFieldLabels',
            'yearOptions',
            'keywordOptions',
            'filters',
        ));
    }

    public function view(): RedirectResponse
    {
        $course = $this->onlineAcademyService->firstVisible();
        if ($course === null) {
            return redirect()->route('online_academy.index');
        }

        return redirect()->route('online_academy.show', $course);
    }

    public function show(EduCourse $course): View
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);

        return $this->renderInner('view', 'online_academy_view', [
            'course' => $course,
        ]);
    }

    public function test(): View
    {
        return $this->renderInner('test', 'online_academy_test');
    }

    public function exam(EduCourse $course): View
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);
        $examPage = $this->onlineAcademyService->examPageData($course);

        return $this->renderInner('exam', 'online_academy_exam', array_merge([
            'course' => $course,
        ], $examPage));
    }

    public function end(): View
    {
        return $this->renderInner('end', 'online_academy_end');
    }

    private function renderInner(string $view, string $slug, array $data = []): View
    {
        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '01';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $gSlug = $slug;

        return view('online_academy.' . $view, array_merge(
            compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'),
            $data,
        ));
    }
}

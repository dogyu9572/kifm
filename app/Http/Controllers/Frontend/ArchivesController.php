<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PublicBoardService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArchivesController extends Controller
{
    public function __construct(private readonly PublicBoardService $publicBoardService) {}

    public function general(Request $request): View
    {
        $posts = $this->publicBoardService->list('general_archive', $request, 10);

        return view('archives.general', array_merge(
            $this->archivesViewData('01', '일반 자료실', 'archives_general'),
            compact('posts'),
        ));
    }

    public function generalShow(int $id): View
    {
        $post = $this->publicBoardService->find('general_archive', $id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('general_archive', $id);

        return view('archives.general_view', array_merge(
            $this->archivesViewData('01', '일반 자료실', 'archives_general_view'),
            compact('post', 'prev', 'next'),
        ));
    }

    public function academic(Request $request): View
    {
        $posts = $this->publicBoardService->list('academic_archive', $request, 10);

        return view('archives.academic', array_merge(
            $this->archivesViewData('02', '학술 자료실', 'archives_academic'),
            compact('posts'),
        ));
    }

    public function academicShow(int $id): View
    {
        $post = $this->publicBoardService->find('academic_archive', $id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('academic_archive', $id);

        return view('archives.academic_view', array_merge(
            $this->archivesViewData('02', '학술 자료실', 'archives_academic_view'),
            compact('post', 'prev', 'next'),
        ));
    }

    public function members(Request $request): View
    {
        $posts = $this->publicBoardService->list('member_archive', $request, 10);

        return view('archives.members', array_merge(
            $this->archivesViewData('03', '회원 자료실', 'archives_members'),
            compact('posts'),
        ));
    }

    public function membersShow(int $id): View
    {
        $post = $this->publicBoardService->find('member_archive', $id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('member_archive', $id);

        return view('archives.members_view', array_merge(
            $this->archivesViewData('03', '회원 자료실', 'archives_members_view'),
            compact('post', 'prev', 'next'),
        ));
    }

    public function journals(Request $request): View
    {
        $posts = $this->publicBoardService->list('academic_journals', $request, 10);

        return view('archives.journals', array_merge(
            $this->archivesViewData('04', '학술지', 'archives_journals'),
            compact('posts'),
        ));
    }

    /**
     * 학회 자료실 공통 화면 변수.
     */
    private function archivesViewData(string $sNum, string $sName, string $slug): array
    {
        return [
            'page_type' => 'professional',
            'gNum' => '04',
            'gName' => '학회 자료실',
            'geName' => 'archives',
            'sNum' => $sNum,
            'sName' => $sName,
            'gSlug' => $slug,
        ];
    }
}

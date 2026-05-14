<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SubcommitteeController extends Controller
{
    public function index(): View
    {
        $page_type = 'professional';
        $gNum = '03';
        $sNum = '01';
        $gName = '산하위원회';
        $sName = '산하위원회';
        $geName = 'Subcommittee';
        $gSlug = 'subcommittee';

        return view('subcommittee.index', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function notice(): View
    {
        return $this->renderNoticeView('notice', 'subcommittee_notice');
    }

    public function noticeView(): View
    {
        return $this->renderNoticeView('notice_view', 'subcommittee_notice_view');
    }

    public function discussion(): View
    {
        return $this->renderDiscussionView('discussion', 'subcommittee_discussion');
    }

    public function discussionView(): View
    {
        return $this->renderDiscussionView('discussion_view', 'subcommittee_discussion_view');
    }

    public function discussionWrite(): View
    {
        return $this->renderDiscussionView('discussion_write', 'subcommittee_discussion_write');
    }

    public function archives(): View
    {
        return $this->renderArchivesView('archives', 'subcommittee_archives');
    }

    public function archivesView(): View
    {
        return $this->renderArchivesView('archives_view', 'subcommittee_archives_view');
    }

    private function renderNoticeView(string $view, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '03';
        $sNum = '01';
        $dNum = '01';
        $gName = '산하위원회';
        $sName = '임상 영양 대사 연구회';
        $dName = '공지사항';
        $geName = 'Subcommittee';
        $gSlug = $slug;

        return view('subcommittee.' . $view, compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug'));
    }

    private function renderDiscussionView(string $view, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '03';
        $sNum = '01';
        $dNum = '02';
        $gName = '산하위원회';
        $sName = '임상 영양 대사 연구회';
        $dName = '토론장';
        $geName = 'Subcommittee';
        $gSlug = $slug;

        return view('subcommittee.' . $view, compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug'));
    }

    private function renderArchivesView(string $view, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '03';
        $sNum = '01';
        $dNum = '03';
        $gName = '산하위원회';
        $sName = '임상 영양 대사 연구회';
        $dName = '자료실';
        $geName = 'Subcommittee';
        $gSlug = $slug;

        return view('subcommittee.' . $view, compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontendSubcommitteeDiscussionCommentRequest;
use App\Http\Requests\FrontendSubcommitteeDiscussionStoreRequest;
use App\Models\CommunityCommittee;
use App\Models\CommunityCommitteeApplication;
use App\Models\Popup;
use App\Services\Frontend\MailformNotificationService;
use App\Services\Frontend\PublicBoardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubcommitteeController extends Controller
{
    public function __construct(
        private readonly PublicBoardService $publicBoardService,
        private readonly MailformNotificationService $mailNotifier,
    ) {}

    public function index(): View
    {
        $page_type = 'professional';
        $gNum = '03';
        $sNum = '01';
        $gName = '산하위원회';
        $sName = '산하위원회';
        $geName = 'Subcommittee';
        $gSlug = 'subcommittee';

        $committees = CommunityCommittee::query()
            ->where('visibility_yn', 'Y')
            ->where(function ($query): void {
                $query->whereNull('committee_type')
                    ->orWhere('committee_type', '!=', 'special');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $user = Auth::user();
        $accessibleCommitteeIds = $user->isAdmin()
            ? $committees->map(static fn (CommunityCommittee $committee): string => (string) $committee->id)->all()
            : $user->communityCommitteeAccessIdStrings();
        $accessibleCommitteeIdSet = array_flip($accessibleCommitteeIds);
        $pendingCommitteeIds = CommunityCommitteeApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'PENDING')
            ->pluck('community_committee_id')
            ->map(static fn ($id): string => (string) $id)
            ->all();
        $pendingCommitteeIdSet = array_flip($pendingCommitteeIds);

        $committeePopups = collect();

        return view('subcommittee.index', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'committees',
            'accessibleCommitteeIdSet',
            'pendingCommitteeIdSet',
            'committeePopups',
        ));
    }

    public function apply(Request $request, CommunityCommittee $committee): RedirectResponse
    {
        if (! $this->isPublicCommittee($committee)) {
            throw new NotFoundHttpException();
        }

        $user = Auth::user();
        if ($user === null) {
            abort(403);
        }
        if ($user->isAdmin() || $user->canAccessCommunityCommitteeId((string) $committee->id)) {
            return redirect()->route('subcommittee.notice', $committee);
        }

        $application = null;
        DB::transaction(function () use ($committee, $user, &$application) {
            $application = CommunityCommitteeApplication::query()
                ->where('community_committee_id', $committee->id)
                ->where('user_id', $user->id)
                ->where('status', 'PENDING')
                ->first();

            if ($application instanceof CommunityCommitteeApplication) {
                $application->fill([
                    'applicant_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                ])->save();
            } else {
                $application = CommunityCommitteeApplication::query()->create([
                    'community_committee_id' => $committee->id,
                    'user_id' => $user->id,
                    'applicant_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                    'status' => 'PENDING',
                    'reject_reason' => null,
                    'applied_at' => now(),
                    'processed_at' => null,
                    'processed_by' => null,
                ]);
            }

            $committee->pending_count = CommunityCommitteeApplication::query()
                ->where('community_committee_id', $committee->id)
                ->where('status', 'PENDING')
                ->count();
            $committee->save();
        });

        if ($application instanceof CommunityCommitteeApplication) {
            $this->mailNotifier->sendCommitteeApplicationReceived($application);
        }

        return redirect()
            ->route('subcommittee.index')
            ->with('alert', "가입 신청이 완료되었습니다.\n관리자 승인 후 알림을 드리겠습니다.");
    }

    public function notice(Request $request, CommunityCommittee $committee): View
    {
        $this->assertMayAccessCommittee($committee);
        $posts = $this->publicBoardService->list(
            'community_committee_notices',
            $request,
            10,
            $committee->name,
        );

        return view('subcommittee.notice', array_merge(
            $this->committeePageData($committee, '공지사항', '01', 'community_committee_notices'),
            compact('posts'),
        ));
    }

    public function noticeShow(CommunityCommittee $committee, int $id): View
    {
        $this->assertMayAccessCommittee($committee);
        $name = $committee->name;
        $post = $this->publicBoardService->find('community_committee_notices', $id, $name);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('community_committee_notices', $id, $name);

        return view('subcommittee.notice_view', array_merge(
            $this->committeePageData($committee, '공지사항', '01', 'community_committee_notices'),
            compact('post', 'prev', 'next'),
        ));
    }

    public function discussion(Request $request, CommunityCommittee $committee): View
    {
        $this->assertMayAccessCommittee($committee);
        $posts = $this->publicBoardService->list(
            'community_committee_discussions',
            $request,
            10,
            $committee->name,
        );

        return view('subcommittee.discussion', array_merge(
            $this->committeePageData($committee, '토론장', '02', 'community_committee_discussions'),
            compact('posts'),
        ));
    }

    public function discussionShow(CommunityCommittee $committee, int $id): View
    {
        $this->assertMayAccessCommittee($committee);
        $name = $committee->name;
        $post = $this->publicBoardService->find('community_committee_discussions', $id, $name);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('community_committee_discussions', $id, $name);
        $comments = $this->publicBoardService->listComments('community_committee_discussions', $id);

        return view('subcommittee.discussion_view', array_merge(
            $this->committeePageData($committee, '토론장', '02'),
            compact('post', 'prev', 'next', 'comments'),
        ));
    }

    public function discussionWrite(CommunityCommittee $committee): View
    {
        $this->assertMayAccessCommittee($committee);

        return view('subcommittee.discussion_write', $this->committeePageData($committee, '토론 주제 등록', '02', 'community_committee_discussions'));
    }

    public function discussionStore(FrontendSubcommitteeDiscussionStoreRequest $request, CommunityCommittee $committee): RedirectResponse
    {
        $this->assertMayAccessCommittee($committee);

        $user = Auth::user();
        $postId = $this->publicBoardService->createCommitteeDiscussion(
            $committee,
            $request->validated(),
            (int) $user->id,
            ($user->name ?: $user->login_id) ?: '회원'
        );

        $request->session()->forget('captcha.discussion');

        return redirect()->route('subcommittee.discussion_show', [$committee, $postId]);
    }

    public function discussionCommentStore(
        FrontendSubcommitteeDiscussionCommentRequest $request,
        CommunityCommittee $committee,
        int $id
    ): RedirectResponse {
        $this->assertMayAccessCommittee($committee);

        $name = $committee->name;
        $post = $this->publicBoardService->find('community_committee_discussions', $id, $name);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        $user = Auth::user();
        $this->publicBoardService->createComment(
            'community_committee_discussions',
            $id,
            $request->validated('content'),
            (int) $user->id,
            ($user->name ?: $user->login_id) ?: '회원'
        );

        return redirect()->route('subcommittee.discussion_show', [$committee, $id]);
    }

    public function archives(Request $request, CommunityCommittee $committee): View
    {
        $this->assertMayAccessCommittee($committee);
        $posts = $this->publicBoardService->list(
            'community_committee_archive',
            $request,
            10,
            $committee->name,
        );

        return view('subcommittee.archives', array_merge(
            $this->committeePageData($committee, '자료실', '03', 'community_committee_archive'),
            compact('posts'),
        ));
    }

    public function archivesShow(CommunityCommittee $committee, int $id): View
    {
        $this->assertMayAccessCommittee($committee);
        $name = $committee->name;
        $post = $this->publicBoardService->find('community_committee_archive', $id, $name);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('community_committee_archive', $id, $name);

        return view('subcommittee.archives_view', array_merge(
            $this->committeePageData($committee, '자료실', '03', 'community_committee_archive'),
            compact('post', 'prev', 'next'),
        ));
    }

    /**
     * @param  string|null  $targetBoardSlug  {@see Popup::COMMITTEE_TARGET_BOARDS} 키, null이면 위원회 팝업 없음
     * @return array<string, mixed>
     */
    private function committeePageData(CommunityCommittee $committee, string $dName, string $dNum, ?string $targetBoardSlug = null): array
    {
        $committeePopups = $targetBoardSlug !== null
            ? Popup::activeCommitteePopupsForBoard((int) $committee->id, $targetBoardSlug)
            : collect();

        return [
            'page_type' => 'professional',
            'gNum' => '03',
            'sNum' => '02',
            'dNum' => $dNum,
            'gName' => '산하위원회',
            'sName' => $committee->name,
            'dName' => $dName,
            'geName' => 'Subcommittee',
            'gSlug' => 'subcommittee_'.$committee->id,
            'committee' => $committee,
            'committeePopups' => $committeePopups,
        ];
    }

    private function assertMayAccessCommittee(CommunityCommittee $committee): void
    {
        if (! $this->isPublicCommittee($committee)) {
            throw new NotFoundHttpException();
        }

        $user = Auth::user();
        if ($user === null) {
            abort(403);
        }
        if ($user->isAdmin()) {
            return;
        }
        if (! $user->canAccessCommunityCommitteeId((string) $committee->id)) {
            abort(403);
        }
    }

    private function isPublicCommittee(CommunityCommittee $committee): bool
    {
        return $committee->visibility_yn === 'Y' && $committee->committee_type !== 'special';
    }
}

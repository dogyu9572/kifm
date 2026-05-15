<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PublicBoardService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberPlazaController extends Controller
{
    public function __construct(private readonly PublicBoardService $publicBoardService) {}

    public function societyNotices(Request $request): View
    {
        $posts = $this->publicBoardService->list('member_square_notices', $request, 10);

        return $this->render('society_notices', '01', '학회 공지', 'member_plaza_society_notices', compact('posts'));
    }

    public function societyNoticesShow(int $id): View
    {
        $post = $this->publicBoardService->find('member_square_notices', $id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('member_square_notices', $id);

        return $this->render('society_notices_view', '01', '학회 공지', 'member_plaza_society_notices_view', compact('post', 'prev', 'next'));
    }

    public function otherNotices(Request $request): View
    {
        $posts = $this->publicBoardService->list('other_notices', $request, 10);

        return $this->render('other_notices', '02', '기타 공지', 'member_plaza_other_notices', compact('posts'));
    }

    public function otherNoticesShow(int $id): View
    {
        $post = $this->publicBoardService->find('other_notices', $id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('other_notices', $id);

        return $this->render('other_notices_view', '02', '기타 공지', 'member_plaza_other_notices_view', compact('post', 'prev', 'next'));
    }

    public function societyAlbum(Request $request): View
    {
        $posts = $this->publicBoardService->list('member_square_album', $request, 8);

        return $this->render('society_album', '03', '학회 앨범', 'member_plaza_society_album', compact('posts'));
    }

    public function societyAlbumShow(int $id): View
    {
        $post = $this->publicBoardService->find('member_square_album', $id);
        if ($post === null) {
            throw new NotFoundHttpException();
        }

        ['prev' => $prev, 'next' => $next] = $this->publicBoardService->prevNext('member_square_album', $id);

        return $this->render('society_album_view', '03', '학회 앨범', 'member_plaza_society_album_view', compact('post', 'prev', 'next'));
    }

    public function feePaymentGuide(): View
    {
        return $this->render('fee_payment_guide', '04', '회비 납부 안내', 'member_plaza_fee_payment_guide');
    }

    private function render(string $view, string $sNum, string $sName, string $slug, array $extra = []): View
    {
        $page_type = 'professional';
        $gNum = '05';
        $gName = '회원광장';
        $geName = 'Member Plaza';
        $gSlug = $slug;

        return view(
            'member_plaza.' . $view,
            array_merge(compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'), $extra),
        );
    }
}

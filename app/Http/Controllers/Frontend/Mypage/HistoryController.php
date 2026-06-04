<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Services\Frontend\MypageBookmarkService;
use App\Services\Frontend\MypageFavoriteMenuService;
use App\Services\Frontend\MypageOnlineTrainingService;
use App\Services\Frontend\MypageParticipationHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    use RendersMypageViews;

    public function __construct(
        private readonly MypageParticipationHistoryService $participationService,
        private readonly MypageOnlineTrainingService $onlineTrainingService,
        private readonly MypageFavoriteMenuService $favoriteMenuService,
        private readonly MypageBookmarkService $bookmarkService,
    ) {}

    public function participation(Request $request): View
    {
        $user = $this->currentMember();
        $registrations = $this->participationService->paginate($user, $request);

        return $this->renderMypage('participation_history', '02', '참가내역 관리', 'participation_history', [
            'registrations' => $registrations,
            'paymentStatusLabels' => $this->participationService->paymentStatusLabels(),
            'paymentMethodLabels' => $this->participationService->paymentMethodLabels(),
            'filterYear' => $request->get('year'),
            'filterMonth' => $request->get('month'),
        ]);
    }

    public function participationView(Request $request): View
    {
        $id = (int) $request->query('id', 0);
        $registration = $this->participationService->findForMember($this->currentMember(), $id);
        abort_if($registration === null, 404);

        return $this->renderMypage('participation_history_view', '02', '참가내역 관리', 'participation_history_view', [
            'registration' => $registration,
            'paymentStatusLabels' => $this->participationService->paymentStatusLabels(),
            'paymentMethodLabels' => $this->participationService->paymentMethodLabels(),
        ]);
    }

    public function onlineTraining(Request $request): View
    {
        $user = $this->currentMember();
        $filterYear = $request->get('year') ?: $this->onlineTrainingService->latestEnrollmentYear($user);
        if (! $request->filled('year') && $filterYear) {
            $request->merge(['year' => $filterYear]);
        }

        $enrollments = $this->onlineTrainingService->paginate($user, $request);

        return $this->renderMypage('online_training', '03', '온라인 교육 수강내역', 'online_training', [
            'enrollments' => $enrollments,
            'statusLabels' => $this->onlineTrainingService->enrollmentStatusLabels(),
            'paymentStatusLabels' => $this->onlineTrainingService->paymentStatusLabels(),
            'filterYear' => $filterYear,
            'filterStatus' => $request->get('status', 'all'),
            'filterKeyword' => $request->get('keyword'),
        ]);
    }

    public function onlineTrainingView(Request $request): View
    {
        $id = (int) $request->query('id', 0);
        $enrollment = $this->onlineTrainingService->findForMember($this->currentMember(), $id);
        abort_if($enrollment === null, 404);

        return $this->renderMypage('online_training_view', '03', '온라인 교육 수강내역', 'online_training_view', [
            'enrollment' => $enrollment,
            'statusLabels' => $this->onlineTrainingService->enrollmentStatusLabels(),
            'paymentStatusLabels' => $this->onlineTrainingService->paymentStatusLabels(),
            'paymentMethodLabels' => $this->onlineTrainingService->paymentMethodLabels(),
            'receiptTypeLabels' => $this->onlineTrainingService->receiptTypeLabels(),
            'memberGradeLabels' => $this->onlineTrainingService->memberGradeLabels(),
            'examStatusLabels' => $this->onlineTrainingService->examStatusLabels(),
        ]);
    }

    public function favoriteMenu(): View
    {
        $user = $this->currentMember();

        return $this->renderMypage('favorite', '05', '즐겨찾는 메뉴', 'favorite_menu', [
            'menuGroups' => $this->favoriteMenuService->menuGroups(),
            'savedCodes' => $this->favoriteMenuService->savedCodesForUser($user),
            'maxFavorites' => $this->favoriteMenuService->maxFavorites(),
        ]);
    }

    public function favoriteMenuStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_codes' => ['nullable', 'array', 'max:20'],
            'menu_codes.*' => ['string', 'max:80'],
        ]);

        try {
            $this->favoriteMenuService->sync(
                $this->currentMember(),
                $validated['menu_codes'] ?? [],
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => '저장되었습니다.']);
    }

    public function bookmark(Request $request): View
    {
        $user = $this->currentMember();
        $bookmarks = $this->bookmarkService->paginate($user, $request);

        return $this->renderMypage('bookmark', '06', '북마크', 'bookmark', [
            'bookmarks' => $bookmarks,
            'contentTypeOptions' => $this->bookmarkService->contentTypes($user),
            'filterContentType' => $request->get('content_type', 'all'),
            'filterKeyword' => $request->get('keyword'),
        ]);
    }

    public function bookmarkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $count = $this->bookmarkService->destroyIds($this->currentMember(), $validated['ids']);

        return response()->json([
            'success' => true,
            'message' => $count.'건이 삭제되었습니다.',
        ]);
    }

    public function bookmarkToggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content_type' => ['required', 'string', 'max:40'],
            'content_id' => ['required', 'integer', 'min:1'],
            'title' => ['nullable', 'string', 'max:500'],
            'menu_label' => ['nullable', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:500'],
        ]);

        $bookmarked = $this->bookmarkService->toggle(
            $this->currentMember(),
            (string) $validated['content_type'],
            (int) $validated['content_id'],
            $validated['title'] ?? null,
            $validated['menu_label'] ?? null,
            $validated['url'] ?? null,
        );

        return response()->json([
            'success' => true,
            'bookmarked' => $bookmarked,
            'message' => $bookmarked ? '북마크에 저장되었습니다.' : '북마크가 해제되었습니다.',
        ]);
    }
}

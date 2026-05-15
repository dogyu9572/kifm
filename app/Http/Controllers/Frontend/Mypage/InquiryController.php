<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Http\Requests\FrontendMypageInquiryStoreRequest;
use App\Services\Frontend\MypageInquiryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    use RendersMypageViews;

    public function __construct(
        private readonly MypageInquiryService $inquiryService,
    ) {}

    public function index(): View
    {
        $posts = $this->inquiryService->paginateForMember($this->currentMember());

        return $this->renderMypage('inquiry', '04', '1:1 문의', 'inquiry_list', [
            'posts' => $posts,
        ]);
    }

    public function show(Request $request): View
    {
        $id = (int) $request->query('id', 0);
        $detail = $this->inquiryService->findDetailForMember($this->currentMember(), $id);
        abort_if($detail === null, 404);

        return $this->renderMypage('inquiry_view', '04', '1:1 문의', 'inquiry_view', $detail);
    }

    public function create(): View
    {
        return $this->renderMypage('inquiry_write', '04', '1:1 문의', 'inquiry_write');
    }

    public function edit(Request $request): View
    {
        $id = (int) $request->query('id', 0);
        $member = $this->currentMember();
        $post = $this->inquiryService->findForMember($member, $id);
        abort_if($post === null || ! $this->inquiryService->isEditableByMember($member, $post), 403);

        return $this->renderMypage('inquiry_write', '04', '1:1 문의', 'inquiry_write', [
            'post' => $post,
        ]);
    }

    public function store(FrontendMypageInquiryStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $id = $this->inquiryService->create(
            $this->currentMember(),
            $validated['title'],
            $validated['content'],
        );

        return redirect()->route('mypage.inquiry_view', ['id' => $id]);
    }

    public function update(FrontendMypageInquiryStoreRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();
        $this->inquiryService->update(
            $this->currentMember(),
            $id,
            $validated['title'],
            $validated['content'],
        );

        return redirect()->route('mypage.inquiry_view', ['id' => $id]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->inquiryService->delete($this->currentMember(), $id);

        return redirect()->route('mypage.inquiry');
    }
}

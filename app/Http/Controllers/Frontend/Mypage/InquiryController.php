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

    public function index(Request $request): View
    {
        $posts = $this->inquiryService->paginateForMember($this->currentMember(), $request);

        return $this->renderMypage('inquiry', '04', '1:1 문의', 'inquiry_list', [
            'posts' => $posts,
            'filters' => $this->inquiryService->filters($request),
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
            'attachments' => $post->display_attachments ?? [],
        ]);
    }

    public function store(FrontendMypageInquiryStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $id = $this->inquiryService->create(
            $this->currentMember(),
            $validated['title'],
            $validated['content'],
            $request->file('attachments'),
        );

        return redirect()
            ->route('mypage.inquiry_view', ['id' => $id])
            ->with('alert', "문의하신 내용이 접수 되었습니다.\n최대한 빠른 시일내에 답변드리겠습니다.");
    }

    public function update(FrontendMypageInquiryStoreRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();
        $this->inquiryService->update(
            $this->currentMember(),
            $id,
            $validated['title'],
            $validated['content'],
            $request->file('attachments'),
            $validated['delete_attachments'] ?? [],
        );

        return redirect()->route('mypage.inquiry_view', ['id' => $id]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->inquiryService->delete($this->currentMember(), $id);

        return redirect()->route('mypage.inquiry');
    }
}

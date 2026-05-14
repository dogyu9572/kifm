<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\OneOnOneInquiryRequest;
use App\Models\OneOnOneInquiry;
use App\Services\Backoffice\OneOnOneInquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OneOnOneInquiryController extends Controller
{
    public function __construct(
        protected OneOnOneInquiryService $oneOnOneInquiryService
    ) {}

    public function index(Request $request): View
    {
        $inquiries = $this->oneOnOneInquiryService->paginateFiltered($request);

        return view('backoffice.one-on-one-inquiries.index', [
            'inquiries' => $inquiries,
            'perPage' => $inquiries->perPage(),
            'statusLabels' => OneOnOneInquiryService::statusLabels(),
            'searchFieldLabels' => OneOnOneInquiryService::searchFieldLabels(),
        ]);
    }

    public function edit(Request $request, OneOnOneInquiry $oneOnOneInquiry): View
    {
        $oneOnOneInquiry->load(['user', 'answerer']);

        return view('backoffice.one-on-one-inquiries.edit', [
            'inquiry' => $oneOnOneInquiry,
            'cancelUrl' => $this->cancelUrl($request),
            'statusLabels' => OneOnOneInquiryService::statusLabels(),
        ]);
    }

    public function update(OneOnOneInquiryRequest $request, OneOnOneInquiry $oneOnOneInquiry): RedirectResponse
    {
        $validated = $request->validated();
        $uploaded = $request->file('answer_attachments', []);
        if (! is_array($uploaded)) {
            $uploaded = array_filter([$uploaded]);
        }

        $deleteIndexes = $validated['delete_answer_attachment_indexes'] ?? [];
        if (! is_array($deleteIndexes)) {
            $deleteIndexes = [];
        }

        $this->oneOnOneInquiryService->updateAnswer(
            $oneOnOneInquiry,
            $validated,
            $uploaded,
            $deleteIndexes,
            $request->user()?->id
        );

        return redirect()->route('backoffice.one-on-one-inquiries.index');
    }

    public function destroy(OneOnOneInquiry $oneOnOneInquiry): RedirectResponse
    {
        $this->oneOnOneInquiryService->deleteOne($oneOnOneInquiry);

        return redirect()
            ->route('backoffice.one-on-one-inquiries.index')
            ->with('success', '문의가 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inquiry_ids' => ['required', 'array'],
            'inquiry_ids.*' => ['integer', 'exists:one_on_one_inquiries,id'],
        ]);

        $deleted = $this->oneOnOneInquiryService->deleteMany($validated['inquiry_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted.'건의 문의가 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    protected function cancelUrl(Request $request): string
    {
        $fallback = route('backoffice.one-on-one-inquiries.index');
        $raw = $request->query('return_url');
        if (! is_string($raw) || $raw === '') {
            return $fallback;
        }

        $decoded = urldecode($raw);
        if (str_starts_with($decoded, '/backoffice/') && ! str_starts_with($decoded, '//')) {
            return $decoded;
        }

        $parts = parse_url($decoded);
        if (! empty($parts['path']) && str_starts_with($parts['path'], '/backoffice/')) {
            $query = isset($parts['query']) ? '?'.$parts['query'] : '';

            return $parts['path'].$query;
        }

        return $fallback;
    }
}

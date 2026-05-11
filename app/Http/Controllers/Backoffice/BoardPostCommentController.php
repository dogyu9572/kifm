<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoardPostCommentRequest;
use App\Models\Board;
use App\Models\BoardComment;
use App\Services\Backoffice\BoardPostCommentService;
use Illuminate\Http\Request;

class BoardPostCommentController extends Controller
{
    public function __construct(
        private BoardPostCommentService $boardPostCommentService
    ) {}

    public function store(BoardPostCommentRequest $request, string $slug, $post)
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        if (! $board->enable_comments) {
            abort(404);
        }

        $postId = (int) $post;
        if (! $this->boardPostCommentService->postExists($slug, $postId)) {
            abort(404);
        }

        $parentId = $request->input('parent_id') !== null ? (int) $request->input('parent_id') : null;

        $this->boardPostCommentService->store(
            $slug,
            $postId,
            (string) $request->input('content'),
            $parentId,
            (int) $request->user()->id,
            (string) ($request->user()->name ?? '관리자')
        );

        return redirect()
            ->route('backoffice.board-posts.edit', [$slug, $postId])
            ->with('success', '댓글이 등록되었습니다.');
    }

    public function update(Request $request, string $slug, $post, BoardComment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:65535',
        ], [], ['content' => '댓글 내용']);

        $board = Board::where('slug', $slug)->firstOrFail();
        if (! $board->enable_comments) {
            abort(404);
        }

        $postId = (int) $post;
        $this->boardPostCommentService->updateComment($comment, $slug, $postId, (string) $request->input('content'));

        return redirect()
            ->route('backoffice.board-posts.edit', [$slug, $postId])
            ->with('success', '댓글이 수정되었습니다.');
    }

    public function destroy(string $slug, $post, BoardComment $comment)
    {
        $board = Board::where('slug', $slug)->firstOrFail();
        if (! $board->enable_comments) {
            abort(404);
        }

        $postId = (int) $post;
        $this->boardPostCommentService->deleteComment($comment, $slug, $postId);

        return redirect()
            ->route('backoffice.board-posts.edit', [$slug, $postId])
            ->with('success', '댓글이 삭제되었습니다.');
    }
}

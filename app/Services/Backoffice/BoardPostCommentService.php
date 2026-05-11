<?php

namespace App\Services\Backoffice;

use App\Models\BoardComment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BoardPostCommentService
{
    /**
     * 동적 게시글 테이블에 해당 id 행이 있는지 확인
     */
    public function postExists(string $slug, int $postId): bool
    {
        $table = 'board_'.$slug;
        if (! DB::getSchemaBuilder()->hasTable($table)) {
            return false;
        }

        return DB::table($table)->where('id', $postId)->exists();
    }

    /**
     * 루트 댓글과 1단계 답글(자식)을 로드합니다.
     */
    public function listRootWithReplies(string $slug, int $postId): Collection
    {
        return BoardComment::query()
            ->forPost($slug, $postId)
            ->whereNull('parent_id')
            ->with(['replies' => function ($q) {
                $q->orderBy('id');
            }])
            ->orderBy('id')
            ->get();
    }

    /**
     * 목록용: post_id별 전체 댓글 수(삭제 제외)
     *
     * @param  array<int>  $postIds
     * @return array<int, int> post_id => count
     */
    public function commentCountsForPosts(string $slug, array $postIds): array
    {
        if ($postIds === []) {
            return [];
        }

        return BoardComment::query()
            ->forPostSlug($slug)
            ->whereIn('post_id', $postIds)
            ->selectRaw('post_id, count(*) as cnt')
            ->groupBy('post_id')
            ->pluck('cnt', 'post_id')
            ->map(fn ($c) => (int) $c)
            ->all();
    }

    public function store(
        string $slug,
        int $postId,
        string $content,
        ?int $parentId,
        int $userId,
        string $authorName
    ): BoardComment {
        $depth = 0;
        if ($parentId !== null) {
            $parent = BoardComment::query()
                ->forPost($slug, $postId)
                ->whereKey($parentId)
                ->firstOrFail();
            $depth = min((int) $parent->depth + 1, 10);
        }

        return BoardComment::create([
            'board_slug' => $slug,
            'post_id' => $postId,
            'parent_id' => $parentId,
            'user_id' => $userId,
            'author_name' => $authorName,
            'password' => null,
            'content' => $content,
            'depth' => $depth,
            'is_secret' => false,
            'attachments' => null,
        ]);
    }

    public function updateComment(BoardComment $comment, string $slug, int $postId, string $content): void
    {
        if ($comment->board_slug !== $slug || (int) $comment->post_id !== $postId) {
            abort(404);
        }
        $comment->update(['content' => $content]);
    }

    public function deleteComment(BoardComment $comment, string $slug, int $postId): void
    {
        if ($comment->board_slug !== $slug || (int) $comment->post_id !== $postId) {
            abort(404);
        }

        $ids = $this->collectSubtreeIds($comment);
        BoardComment::query()->whereIn('id', $ids)->delete();
    }

    /**
     * @return array<int>
     */
    private function collectSubtreeIds(BoardComment $root): array
    {
        $all = [$root->id];
        $queue = [$root->id];
        while ($queue !== []) {
            $pid = (int) array_shift($queue);
            $childIds = BoardComment::query()
                ->forPost($root->board_slug, (int) $root->post_id)
                ->where('parent_id', $pid)
                ->pluck('id')
                ->all();
            foreach ($childIds as $cid) {
                $cid = (int) $cid;
                $all[] = $cid;
                $queue[] = $cid;
            }
        }

        return $all;
    }

    /**
     * 게시글 삭제 시 해당 글의 댓글 전부 소프트 삭제
     */
    public function softDeleteAllForPost(string $slug, int $postId): void
    {
        BoardComment::query()
            ->forPostSlug($slug)
            ->where('post_id', $postId)
            ->delete();
    }

    /**
     * 일괄 게시글 삭제 시 댓글 정리
     *
     * @param  array<int>  $postIds
     */
    public function softDeleteAllForPosts(string $slug, array $postIds): void
    {
        if ($postIds === []) {
            return;
        }
        BoardComment::query()
            ->forPostSlug($slug)
            ->whereIn('post_id', $postIds)
            ->delete();
    }
}

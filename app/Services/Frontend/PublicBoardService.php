<?php

namespace App\Services\Frontend;

use App\Models\Board;
use App\Models\CommunityCommittee;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublicBoardService
{
    /**
     * 사용자 페이지용 게시글 목록 (공개 글만).
     *
     * @param string $slug 게시판 슬러그 (예: general_archive)
     * @param string|null $committeeCategory 위원회 게시판일 때 `community_committees.name` 과 일치하는 category 필터
     */
    public function list(string $slug, Request $request, int $perPage = 10, ?string $committeeCategory = null): LengthAwarePaginator
    {
        if (! Schema::hasTable($this->table($slug))) {
            return new LengthAwarePaginator([], 0, $perPage, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        $query = DB::table($this->table($slug))
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false);

        $this->applyCommitteeCategoryFilter($query, $slug, $committeeCategory);
        $this->applyKeywordFilter($query, $request);

        if ($request->has('per_page')) {
            $perPage = $this->normalizePerPage($request->get('per_page'));
        }

        return $query
            ->orderBy('is_notice', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * 공개 글 전체를 컬렉션으로 반환한다.
     * 페이지네이션이 필요 없는 한 화면 표시(연혁·임원진 등)에 사용한다.
     */
    public function listAll(string $slug): \Illuminate\Support\Collection
    {
        return DB::table($this->table($slug))
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * 단일 페이지 보드의 최신 1건을 조회한다.
     * 인사말·회칙·약관·연혁 등 single page 보드에서 사용한다.
     * view_count 는 증분하지 않는다.
     */
    public function findSingle(string $slug, ?int $id = null): ?object
    {
        $query = DB::table($this->table($slug))
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false);

        if ($id !== null) {
            $query->where('id', $id);
        }

        return $query->orderBy('id', 'desc')->first();
    }

    /**
     * 단건 조회 + view_count 1 증가.
     * 비공개/삭제글은 404 처리되도록 null 을 반환한다.
     */
    public function find(string $slug, int $id, ?string $committeeCategory = null): ?object
    {
        $query = DB::table($this->table($slug))
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false)
            ->where('id', $id);

        $this->applyCommitteeCategoryFilter($query, $slug, $committeeCategory);

        $post = $query->first();

        if ($post === null) {
            return null;
        }

        DB::table($this->table($slug))->where('id', $id)->increment('view_count');
        $post->view_count = (int) ($post->view_count ?? 0) + 1;

        return $post;
    }

    /**
     * 이전/다음 글 (목록 정렬과 동일한 기준: 공지 우선, 최신순).
     * 화면에서는 등록일이 더 오래된 글을 "이전 글", 더 최신 글을 "다음 글"로 노출한다.
     *
     * @return array{prev: ?object, next: ?object}
     */
    public function prevNext(string $slug, int $id, ?string $committeeCategory = null): array
    {
        $base = function () use ($slug, $committeeCategory) {
            $q = DB::table($this->table($slug))
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->where('is_secret', false);
            $this->applyCommitteeCategoryFilter($q, $slug, $committeeCategory);

            return $q;
        };

        $current = $base()->where('id', $id)->first();
        if ($current === null) {
            return ['prev' => null, 'next' => null];
        }

        $prev = $base()
            ->where('created_at', '<', $current->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        $next = $base()
            ->where('created_at', '>', $current->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        return ['prev' => $prev, 'next' => $next];
    }

    public function createCommitteeDiscussion(CommunityCommittee $committee, array $data, int $userId, string $authorName): int
    {
        $title = trim((string) $data['title']);

        return DB::table($this->table('community_committee_discussions'))->insertGetId([
            'user_id' => $userId,
            'title' => $title,
            'content' => $title,
            'author_name' => $authorName,
            'password' => null,
            'is_notice' => false,
            'is_secret' => false,
            'category' => $committee->name,
            'attachments' => json_encode([]),
            'view_count' => 0,
            'sort_order' => 0,
            'custom_fields' => null,
            'thumbnail' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function table(string $slug): string
    {
        return 'board_'.$slug;
    }

    /**
     * 위원회 전용 게시판에서만 category(위원회명) 필터를 적용한다.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    private function applyCommitteeCategoryFilter($query, string $slug, ?string $committeeCategory): void
    {
        if ($committeeCategory === null || $committeeCategory === '') {
            return;
        }
        if (! Board::usesCommunityCommitteeCategories($slug)) {
            return;
        }
        $query->where('category', $committeeCategory);
    }

    private function applyKeywordFilter($query, Request $request): void
    {
        $keyword = trim((string) $request->get('keyword', ''));
        if ($keyword === '') {
            return;
        }

        $searchType = $request->get('search_type', 'all');
        $like = '%'.$keyword.'%';

        $query->where(function ($q) use ($searchType, $like) {
            if ($searchType === 'title') {
                $q->where('title', 'like', $like);
            } elseif ($searchType === 'content') {
                $q->where('content', 'like', $like);
            } else {
                $q->where('title', 'like', $like)
                  ->orWhere('content', 'like', $like);
            }
        });
    }

    private function normalizePerPage($value): int
    {
        $value = (int) $value;

        return in_array($value, [10, 20, 50, 100], true) ? $value : 10;
    }
}

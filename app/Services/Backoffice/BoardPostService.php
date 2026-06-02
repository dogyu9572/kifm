<?php

namespace App\Services\Backoffice;

use App\Models\Board;
use App\Support\BackofficeFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardPostService
{
    public function __construct(
        private BoardPostCommentService $boardPostCommentService
    ) {}

    /**
     * 동적 테이블명 생성
     */
    public function getTableName(string $slug): string
    {
        return 'board_'.$slug;
    }

    /**
     * 게시글 목록 조회
     */
    public function getPosts(string $slug, Request $request)
    {
        $query = DB::table($this->getTableName($slug));

        $this->applySearchFilters($query, $request, $slug);

        // 목록 개수 설정
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 15;

        // 정렬 기능이 활성화된 게시판인지 확인
        $board = Board::where('slug', $slug)->first();
        if ($board && $board->enable_sorting) {
            // 정렬 기능 활성화: sort_order 내림차순 (큰 값이 위), 공지글, 최신순
            $posts = $query->orderBy('sort_order', 'desc')
                ->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
        } else {
            // 정렬 기능 비활성화: 공지글, 최신순
            $posts = $query->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
        }

        $this->transformDates($posts);

        if ($board && $board->enable_comments) {
            $this->attachCommentCounts($posts, $slug);
        }

        if ($slug === 'academic_notices') {
            $this->attachAcademicEvents($posts);
        }

        return $posts;
    }

    /**
     * academic_notices 목록의 각 게시글에 event_title, event_year 를 매핑합니다.
     */
    private function attachAcademicEvents($posts): void
    {
        $eventIds = $posts->getCollection()
            ->pluck('event_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();

        if ($eventIds === []) {
            return;
        }

        $events = DB::table('academic_events')
            ->whereIn('id', $eventIds)
            ->get(['id', 'title', 'year'])
            ->keyBy('id');

        $posts->getCollection()->transform(function ($post) use ($events) {
            $eventId = isset($post->event_id) ? (int) $post->event_id : 0;
            $event = $events->get($eventId);
            $post->event_title = $event->title ?? null;
            $post->event_year = $event->year ?? null;

            return $post;
        });
    }

    /**
     * 목록에 댓글 수(comments_count)를 붙입니다.
     */
    private function attachCommentCounts($posts, string $slug): void
    {
        $ids = $posts->getCollection()->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();
        if ($ids === []) {
            return;
        }

        $counts = $this->boardPostCommentService->commentCountsForPosts($slug, $ids);
        $posts->getCollection()->transform(function ($post) use ($counts) {
            $post->comments_count = (int) ($counts[$post->id] ?? 0);

            return $post;
        });
    }

    /**
     * 검색 필터 적용
     */
    private function applySearchFilters($query, Request $request, string $slug): void
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if (in_array($slug, ['general_archive', 'academic_archive'], true)) {
            if ($request->filled('visibility')) {
                if ($request->visibility === 'public') {
                    $query->where('is_active', true);
                } elseif ($request->visibility === 'private') {
                    $query->where('is_active', false);
                }
            }

            $memberGradeFilter = $request->input('member_grade', $request->input('member_type'));
            if (! empty($memberGradeFilter)) {
                $memberTypeValues = $this->resolveMemberTypeFilterValues((string) $memberGradeFilter);
                $query->where(function ($subQuery) use ($memberTypeValues) {
                    // 체크박스 배열/단일 문자열 저장 케이스 모두 대응
                    foreach ($memberTypeValues as $index => $memberType) {
                        $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                        $subQuery->{$method}(
                            "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"member_type\"')) = ?",
                            [$memberType]
                        );
                        $subQuery->orWhereRaw(
                            "JSON_CONTAINS(JSON_EXTRACT(custom_fields, '$.\"member_type\"'), JSON_QUOTE(?))",
                            [$memberType]
                        );
                    }
                });
            }

            if ($request->filled('executive_access')) {
                $executiveAccess = (string) $request->executive_access;
                $query->where(function ($subQuery) use ($executiveAccess) {
                    // 권장 키(is_executive_public) + 호환 키(executive_access) 동시 지원
                    $subQuery->whereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"is_executive_public\"')) = ?",
                        [$executiveAccess]
                    )->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"executive_access\"')) = ?",
                        [$executiveAccess]
                    );
                });
            }
        }

        if ($slug === 'member_archive') {
            if ($request->filled('visibility')) {
                if ($request->visibility === 'public') {
                    $query->where('is_active', true);
                } elseif ($request->visibility === 'private') {
                    $query->where('is_active', false);
                }
            }

            if ($request->filled('member_grade')) {
                $memberGrade = (string) $request->member_grade;
                $query->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"member_grade\"')) = ?",
                    [$memberGrade]
                );
            }

            if ($request->filled('archive_type')) {
                $archiveType = (string) $request->archive_type;
                $query->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"archive_type\"')) = ?",
                    [$archiveType]
                );
            }
        }

        if (in_array($slug, ['member_square_notices', 'inquiry_qna', 'academic_journals'], true) && $request->filled('visibility')) {
            if ($request->visibility === 'public') {
                $query->where('is_active', true);
            } elseif ($request->visibility === 'private') {
                $query->where('is_active', false);
            }
        }

        if ($slug === 'academic_notices') {
            if ($request->filled('event_id')) {
                $query->where('event_id', (int) $request->event_id);
            }

            if ($request->filled('visibility')) {
                if ($request->visibility === 'public') {
                    $query->where('is_active', true);
                } elseif ($request->visibility === 'private') {
                    $query->where('is_active', false);
                }
            }
        }

        if ($slug === 'society_history') {
            if ($request->filled('year')) {
                $query->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"history_year\"')) = ?",
                    [(string) $request->year]
                );
            }

            if ($request->filled('month')) {
                $query->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"history_month\"')) = ?",
                    [(string) $request->month]
                );
            }

            if ($request->filled('visibility')) {
                if ($request->visibility === 'public') {
                    $query->where('is_active', true);
                } elseif ($request->visibility === 'private') {
                    $query->where('is_active', false);
                }
            }
        }

        if ($slug === 'academic_conference_history') {
            if ($request->filled('visibility')) {
                if ($request->visibility === 'public') {
                    $query->where('is_active', true);
                } elseif ($request->visibility === 'private') {
                    $query->where('is_active', false);
                }
            }
        }

        if ($request->filled('keyword')) {
            $this->applyKeywordSearch($query, $request->keyword, $request->search_type, $slug);
        }
    }

    /**
     * 회원설정 필터값(영문/한글)을 실제 저장값 배열로 변환합니다.
     */
    private function resolveMemberTypeFilterValues(string $memberType): array
    {
        $memberTypeMap = [
            'associate' => '준회원',
            'regular' => '정회원',
            'lifetime' => '평생회원',
            'senior' => '시니어회원',
        ];

        if (isset($memberTypeMap[$memberType])) {
            return [$memberType, $memberTypeMap[$memberType]];
        }

        $reverseMatch = array_search($memberType, $memberTypeMap, true);
        if ($reverseMatch !== false) {
            return [$memberType, $reverseMatch];
        }

        return [$memberType];
    }

    /**
     * 키워드 검색 적용
     */
    private function applyKeywordSearch($query, string $keyword, ?string $searchType, string $slug): void
    {
        if ($searchType === 'title') {
            $query->where('title', 'like', "%{$keyword}%");
        } elseif ($searchType === 'keyword' && $slug === 'member_archive') {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        } elseif ($searchType === 'keyword' && $slug === 'inquiry_qna') {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author_name', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        } elseif ($searchType === 'keyword' && in_array($slug, ['society_history', 'academic_conference_history'], true)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        } elseif ($searchType === 'author') {
            $query->where('author_name', 'like', "%{$keyword}%");
        } elseif ($searchType === 'content') {
            $query->where('content', 'like', "%{$keyword}%");
        } elseif ($slug === 'member_archive') {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        } else {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author_name', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }
    }

    /**
     * 날짜 변환
     */
    private function transformDates($posts): void
    {
        $posts->getCollection()->transform(function ($post) {
            foreach (['created_at', 'updated_at'] as $dateField) {
                if (isset($post->$dateField) && is_string($post->$dateField)) {
                    $post->$dateField = Carbon::parse($post->$dateField);
                }
            }

            return $post;
        });
    }

    /**
     * 게시글 저장
     */
    public function storePost(string $slug, array $validated, Request $request, $board): int
    {
        $data = $this->preparePostData($validated, $request, $slug, $board);

        return DB::table($this->getTableName($slug))->insertGetId($data);
    }

    /**
     * 게시글 데이터 준비
     */
    private function preparePostData(array $validated, Request $request, string $slug, $board): array
    {
        // 정렬 기능 활성화된 게시판인 경우 자동으로 sort_order 설정
        $sortOrder = 0;
        if ($board && $board->enable_sorting) {
            $requestedSortOrder = $request->input('sort_order');
            if ($requestedSortOrder !== null && is_numeric($requestedSortOrder)) {
                $sortOrder = max(0, (int) $requestedSortOrder);
            } else {
                $sortOrder = $this->getNextSortOrder($slug);
            }
        }

        // is_active 필드 처리: 필드가 활성화되어 있고 요청에 있으면 사용, 없으면 기본값 true
        $isActive = true;
        if ($board && $board->isFieldEnabled('is_active')) {
            $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : true;
        }

        $data = [
            'user_id' => null,
            'author_name' => $validated['author_name'] ?? '관리자',
            'title' => $validated['title'],
            'content' => $this->sanitizeContent((string) ($validated['content'] ?? '')),
            'category' => $validated['category'] ?? null,
            'is_notice' => $request->has('is_notice'),
            'is_secret' => $request->has('is_secret'),
            'is_active' => $isActive,
            'password' => $validated['password'] ?? null,
            'thumbnail' => $this->handleThumbnail($request, $slug),
            'attachments' => json_encode($this->handleAttachments($request, $slug)),
            'custom_fields' => $this->getCustomFieldsJson($request, $board),
            'view_count' => (int) ($validated['view_count'] ?? 0),
            'sort_order' => $sortOrder,
            'created_at' => ! empty($validated['created_at'])
                ? Carbon::parse($validated['created_at'])
                : now(),
            'updated_at' => now(),
        ];

        if ($slug === 'academic_notices') {
            $data['event_id'] = $request->filled('event_id') ? (int) $request->input('event_id') : null;
        }

        return $data;
    }

    /**
     * 다음 정렬 순서 값을 계산합니다 (외부에서 호출 가능)
     */
    public function calculateNextSortOrder(string $slug): int
    {
        $maxSortOrder = DB::table($this->getTableName($slug))
            ->max('sort_order');

        return ($maxSortOrder ?? 0) + 1;
    }

    /**
     * 다음 정렬 순서 값을 가져옵니다 (정렬 기능 활성화된 게시판에만 사용)
     */
    private function getNextSortOrder(string $slug): int
    {
        return $this->calculateNextSortOrder($slug);
    }

    /**
     * HTML 내용 정리
     */
    private function sanitizeContent(string $content): string
    {
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><table><thead><tbody><tr><td><th><a><img><div><span><iframe><video><source>';

        return strip_tags($content, $allowedTags);
    }

    /**
     * 썸네일 처리
     */
    private function handleThumbnail(Request $request, string $slug): ?string
    {
        // 썸네일 제거 요청이 있는 경우
        if ($request->has('remove_thumbnail')) {
            return null;
        }

        // 새 썸네일이 업로드된 경우
        if ($request->hasFile('thumbnail')) {
            return BackofficeFile::storeWithOriginalName($request->file('thumbnail'), 'thumbnails/'.$slug, 'public');
        }

        // 기존 썸네일이 있는 경우 보존
        if ($request->has('existing_thumbnail')) {
            return $request->input('existing_thumbnail');
        }

        return null;
    }

    /**
     * 첨부파일 처리
     */
    private function handleAttachments(Request $request, string $slug): array
    {
        $attachments = [];
        $removeIndices = $request->input('remove_attachments', []);

        // 기존 첨부파일 보존 (제거 요청이 없는 것만)
        if ($request->has('existing_attachments')) {
            $existingAttachments = $request->input('existing_attachments', []);
            foreach ($existingAttachments as $index => $attachment) {
                // 제거 요청이 있는 인덱스는 제외
                if (in_array($index, $removeIndices)) {
                    continue;
                }

                if (is_string($attachment)) {
                    $attachment = json_decode($attachment, true);
                }
                if (is_array($attachment) && isset($attachment['name'], $attachment['path'])) {
                    $attachments[] = $attachment;
                }
            }
        }

        // 새 첨부파일 추가
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => BackofficeFile::storeWithOriginalName($file, 'uploads/'.$slug, 'public'),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        return $attachments;
    }

    /**
     * 커스텀 필드 JSON 생성
     */
    private function getCustomFieldsJson(Request $request, $board): ?string
    {
        $customFields = $this->processCustomFields($request, $board);

        return ! empty($customFields) ? json_encode($customFields) : null;
    }

    /**
     * 커스텀 필드 처리
     */
    private function processCustomFields(Request $request, $board): array
    {
        if (! $board->custom_fields_config || ! is_array($board->custom_fields_config)) {
            return [];
        }

        $customFields = [];
        foreach ($board->custom_fields_config as $fieldConfig) {
            $fieldName = $fieldConfig['name'];
            $customFields[$fieldName] = $request->input("custom_field_{$fieldName}");
        }

        return $customFields;
    }

    /**
     * 게시글 조회
     */
    public function getPost(string $slug, int $postId)
    {
        $post = DB::table($this->getTableName($slug))->where('id', $postId)->first();

        if (! $post) {
            return null;
        }

        $this->transformSinglePostDates($post);

        return $post;
    }

    /**
     * 단일 게시글 날짜 변환
     */
    private function transformSinglePostDates($post): void
    {
        foreach (['created_at', 'updated_at'] as $dateField) {
            if (isset($post->$dateField) && is_string($post->$dateField)) {
                $post->$dateField = Carbon::parse($post->$dateField);
            }
        }
    }

    /**
     * 게시글 수정
     */
    public function updatePost(string $slug, int $postId, array $validated, Request $request, $board): bool
    {
        // 기존 게시물 조회
        $existingPost = $this->getPost($slug, $postId);

        $data = $this->prepareUpdateData($validated, $request, $slug, $board, $existingPost);

        return DB::table($this->getTableName($slug))
            ->where('id', $postId)
            ->update($data);
    }

    /**
     * 수정 데이터 준비
     */
    private function prepareUpdateData(array $validated, Request $request, string $slug, $board, $existingPost = null): array
    {
        // is_active 필드 처리
        // 필드가 활성화되어 있고 요청에 있으면 사용
        // 필드가 비활성화되어 있으면 기존 값 유지 (수정 시)
        // 필드가 활성화되어 있지만 요청에 없으면 기본값 true
        $isActive = true;
        if ($board && $board->isFieldEnabled('is_active')) {
            $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : true;
        } elseif ($existingPost && isset($existingPost->is_active)) {
            // 필드가 비활성화되어 있으면 기존 값 유지
            $isActive = (bool) $existingPost->is_active;
        }

        $data = [
            'title' => $validated['title'],
            'content' => $this->sanitizeContent((string) ($validated['content'] ?? '')),
            'category' => $validated['category'] ?? null,
            'is_notice' => $request->has('is_notice'),
            'is_secret' => $request->has('is_secret'),
            'is_active' => $isActive,
            'author_name' => $validated['author_name'] ?? null,
            'password' => $validated['password'] ?? null,
            'thumbnail' => $this->handleThumbnail($request, $slug),
            'attachments' => json_encode($this->handleAttachments($request, $slug)),
            'custom_fields' => $this->getCustomFieldsJson($request, $board),
            'created_at' => ! empty($validated['created_at'])
                ? Carbon::parse($validated['created_at'])
                : ($existingPost->created_at ?? now()),
            'view_count' => (int) ($validated['view_count'] ?? ($existingPost->view_count ?? 0)),
            'sort_order' => $request->input('sort_order', 0),
            'updated_at' => now(),
        ];

        if ($slug === 'academic_notices') {
            $data['event_id'] = $request->filled('event_id') ? (int) $request->input('event_id') : null;
        }

        return $data;
    }

    /**
     * 게시글 삭제
     */
    public function deletePost(string $slug, int $postId): bool
    {
        $post = DB::table($this->getTableName($slug))->where('id', $postId)->first();

        if (! $post) {
            return false;
        }

        $this->deleteAttachments($post);

        $this->boardPostCommentService->softDeleteAllForPost($slug, $postId);

        return DB::table($this->getTableName($slug))->where('id', $postId)->delete();
    }

    /**
     * 첨부파일 삭제
     */
    private function deleteAttachments($post): void
    {
        if (! $post->attachments) {
            return;
        }

        $attachments = json_decode($post->attachments, true);
        if (! is_array($attachments)) {
            return;
        }

        foreach ($attachments as $attachment) {
            if (isset($attachment['path'])) {
                $filePath = storage_path('app/public/'.$attachment['path']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }

    /**
     * 일괄 삭제
     */
    public function bulkDelete(string $slug, array $postIds): int
    {
        $this->boardPostCommentService->softDeleteAllForPosts($slug, $postIds);

        return DB::table($this->getTableName($slug))->whereIn('id', $postIds)->delete();
    }
}

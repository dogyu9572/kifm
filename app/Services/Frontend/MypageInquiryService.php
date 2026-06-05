<?php

namespace App\Services\Frontend;

use App\Models\OneOnOneInquiry;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use stdClass;

/**
 * 마이페이지 1:1 문의 — 백오피스 「1:1 문의 관리」와 동일 저장소.
 *
 * - 사용 테이블: one_on_one_inquiries (OneOnOneInquiry)
 * - 사용 금지: board_inquiry_qna / 게시판 slug inquiry_qna (백오피스 「Q&A 관리」 전용, 별도 메뉴)
 */
class MypageInquiryService
{
    public function paginateForMember(User $user, ?Request $request = null, int $perPage = 20): LengthAwarePaginator
    {
        $filters = $this->filters($request);

        return OneOnOneInquiry::query()
            ->where('user_id', $user->id)
            ->when($filters['status'] !== 'all', function (Builder $query) use ($filters): void {
                if ($filters['status'] === 'answered') {
                    $query->where(function (Builder $statusQuery): void {
                        $statusQuery->where('answer_status', 'DONE')
                            ->orWhereNotNull('answer_content');
                    });
                    return;
                }

                $query->where(function (Builder $statusQuery): void {
                    $statusQuery->whereNull('answer_content')
                        ->where(function (Builder $pendingQuery): void {
                            $pendingQuery->whereNull('answer_status')
                                ->orWhere('answer_status', '!=', 'DONE');
                        });
                });
            })
            ->when($filters['keyword'] !== '', function (Builder $query) use ($filters): void {
                $keyword = '%' . $filters['keyword'] . '%';
                if ($filters['search_field'] === 'title') {
                    $query->where('title', 'like', $keyword);
                    return;
                }
                if ($filters['search_field'] === 'content') {
                    $query->where('content', 'like', $keyword);
                    return;
                }

                $query->where(function (Builder $searchQuery) use ($keyword): void {
                    $searchQuery->where('title', 'like', $keyword)
                        ->orWhere('content', 'like', $keyword);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (OneOnOneInquiry $inquiry) => $this->decorateRow($inquiry));
    }

    /** @return array{status: string, search_field: string, keyword: string} */
    public function filters(?Request $request = null): array
    {
        $status = (string) $request?->query('status', 'all');
        $searchField = (string) $request?->query('search_field', 'all');

        return [
            'status' => in_array($status, ['all', 'pending', 'answered'], true) ? $status : 'all',
            'search_field' => in_array($searchField, ['all', 'title', 'content'], true) ? $searchField : 'all',
            'keyword' => trim((string) $request?->query('keyword', '')),
        ];
    }

    /**
     * @return array{
     *   post: stdClass,
     *   attachments: array<int, array<string, mixed>>,
     *   is_answered: bool,
     *   answer_comment: stdClass|null,
     *   answer_attachments: array<int, array<string, mixed>>
     * }|null
     */
    public function findDetailForMember(User $user, int $id): ?array
    {
        $inquiry = $this->findModelForMember($user, $id);
        if ($inquiry === null) {
            return null;
        }

        $post = $this->decorateRow($inquiry);
        $attachments = $this->parseAttachments($inquiry->attachments);
        $isAnswered = $this->isAnswered($inquiry);
        $answerComment = $isAnswered ? $this->buildAnswerDisplay($inquiry) : null;
        $answerAttachments = $isAnswered
            ? $this->parseAttachments($inquiry->answer_attachments)
            : [];

        return [
            'post' => $post,
            'attachments' => $attachments,
            'is_answered' => $isAnswered,
            'answer_comment' => $answerComment,
            'answer_attachments' => $answerAttachments,
        ];
    }

    public function findForMember(User $user, int $id): ?stdClass
    {
        $inquiry = $this->findModelForMember($user, $id);

        return $inquiry ? $this->decorateRow($inquiry) : null;
    }

    public function isEditableByMember(User $user, stdClass $post): bool
    {
        $inquiry = $this->findModelForMember($user, (int) $post->id);
        if ($inquiry === null) {
            return false;
        }

        return ! $this->isAnswered($inquiry);
    }

    public function update(User $user, int $id, string $title, string $content): void
    {
        $inquiry = $this->findModelForMember($user, $id);
        if ($inquiry === null || $this->isAnswered($inquiry)) {
            abort(403);
        }

        $inquiry->update([
            'title' => $title,
            'content' => $content,
            'content_format' => 'text',
        ]);
    }

    public function delete(User $user, int $id): void
    {
        $inquiry = $this->findModelForMember($user, $id);
        if ($inquiry === null || $this->isAnswered($inquiry)) {
            abort(403);
        }

        $inquiry->delete();
    }

    public function create(User $user, string $title, string $content): int
    {
        $inquiry = OneOnOneInquiry::query()->create([
            'user_id' => $user->id,
            'member_name' => $user->name,
            'member_email' => $user->email,
            'title' => $title,
            'content' => $content,
            'content_format' => 'text',
            'answer_status' => 'PENDING',
            'client_ip' => request()->ip(),
        ]);

        return (int) $inquiry->id;
    }

    private function findModelForMember(User $user, int $id): ?OneOnOneInquiry
    {
        return OneOnOneInquiry::query()
            ->with('answerer')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
    }

    private function isAnswered(OneOnOneInquiry $inquiry): bool
    {
        if ($inquiry->answer_status === 'DONE') {
            return true;
        }

        return filled($inquiry->answer_content);
    }

    private function buildAnswerDisplay(OneOnOneInquiry $inquiry): ?stdClass
    {
        if (! filled($inquiry->answer_content)) {
            return null;
        }

        $display = new stdClass;
        $display->author_name = $inquiry->answerer?->name ?: '담당자';
        $display->content = $inquiry->answer_content;
        $display->created_at = $inquiry->answered_at ?? $inquiry->updated_at;

        return $display;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseAttachments(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        if (! is_array($decoded)) {
            return [];
        }

        $items = [];
        foreach ($decoded as $attachment) {
            if (! is_array($attachment) || empty($attachment['path'])) {
                continue;
            }

            $name = $attachment['name'] ?? $attachment['original_name'] ?? null;
            if ($name === null || $name === '') {
                continue;
            }

            $items[] = [
                'path' => $attachment['path'],
                'name' => $name,
                'size' => $attachment['size'] ?? null,
            ];
        }

        return $items;
    }

    private function decorateRow(OneOnOneInquiry $inquiry): stdClass
    {
        $row = new stdClass;
        $row->id = $inquiry->id;
        $row->title = $inquiry->title;
        $row->content = $inquiry->content;
        $row->created_at = $inquiry->created_at;
        $row->attachments = $inquiry->attachments;

        $isAnswered = $this->isAnswered($inquiry);
        $row->reply_status = $isAnswered ? 'answered' : 'waiting';
        $row->reply_status_label = $isAnswered ? '답변완료' : '답변대기';
        $row->reply_status_class = $isAnswered ? 'end' : 'ing';
        $row->created_at_formatted = $this->formatDate($inquiry->created_at);

        return $row;
    }

    private function formatDate(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return Carbon::parse($value)->format('Y.m.d');
    }
}

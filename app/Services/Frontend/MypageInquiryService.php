<?php

namespace App\Services\Frontend;

use App\Models\OneOnOneInquiry;
use App\Models\User;
use App\Support\BackofficeFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use stdClass;
use Throwable;

/**
 * 마이페이지 1:1 문의 — 백오피스 「1:1 문의 관리」와 동일 저장소.
 *
 * - 사용 테이블: one_on_one_inquiries (OneOnOneInquiry)
 * - 사용 금지: board_inquiry_qna / 게시판 slug inquiry_qna (백오피스 「Q&A 관리」 전용, 별도 메뉴)
 */
class MypageInquiryService
{
    private const ATTACHMENT_DISK = 'public';
    private const ATTACHMENT_DIR = 'mypage/one-on-one-inquiries/attachments';

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

    /**
     * @param  array<int, UploadedFile>|UploadedFile|null  $uploadedFiles
     * @param  array<int, int>  $deleteAttachmentIndexes
     */
    public function update(
        User $user,
        int $id,
        string $title,
        string $content,
        array|UploadedFile|null $uploadedFiles = null,
        array $deleteAttachmentIndexes = [],
    ): void
    {
        $inquiry = $this->findModelForMember($user, $id);
        if ($inquiry === null || $this->isAnswered($inquiry)) {
            abort(403);
        }

        DB::transaction(function () use ($inquiry, $title, $content, $uploadedFiles, $deleteAttachmentIndexes): void {
            $inquiry->title = $title;
            $inquiry->content = $content;
            $inquiry->content_format = 'text';
            $this->removeAttachmentsByIndex($inquiry, $deleteAttachmentIndexes);
            $this->appendAttachments($inquiry, $uploadedFiles);
            $inquiry->save();
        });
    }

    public function delete(User $user, int $id): void
    {
        $inquiry = $this->findModelForMember($user, $id);
        if ($inquiry === null || $this->isAnswered($inquiry)) {
            abort(403);
        }

        DB::transaction(function () use ($inquiry): void {
            $this->cleanupAttachments($inquiry);
            $inquiry->delete();
        });
    }

    /**
     * @param  array<int, UploadedFile>|UploadedFile|null  $uploadedFiles
     */
    public function create(User $user, string $title, string $content, array|UploadedFile|null $uploadedFiles = null): int
    {
        $inquiry = DB::transaction(function () use ($user, $title, $content, $uploadedFiles): OneOnOneInquiry {
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

            $this->appendAttachments($inquiry, $uploadedFiles);
            $inquiry->save();

            return $inquiry;
        });

        return (int) $inquiry->id;
    }

    /**
     * @param  array<int, UploadedFile>|UploadedFile|null  $uploadedFiles
     */
    private function appendAttachments(OneOnOneInquiry $inquiry, array|UploadedFile|null $uploadedFiles): void
    {
        $files = $this->normalizeUploadedFiles($uploadedFiles);
        if ($files === []) {
            return;
        }

        $existing = is_array($inquiry->attachments) ? $inquiry->attachments : [];

        foreach ($files as $file) {
            $storedPath = $this->storeInquiryAttachment($inquiry, $file);
            if ($storedPath === null) {
                continue;
            }

            $existing[] = [
                'path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ];
        }

        if (count($existing) > 5) {
            $removed = array_slice($existing, 0, count($existing) - 5);
            foreach ($removed as $attachment) {
                $path = is_array($attachment) ? ($attachment['path'] ?? null) : null;
                if (is_string($path) && $path !== '' && Storage::disk(self::ATTACHMENT_DISK)->exists($path)) {
                    Storage::disk(self::ATTACHMENT_DISK)->delete($path);
                }
            }
            $existing = array_slice($existing, -5);
        }

        $inquiry->attachments = $existing === [] ? null : array_values($existing);
    }

    private function storeInquiryAttachment(OneOnOneInquiry $inquiry, UploadedFile $file): ?string
    {
        try {
            $storedPath = BackofficeFile::storeWithOriginalName($file, self::ATTACHMENT_DIR, self::ATTACHMENT_DISK);
            if (is_string($storedPath) && $storedPath !== '') {
                return $storedPath;
            }

            Log::warning('mypage inquiry attachment store returned empty path', [
                'inquiry_id' => $inquiry->id,
                'original_name' => $file->getClientOriginalName(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
                'size' => $file->getSize(),
                'real_path' => $file->getRealPath(),
                'is_readable' => is_string($file->getRealPath()) && is_readable($file->getRealPath()),
            ]);
        } catch (Throwable $exception) {
            Log::error('mypage inquiry attachment store failed', [
                'inquiry_id' => $inquiry->id,
                'original_name' => $file->getClientOriginalName(),
                'message' => $exception->getMessage(),
            ]);
        }

        $fallbackPath = trim(self::ATTACHMENT_DIR, '/') . '/'
            . Str::random(24) . '__' . $this->sanitizeAttachmentName($file->getClientOriginalName());

        try {
            $realPath = $file->getRealPath();
            if (is_string($realPath) && is_readable($realPath)) {
                $stream = fopen($realPath, 'r');
                try {
                    if ($stream !== false && Storage::disk(self::ATTACHMENT_DISK)->put($fallbackPath, $stream)) {
                        return $fallbackPath;
                    }
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
            }

            $storedPath = Storage::disk(self::ATTACHMENT_DISK)
                ->putFileAs(trim(self::ATTACHMENT_DIR, '/'), $file, basename($fallbackPath));

            return is_string($storedPath) && $storedPath !== '' ? $storedPath : null;
        } catch (Throwable $exception) {
            Log::error('mypage inquiry attachment fallback store failed', [
                'inquiry_id' => $inquiry->id,
                'original_name' => $file->getClientOriginalName(),
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function sanitizeAttachmentName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[\\\\\\/\\x00-\\x1F\\x7F]+/u', '_', $name) ?: 'file';
        $name = trim($name);

        return $name !== '' ? $name : 'file';
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function normalizeUploadedFiles(array|UploadedFile|null $uploadedFiles): array
    {
        if ($uploadedFiles instanceof UploadedFile) {
            return $uploadedFiles->isValid() ? [$uploadedFiles] : [];
        }

        if (! is_array($uploadedFiles)) {
            return [];
        }

        $files = [];
        array_walk_recursive($uploadedFiles, function (mixed $file) use (&$files): void {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $files[] = $file;
            }
        });

        return $files;
    }

    /**
     * @param  array<int, int>  $deleteAttachmentIndexes
     */
    private function removeAttachmentsByIndex(OneOnOneInquiry $inquiry, array $deleteAttachmentIndexes): void
    {
        $indexes = array_values(array_unique(array_filter(
            array_map('intval', $deleteAttachmentIndexes),
            fn (int $index): bool => $index >= 0,
        )));

        if ($indexes === []) {
            return;
        }

        $existing = is_array($inquiry->attachments) ? array_values($inquiry->attachments) : [];
        if ($existing === []) {
            return;
        }

        foreach ($indexes as $index) {
            if (! array_key_exists($index, $existing)) {
                continue;
            }

            $attachment = $existing[$index];
            $path = is_array($attachment) ? ($attachment['path'] ?? null) : null;
            if (is_string($path) && $path !== '' && Storage::disk(self::ATTACHMENT_DISK)->exists($path)) {
                Storage::disk(self::ATTACHMENT_DISK)->delete($path);
            }

            unset($existing[$index]);
        }

        $existing = array_values($existing);
        $inquiry->attachments = $existing === [] ? null : $existing;
    }

    private function cleanupAttachments(OneOnOneInquiry $inquiry): void
    {
        foreach (['attachments', 'answer_attachments'] as $column) {
            $items = $inquiry->{$column};
            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                $path = is_array($item) ? ($item['path'] ?? null) : null;
                if (is_string($path) && $path !== '' && Storage::disk(self::ATTACHMENT_DISK)->exists($path)) {
                    Storage::disk(self::ATTACHMENT_DISK)->delete($path);
                }
            }
        }
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
            if (! is_array($attachment)) {
                continue;
            }

            $path = (string) ($attachment['path'] ?? '');
            $name = $attachment['name'] ?? $attachment['original_name'] ?? BackofficeFile::displayName($path);
            if ($name === null || $name === '') {
                continue;
            }

            $items[] = [
                'path' => $path,
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
        $row->display_attachments = $this->parseAttachments($inquiry->attachments);

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

<?php

namespace App\Services\Backoffice;

use App\Models\OneOnOneInquiry;
use App\Support\BackofficeFile;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OneOnOneInquiryService
{
    public const ATTACHMENT_DISK = 'public';

    public const ANSWER_ATTACHMENT_DIR = 'backoffice/one-on-one-inquiries/answer-attachments';

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        return OneOnOneInquiry::query()
            ->with(['user', 'answerer'])
            ->status((string) $request->get('answer_status', 'all'))
            ->dateBetween(
                $request->filled('date_from') ? (string) $request->get('date_from') : null,
                $request->filled('date_to') ? (string) $request->get('date_to') : null,
            )
            ->keyword(
                $request->filled('search_field') ? (string) $request->get('search_field') : 'all',
                $request->filled('keyword') ? (string) $request->get('keyword') : null,
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<int, UploadedFile>|null  $uploadedFiles
     * @param  array<int, int>  $deleteIndexes
     */
    public function updateAnswer(
        OneOnOneInquiry $inquiry,
        array $validated,
        ?array $uploadedFiles,
        array $deleteIndexes,
        ?int $answeredByUserId
    ): OneOnOneInquiry {
        return DB::transaction(function () use ($inquiry, $validated, $uploadedFiles, $deleteIndexes, $answeredByUserId) {
            $status = (string) $validated['answer_status'];
            $answeredAtRaw = $validated['answered_at'] ?? null;
            $answeredAt = null;

            if ($answeredAtRaw !== null && $answeredAtRaw !== '') {
                $answeredAt = Carbon::parse((string) $answeredAtRaw);
            }

            if ($status === 'DONE' && $answeredAt === null) {
                $answeredAt = Carbon::now();
            }

            if ($status === 'PENDING') {
                $answeredAt = null;
            }

            $inquiry->answer_status = $status;
            $inquiry->answered_at = $answeredAt;
            $inquiry->answer_content = isset($validated['answer_content'])
                ? (string) $validated['answer_content']
                : null;

            if ($status === 'DONE') {
                $inquiry->answered_by = $answeredByUserId;
            } elseif ($status === 'PENDING') {
                $inquiry->answered_by = null;
            }

            $this->syncAnswerAttachments($inquiry, $uploadedFiles, $deleteIndexes);

            $inquiry->save();

            return $inquiry->fresh(['user', 'answerer']);
        });
    }

    /**
     * @param  array<int, UploadedFile>|null  $uploadedFiles
     * @param  array<int, int>  $deleteIndexes
     */
    protected function syncAnswerAttachments(
        OneOnOneInquiry $inquiry,
        ?array $uploadedFiles,
        array $deleteIndexes
    ): void {
        $existing = is_array($inquiry->answer_attachments) ? $inquiry->answer_attachments : [];

        if (! empty($deleteIndexes)) {
            $deleteIndexes = array_flip(array_map('intval', $deleteIndexes));
            $remaining = [];
            foreach ($existing as $index => $attachment) {
                if (isset($deleteIndexes[$index])) {
                    $path = is_array($attachment) ? ($attachment['path'] ?? null) : null;
                    if (is_string($path) && $path !== '' && Storage::disk(self::ATTACHMENT_DISK)->exists($path)) {
                        Storage::disk(self::ATTACHMENT_DISK)->delete($path);
                    }
                    continue;
                }
                $remaining[] = $attachment;
            }
            $existing = $remaining;
        }

        if (is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }
                $storedPath = BackofficeFile::storeWithOriginalName($file, self::ANSWER_ATTACHMENT_DIR, self::ATTACHMENT_DISK);
                $existing[] = [
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        if (count($existing) > 5) {
            $existing = array_slice($existing, 0, 5);
        }

        $inquiry->answer_attachments = $existing === [] ? null : array_values($existing);
    }

    /**
     * @param  list<int>  $ids
     */
    public function deleteMany(array $ids): int
    {
        $inquiries = OneOnOneInquiry::query()->whereIn('id', $ids)->get();
        foreach ($inquiries as $inquiry) {
            $this->cleanupAttachments($inquiry);
            $inquiry->delete();
        }

        return $inquiries->count();
    }

    public function deleteOne(OneOnOneInquiry $inquiry): void
    {
        DB::transaction(function () use ($inquiry): void {
            $this->cleanupAttachments($inquiry);
            $inquiry->delete();
        });
    }

    protected function cleanupAttachments(OneOnOneInquiry $inquiry): void
    {
        foreach (['answer_attachments', 'attachments'] as $column) {
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

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'PENDING' => '답변 대기',
            'DONE' => '답변 완료',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function searchFieldLabels(): array
    {
        return [
            'all' => '전체',
            'name' => '회원명',
            'title' => '제목',
            'content' => '내용',
        ];
    }
}

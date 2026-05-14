<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

class ImportMemberSquareNoticesFromCsv extends Command
{
    /**
     * @var string
     */
    protected $signature = 'posts:import-member-square-notices
        {path : 학회공지 CSV 경로 (절대 경로 또는 프로젝트 기준 상대 경로)}
        {--encoding=CP949 : 원본 CSV 인코딩}
        {--dry-run : 실제 저장하지 않고 결과만 확인}
        {--copy-assets=1 : 본문 이미지/첨부 파일 실제 복사 여부 (1 복사, 0 경로만 치환)}
        {--legacy-asset-base=docs/data-migration/image : 레거시 이미지 루트(프로젝트 기준 상대 경로)}
        {--legacy-video-base=docs/data-migration/video : 레거시 비디오 루트(프로젝트 기준 상대 경로)}
        {--source-prefix=/img_up/shop_pds/rp415/bbs : 본문에서 매칭할 경로 prefix}
        {--target-prefix=/storage/legacy/bbs : 본문에서 치환할 경로 prefix}
        {--target-storage-dir=legacy/bbs : storage/app/public 하위 복사 대상 디렉토리}';

    /**
     * @var string
     */
    protected $description = '레거시 커뮤니티 학회공지 CSV를 board_member_square_notices 테이블로 이관합니다.';

    private const TABLE = 'board_member_square_notices';

    private const ATTACHMENT_TARGET_DIR = 'legacy/attachments/member_square_notices';

    /** @var array<string, list<string>> */
    private array $assetIndex = [];

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        if (! is_file($path)) {
            $absolutePath = base_path($path);
            if (! is_file($absolutePath)) {
                $this->error('CSV 파일을 찾을 수 없습니다: '.$path);

                return self::FAILURE;
            }
            $path = $absolutePath;
        }

        $encoding = strtoupper((string) $this->option('encoding'));
        $dryRun = (bool) $this->option('dry-run');
        $copyAssets = ((string) $this->option('copy-assets')) === '1';

        $legacyImageBase = base_path((string) $this->option('legacy-asset-base'));
        $legacyVideoBase = base_path((string) $this->option('legacy-video-base'));
        $sourcePrefix = rtrim((string) $this->option('source-prefix'), '/');
        $targetPrefix = rtrim((string) $this->option('target-prefix'), '/');
        $targetStorageDir = trim((string) $this->option('target-storage-dir'), '/');

        $publicStorageBase = storage_path('app/public');

        $handle = $this->openCsvHandle($path, $encoding);
        if (! $handle) {
            $this->error('CSV 파일을 열 수 없습니다: '.$path);

            return self::FAILURE;
        }

        $rows = [];
        $headerMap = null;
        try {
            $line = 0;
            while (($cols = fgetcsv($handle)) !== false) {
                $line++;
                if ($line === 1) {
                    $headerMap = $this->buildHeaderMap($cols);

                    continue;
                }
                if ($line === 2) {
                    continue;
                }
                if ($cols === [null] || count(array_filter($cols, fn ($v) => $v !== null && $v !== '')) === 0) {
                    continue;
                }
                $row = $this->normalizeRow($cols, $headerMap ?? []);
                if (! $row) {
                    continue;
                }
                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        if ($headerMap === null || $headerMap === []) {
            $this->error('CSV 헤더(1행)를 찾지 못했습니다.');

            return self::FAILURE;
        }

        $this->info('이관 대상 게시글 수: '.count($rows));

        if ($copyAssets) {
            $this->buildAssetIndex([$legacyImageBase, $legacyVideoBase]);
            $this->info('레거시 자산 인덱스 파일 수: '.array_sum(array_map('count', $this->assetIndex)));
        }

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'image_copied' => 0,
            'image_missing' => 0,
            'attachment_copied' => 0,
            'attachment_missing' => 0,
        ];

        $progressBar = $this->output->createProgressBar(count($rows));
        $progressBar->start();

        foreach ($rows as $row) {
            try {
                $this->upsertPost(
                    row: $row,
                    dryRun: $dryRun,
                    copyAssets: $copyAssets,
                    legacyImageBase: $legacyImageBase,
                    sourcePrefix: $sourcePrefix,
                    targetPrefix: $targetPrefix,
                    targetStorageDir: $targetStorageDir,
                    publicStorageBase: $publicStorageBase,
                    stats: $stats,
                );
            } catch (Throwable $e) {
                $this->newLine();
                $this->error(sprintf(
                    '레거시 게시글 ID %s 처리 실패: %s',
                    $row['legacy_post_id'] ?? '?',
                    $e->getMessage()
                ));
                $stats['skipped']++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info(sprintf(
            '완료. 생성=%d / 갱신=%d / 스킵=%d / 본문이미지 복사=%d, 누락=%d / 첨부 복사=%d, 누락=%d%s',
            $stats['created'],
            $stats['updated'],
            $stats['skipped'],
            $stats['image_copied'],
            $stats['image_missing'],
            $stats['attachment_copied'],
            $stats['attachment_missing'],
            $dryRun ? ' (dry-run, 실제 저장/복사 없음)' : ''
        ));

        return self::SUCCESS;
    }

    /**
     * @return resource|false
     */
    protected function openCsvHandle(string $path, string $encoding)
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return false;
        }
        if ($encoding === '' || strtoupper($encoding) === 'UTF-8') {
            return $handle;
        }

        $filter = stream_filter_append($handle, 'convert.iconv.'.$encoding.'/UTF-8//TRANSLIT//IGNORE', STREAM_FILTER_READ);
        if ($filter === false) {
            fclose($handle);

            return false;
        }

        return $handle;
    }

    /**
     * @param  list<string>  $cols
     * @return array<string, int>
     */
    protected function buildHeaderMap(array $cols): array
    {
        $map = [];
        foreach ($cols as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $map[$name] = $index;
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $cols
     * @param  array<string, int>  $headerMap
     * @return array<string, mixed>|null
     */
    protected function normalizeRow(array $cols, array $headerMap): ?array
    {
        $get = function (string $name) use ($cols, $headerMap): ?string {
            if (! isset($headerMap[$name])) {
                return null;
            }
            $value = $cols[$headerMap[$name]] ?? null;
            if ($value === null) {
                return null;
            }
            $value = trim((string) $value);

            return $value === '' ? null : $value;
        };

        $legacyPostId = $get('게시글고유번호');
        if ($legacyPostId === null) {
            return null;
        }

        $attachmentNames = [];
        for ($i = 1; $i <= 5; $i++) {
            $name = $get('첨부파일명'.$i);
            if ($name !== null) {
                $attachmentNames[] = $name;
            }
        }

        return [
            'legacy_post_id' => (int) $legacyPostId,
            'author_name' => $get('이름') ?? '관리자',
            'view_count' => (int) ($get('조회 수') ?? 0),
            'legacy_like_count' => $get('추천 수') !== null ? (int) $get('추천 수') : null,
            'created_at_raw' => $get('작성일'),
            'is_active' => ($get('잠금') ?? '0') === '0',
            'legacy_ip' => $get('IP'),
            'attachment_names' => $attachmentNames,
            'title' => $get('제목') ?? '(제목 없음)',
            'content' => $get('내용') ?? '',
            'legacy_comment_id' => $get('댓글고유번호') !== null ? (int) $get('댓글고유번호') : null,
            'legacy_thread_id' => $get('질문답글통합번호') !== null ? (int) $get('질문답글통합번호') : null,
        ];
    }

    protected function parseDatetime(?string $raw): ?Carbon
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $patterns = ['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d G:i', 'YmdHis', 'YmdHi', 'Y/m/d H:i:s', 'Y/m/d H:i'];
        foreach ($patterns as $pattern) {
            try {
                $dt = Carbon::createFromFormat($pattern, $raw);
                if ($dt) {
                    return $dt;
                }
            } catch (Throwable) {
                continue;
            }
        }
        try {
            return Carbon::parse($raw);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * 레거시 자산 디렉토리를 재귀 스캔해 basename → 파일 절대경로 인덱스를 구축한다.
     * 동일 basename이 여러 곳에 존재하면 모두 보관한다.
     *
     * @param  list<string>  $bases
     */
    protected function buildAssetIndex(array $bases): void
    {
        $this->assetIndex = [];
        foreach ($bases as $base) {
            if (! is_dir($base)) {
                continue;
            }
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            /** @var \SplFileInfo $file */
            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }
                $basename = $file->getFilename();
                $this->assetIndex[$basename][] = $file->getPathname();
            }
        }
    }

    /**
     * 본문에서 레거시 이미지 src 를 찾아 storage 로 복사하고 경로를 치환한다.
     *
     * @param  array<string, int>  $stats
     */
    protected function rewriteContentAndCopyImages(
        string $content,
        bool $dryRun,
        bool $copyAssets,
        string $legacyImageBase,
        string $sourcePrefix,
        string $targetPrefix,
        string $targetStorageDir,
        string $publicStorageBase,
        array &$stats,
    ): string {
        $pattern = '#'.preg_quote($sourcePrefix, '#').'/([^\s"\'<>()]+)#u';

        return (string) preg_replace_callback($pattern, function (array $m) use (
            $dryRun,
            $copyAssets,
            $legacyImageBase,
            $targetPrefix,
            $targetStorageDir,
            $publicStorageBase,
            &$stats,
        ): string {
            $rest = $m[1];
            $sourceFile = rtrim($legacyImageBase, '/').'/bbs/'.$rest;
            $targetRelative = trim($targetStorageDir, '/').'/'.$rest;
            $targetFile = rtrim($publicStorageBase, '/').'/'.$targetRelative;

            if ($copyAssets) {
                if (is_file($sourceFile)) {
                    if (! $dryRun && ! is_file($targetFile)) {
                        $dir = dirname($targetFile);
                        if (! is_dir($dir)) {
                            @mkdir($dir, 0775, true);
                        }
                        @copy($sourceFile, $targetFile);
                    }
                    $stats['image_copied']++;
                } else {
                    $stats['image_missing']++;
                }
            }

            return rtrim($targetPrefix, '/').'/'.$rest;
        }, $content);
    }

    /**
     * 첨부파일명 목록을 레거시 자산 인덱스에서 매칭해 복사한 뒤 attachments JSON 배열로 변환한다.
     *
     * @param  list<string>  $names
     * @param  array<string, int>  $stats
     * @return list<array<string, mixed>>
     */
    protected function buildAttachments(
        array $names,
        bool $dryRun,
        bool $copyAssets,
        string $publicStorageBase,
        array &$stats,
    ): array {
        $attachments = [];
        foreach ($names as $name) {
            $tokens = preg_split('/\s*(?:\|\||;)\s*/', (string) $name) ?: [$name];
            $tokens = array_values(array_filter(array_map(
                fn ($t) => trim((string) $t),
                $tokens
            ), fn ($t) => $t !== ''));
            if ($tokens === []) {
                continue;
            }

            $sharedRelativeTarget = null;
            $sharedSize = null;
            foreach ($tokens as $token) {
                $tokenBasename = basename($token);
                $candidates = $copyAssets ? ($this->assetIndex[$tokenBasename] ?? []) : [];
                if ($candidates === []) {
                    continue;
                }
                $matchedSource = $candidates[0];
                $sharedRelativeTarget = self::ATTACHMENT_TARGET_DIR.'/'.$tokenBasename;
                $absoluteTarget = rtrim($publicStorageBase, '/').'/'.$sharedRelativeTarget;
                if (! $dryRun && ! is_file($absoluteTarget)) {
                    $dir = dirname($absoluteTarget);
                    if (! is_dir($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    @copy($matchedSource, $absoluteTarget);
                }
                $sharedSize = is_file($matchedSource) ? (int) filesize($matchedSource) : null;
                break;
            }

            foreach ($tokens as $token) {
                $entry = ['name' => basename($token)];
                if ($sharedRelativeTarget !== null) {
                    $entry['path'] = $sharedRelativeTarget;
                    $entry['size'] = $sharedSize;
                    $entry['type'] = null;
                    $stats['attachment_copied']++;
                } elseif ($copyAssets) {
                    $stats['attachment_missing']++;
                }
                $attachments[] = $entry;
            }
        }

        return $attachments;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, int>  $stats
     */
    protected function upsertPost(
        array $row,
        bool $dryRun,
        bool $copyAssets,
        string $legacyImageBase,
        string $sourcePrefix,
        string $targetPrefix,
        string $targetStorageDir,
        string $publicStorageBase,
        array &$stats,
    ): void {
        $legacyPostId = (int) $row['legacy_post_id'];

        $content = $this->rewriteContentAndCopyImages(
            (string) $row['content'],
            $dryRun,
            $copyAssets,
            $legacyImageBase,
            $sourcePrefix,
            $targetPrefix,
            $targetStorageDir,
            $publicStorageBase,
            $stats,
        );

        $attachments = $this->buildAttachments(
            $row['attachment_names'] ?? [],
            $dryRun,
            $copyAssets,
            $publicStorageBase,
            $stats,
        );

        $customFields = array_filter([
            'legacy_like_count' => $row['legacy_like_count'] ?? null,
            'legacy_ip' => $row['legacy_ip'] ?? null,
            'legacy_comment_id' => $row['legacy_comment_id'] ?? null,
            'legacy_thread_id' => $row['legacy_thread_id'] ?? null,
        ], fn ($v) => $v !== null);

        $createdAt = $this->parseDatetime($row['created_at_raw'] ?? null) ?? Carbon::now();

        $data = [
            'user_id' => null,
            'title' => (string) $row['title'],
            'content' => $content,
            'author_name' => (string) $row['author_name'],
            'password' => null,
            'is_notice' => false,
            'is_secret' => false,
            'category' => null,
            'attachments' => json_encode($attachments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'view_count' => (int) ($row['view_count'] ?? 0),
            'sort_order' => 0,
            'custom_fields' => $customFields === [] ? null : json_encode($customFields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'thumbnail' => null,
            'is_active' => (bool) ($row['is_active'] ?? true),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        if ($dryRun) {
            $exists = DB::table(self::TABLE)->where('legacy_post_id', $legacyPostId)->exists();
            if ($exists) {
                $stats['updated']++;
            } else {
                $stats['created']++;
            }

            return;
        }

        $existing = DB::table(self::TABLE)->where('legacy_post_id', $legacyPostId)->first();
        if ($existing) {
            DB::table(self::TABLE)
                ->where('legacy_post_id', $legacyPostId)
                ->update($data);
            $stats['updated']++;

            return;
        }

        DB::table(self::TABLE)->insert(array_merge($data, [
            'legacy_post_id' => $legacyPostId,
        ]));
        $stats['created']++;
    }
}

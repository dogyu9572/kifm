<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEvent;
use Carbon\Carbon;
use RuntimeException;

/**
 * docs/data-migration/학술행사_학술행사 목록.csv (CP949) → academic_events 최소 필드 이관.
 * 1행 헤더, 2행 매핑 설명 행 스킵 후 데이터 반영.
 */
class AcademicEventCsvImporter
{
    /**
     * @return array{created:int,updated:int,skipped:int,errors:list<string>,warnings:list<string>}
     */
    public function import(
        string $absolutePath,
        bool $dryRun,
        ?int $limit,
        string $onDuplicate
    ): array {
        if (! is_readable($absolutePath)) {
            throw new RuntimeException('CSV 파일을 읽을 수 없습니다: ' . $absolutePath);
        }
        $raw = file_get_contents($absolutePath);
        if ($raw === false) {
            throw new RuntimeException('CSV 파일 읽기 실패');
        }
        $utf8 = mb_convert_encoding($raw, 'UTF-8', 'CP949');
        $handle = fopen('php://memory', 'r+b');
        if ($handle === false) {
            throw new RuntimeException('메모리 스트림 생성 실패');
        }
        fwrite($handle, $utf8);
        rewind($handle);

        $row1 = fgetcsv($handle);
        $row2 = fgetcsv($handle);
        if ($row1 === false || $row2 === false) {
            fclose($handle);
            throw new RuntimeException('CSV 헤더/매핑 행이 부족합니다.');
        }

        $idx = $this->headerIndex($row1);
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'warnings' => []];
        $processed = 0;
        $rowNum = 2;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($this->rowIsEmpty($row)) {
                continue;
            }
            if ($limit !== null && $processed >= $limit) {
                break;
            }

            try {
                $legacyId = $this->cell($row, $idx, '게시글고유번호');
                if ($legacyId === null || $legacyId === '') {
                    $stats['warnings'][] = "행 {$rowNum}: 게시글고유번호 없음 — 스킵";
                    $stats['skipped']++;

                    continue;
                }
                $legacyId = (int) $legacyId;
                $title = trim((string) $this->cell($row, $idx, '제목'));
                if ($title === '') {
                    $stats['warnings'][] = "행 {$rowNum}: 제목 없음 — 스킵";
                    $stats['skipped']++;

                    continue;
                }

                $processed++;
                $folder = 'legacy-' . $legacyId;
                $year = $this->guessYear($title);
                $season = $this->guessSeason($title);
                $lock = $this->cell($row, $idx, '잠금');
                $isPublic = ((string) $lock === '0') ? 'Y' : 'N';
                $views = (int) ($this->cell($row, $idx, '조회 수') ?? 0);
                $content = (string) $this->cell($row, $idx, '내용');
                $written = $this->cell($row, $idx, '작성일');
                $createdAt = null;
                if ($written) {
                    try {
                        $createdAt = Carbon::parse($written);
                    } catch (\Throwable) {
                        $stats['warnings'][] = "행 {$rowNum}: 작성일 파싱 실패 ({$written})";
                    }
                }

                $payload = [
                    'legacy_post_id' => $legacyId,
                    'folder_name' => $folder,
                    'title' => $title,
                    'year' => $year,
                    'season' => $season,
                    'is_public' => $isPublic,
                    'view_count' => $views,
                    'greeting_title' => $title,
                    'greeting_content' => $content,
                    'event_type' => 'offline',
                    'main_exposure' => 'N',
                ];

                $existing = AcademicEvent::query()->where('legacy_post_id', $legacyId)->first();
                if ($existing && $onDuplicate === 'skip') {
                    $stats['skipped']++;

                    continue;
                }

                if ($dryRun) {
                    $existing ? $stats['updated']++ : $stats['created']++;

                    continue;
                }

                if ($existing && $onDuplicate === 'update') {
                    $existing->fill($payload);
                    if ($createdAt) {
                        $existing->created_at = $createdAt;
                    }
                    $existing->save();
                    $stats['updated']++;

                    continue;
                }

                if ($existing) {
                    $stats['skipped']++;
                    $stats['warnings'][] = "행 {$rowNum}: legacy_post_id={$legacyId} 중복";

                    continue;
                }

                $ev = new AcademicEvent($payload);
                if ($createdAt) {
                    $ev->created_at = $createdAt;
                    $ev->updated_at = $createdAt;
                }
                $ev->save();
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "행 {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return $stats;
    }

    /** @param list<string|null> $header */
    protected function headerIndex(array $header): array
    {
        $map = [];
        foreach ($header as $i => $name) {
            $map[trim((string) $name)] = $i;
        }

        return $map;
    }

    /** @param list<string|null> $row */
    protected function cell(array $row, array $idx, string $key): ?string
    {
        if (! isset($idx[$key])) {
            return null;
        }
        $i = $idx[$key];

        return isset($row[$i]) ? trim((string) $row[$i]) : null;
    }

    /** @param list<string|null> $row */
    protected function rowIsEmpty(array $row): bool
    {
        foreach ($row as $c) {
            if (trim((string) $c) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function guessYear(string $title): ?int
    {
        if (preg_match('/(\d{4})년/', $title, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    protected function guessSeason(string $title): ?string
    {
        if (str_contains($title, '춘계')) {
            return 'spring';
        }
        if (str_contains($title, '하계')) {
            return 'summer';
        }
        if (str_contains($title, '추계')) {
            return 'fall';
        }
        if (str_contains($title, '동계')) {
            return 'winter';
        }

        return null;
    }
}

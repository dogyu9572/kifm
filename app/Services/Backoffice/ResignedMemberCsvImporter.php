<?php

namespace App\Services\Backoffice;

use App\Models\User;
use RuntimeException;

/**
 * docs/data-migration/탈퇴회원.csv (CP949) 이관.
 *
 * 아이디(login_id) 기준으로 기존 users를 탈퇴 처리한다.
 */
class ResignedMemberCsvImporter
{
    /**
     * @return array{updated: int, skipped: int, missing: int, errors: list<string>}
     */
    public function import(
        string $absolutePath,
        bool $dryRun,
        ?int $limit,
        string $onMissing,
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

        $headerRow = fgetcsv($handle);
        if ($headerRow === false) {
            fclose($handle);
            throw new RuntimeException('CSV 헤더가 없습니다.');
        }

        $headers = [];
        foreach ($headerRow as $i => $h) {
            $headers[$i] = $this->normalizeCell((string) $h);
        }

        $idIdx = array_search('아이디', $headers, true);
        $reasonIdx = array_search('사유', $headers, true);
        if ($idIdx === false) {
            fclose($handle);
            throw new RuntimeException('CSV 헤더에 아이디 컬럼이 없습니다.');
        }

        $stats = ['updated' => 0, 'skipped' => 0, 'missing' => 0, 'errors' => []];
        $rowNum = 1;
        $withdrawnAt = now();

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if ($this->rowIsEmpty($row)) {
                continue;
            }
            if ($limit !== null && ($stats['updated'] + $stats['skipped'] + $stats['missing']) >= $limit) {
                break;
            }

            $loginId = isset($row[$idIdx]) ? trim((string) $row[$idIdx]) : '';
            if ($loginId === '') {
                $stats['skipped']++;
                $stats['errors'][] = "행 {$rowNum}: 아이디 없음";
                continue;
            }

            $reason = '';
            if ($reasonIdx !== false) {
                $reason = isset($row[$reasonIdx]) ? trim((string) $row[$reasonIdx]) : '';
            }

            try {
                $user = User::query()
                    ->where('role', 'user')
                    ->where('login_id', $loginId)
                    ->first();

                if (! $user) {
                    $stats['missing']++;
                    if ($onMissing === 'error') {
                        $stats['errors'][] = "행 {$rowNum}: 대상 회원 없음(login_id={$loginId})";
                    }
                    continue;
                }

                if ($user->withdrawn_at !== null) {
                    $stats['skipped']++;
                    continue;
                }

                if ($dryRun) {
                    $stats['updated']++;
                    continue;
                }

                $legacy = $user->legacy_import_json;
                if (! is_array($legacy)) {
                    $legacy = [];
                }
                if ($reason !== '') {
                    $legacy['withdraw_reason'] = $reason;
                }

                $payload = [
                    'withdrawn_at' => $withdrawnAt,
                    'is_active' => false,
                    'legacy_import_json' => $legacy === [] ? null : $legacy,
                ];

                $user->update($payload);
                $stats['updated']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "행 {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return $stats;
    }

    private function normalizeCell(string $value): string
    {
        $v = str_replace(["\r\n", "\r", "\n"], ' ', $value);
        return trim(preg_replace('/\s+/u', ' ', $v));
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }
}


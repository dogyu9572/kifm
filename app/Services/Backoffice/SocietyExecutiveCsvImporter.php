<?php

namespace App\Services\Backoffice;

use App\Models\SocietyExecutive;
use RuntimeException;

class SocietyExecutiveCsvImporter
{
    /**
     * @return array{created: int, updated: int, skipped: int, errors: list<string>}
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

        $headers = $this->buildHeadersFromDefinitionRows($row1, $row2);
        $rowNum = 2;
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($this->rowIsEmpty($row)) {
                continue;
            }
            if ($limit !== null && ($stats['created'] + $stats['updated'] + $stats['skipped']) >= $limit) {
                break;
            }

            try {
                $assoc = $this->buildAssoc($headers, $row);
                $payload = $this->mapPayload($assoc);
                if ($payload === null) {
                    $stats['skipped']++;

                    continue;
                }

                $existing = SocietyExecutive::query()
                    ->where('name', $payload['name'])
                    ->where('position', $payload['position'])
                    ->where('organization', $payload['organization'])
                    ->first();

                if ($existing && $onDuplicate === 'skip') {
                    $stats['skipped']++;

                    continue;
                }

                if ($dryRun) {
                    $existing ? $stats['updated']++ : $stats['created']++;

                    continue;
                }

                if ($existing && $onDuplicate === 'update') {
                    $existing->update($payload);
                    $stats['updated']++;

                    continue;
                }

                SocietyExecutive::query()->create($payload);
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "행 {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return $stats;
    }

    /**
     * @param  list<string|null>  $row1
     * @param  list<string|null>  $row2
     * @return list<string>
     */
    private function buildHeadersFromDefinitionRows(array $row1, array $row2): array
    {
        $headers = [];
        $max = max(count($row1), count($row2));
        for ($i = 0; $i < $max; $i++) {
            $h1 = $this->normalizeHeader((string) ($row1[$i] ?? ''));
            $h2 = $this->normalizeHeader((string) ($row2[$i] ?? ''));
            if ($h2 === '삭제') {
                $headers[$i] = '';

                continue;
            }
            $headers[$i] = $h1;
        }

        return $headers;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, string|null>  $row
     * @return array<string, string>
     */
    private function buildAssoc(array $headers, array $row): array
    {
        $assoc = [];
        foreach ($headers as $idx => $header) {
            if ($header === '') {
                continue;
            }
            $assoc[$header] = trim((string) ($row[$idx] ?? ''));
        }

        return $assoc;
    }

    /**
     * @param  array<string, string>  $assoc
     * @return array<string, mixed>|null
     */
    private function mapPayload(array $assoc): ?array
    {
        $position = trim((string) ($assoc['직함(s_v1)'] ?? ''));
        $name = trim((string) ($assoc['성명(s_v2)'] ?? ''));
        $organization = trim((string) ($assoc['소속(s_v5)'] ?? ''));
        if ($position === '' || $name === '' || $organization === '') {
            return null;
        }

        $groupNo = $this->parseGroupNo((string) ($assoc['카테고리'] ?? ''));
        $sortOrder = $this->parseInt((string) ($assoc['출력순서 (높은숫자일수록 위쪽으로 출력)'] ?? '1'));
        $status = trim((string) ($assoc['상태 (대기중,확인,임시보류) 중 입력'] ?? ''));
        $isActive = ! in_array($status, ['임시보류', '삭제'], true);
        $email = trim((string) ($assoc['이메일(s_v4)'] ?? ''));
        if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = null;
        }

        $legacyNo = trim((string) ($assoc['게시글 고유번호'] ?? ''));
        $legacyIp = trim((string) ($assoc['IP'] ?? ''));
        $legacyDate = trim((string) ($assoc['등록일'] ?? ''));
        $note = 'legacy_post_id=' . ($legacyNo === '' ? '-' : $legacyNo)
            . ', legacy_ip=' . ($legacyIp === '' ? '-' : $legacyIp)
            . ', legacy_created_at=' . ($legacyDate === '' ? '-' : $legacyDate);

        return [
            'group_no' => $groupNo,
            'position' => $position,
            'name' => $name,
            'organization' => $organization,
            'email' => $email,
            'sort_order' => max(1, $sortOrder),
            'is_active' => $isActive,
            'note' => $note,
        ];
    }

    private function parseGroupNo(string $category): int
    {
        if (preg_match('/(\d+)$/', trim($category), $matches) === 1) {
            $num = (int) $matches[1];

            return min(3, max(1, $num));
        }

        return 1;
    }

    private function parseInt(string $value): int
    {
        $digits = preg_replace('/[^\d-]/', '', $value) ?? '';
        if ($digits === '' || $digits === '-') {
            return 1;
        }

        return (int) $digits;
    }

    private function normalizeHeader(string $s): string
    {
        $v = str_replace(["\r\n", "\r", "\n"], ' ', $s);

        return trim((string) preg_replace('/\s+/u', ' ', $v));
    }

    /**
     * @param  array<int, string|null>  $row
     */
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


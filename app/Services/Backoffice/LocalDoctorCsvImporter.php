<?php

namespace App\Services\Backoffice;

use App\Models\LocalDoctor;
use Carbon\Carbon;
use RuntimeException;

/**
 * docs/data-migration/우리동네주치의_주치의 목록.csv (CP949) → local_doctors 이관.
 * 1행 참고, 2행이 삭제인 열은 제외. 행 단위 누락 없이 반영(지역 매핑 실패 시 원문+경고).
 */
class LocalDoctorCsvImporter
{
    /**
     * @return array{
     *   created: int,
     *   updated: int,
     *   skipped: int,
     *   errors: list<string>,
     *   warnings: list<string>
     * }
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

        $columnKeys = $this->buildColumnKeys($row1, $row2);
        $rowNum = 2;
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'warnings' => []];
        $processed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($this->rowIsEmpty($row)) {
                continue;
            }
            if ($limit !== null && $processed >= $limit) {
                break;
            }

            try {
                $assoc = $this->buildAssoc($columnKeys, $row);
                $payload = $this->mapPayload($assoc, $row, $row1, $row2, $stats['warnings']);
                if ($payload === null) {
                    $stats['skipped']++;

                    continue;
                }

                $processed++;
                $legacyId = $payload['legacy_post_id'] ?? null;
                $existing = $legacyId !== null
                    ? LocalDoctor::query()->where('legacy_post_id', $legacyId)->first()
                    : null;

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

                if ($existing) {
                    $stats['skipped']++;
                    $stats['warnings'][] = "행 {$rowNum}: legacy_post_id={$legacyId} 중복(on-duplicate=skip 처리와 유사)";

                    continue;
                }

                LocalDoctor::query()->create($payload);
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
     * @return list<string> 인덱스별 assoc 키(빈 문자열이면 해당 열 스킵)
     */
    private function buildColumnKeys(array $row1, array $row2): array
    {
        $keys = [];
        $max = max(count($row1), count($row2));
        for ($i = 0; $i < $max; $i++) {
            $h1 = $this->normalizeCell((string) ($row1[$i] ?? ''));
            $h2 = $this->normalizeCell((string) ($row2[$i] ?? ''));
            if ($h2 === '삭제') {
                $keys[$i] = '';

                continue;
            }
            if ($h2 === 'DB' || str_contains($h2, 'db 에 이관')) {
                $keys[$i] = $h1 !== '' ? $h1 : $h2;

                continue;
            }
            if (str_contains($h2, '이 뒤에 항목들은')) {
                $keys[$i] = $h1;

                continue;
            }
            if ($h2 !== '') {
                $keys[$i] = $h2;

                continue;
            }
            $keys[$i] = $h1;
        }

        return $keys;
    }

    /**
     * @param  array<int, string>  $columnKeys
     * @param  array<int, string|null>  $row
     * @return array<string, string>
     */
    private function buildAssoc(array $columnKeys, array $row): array
    {
        $assoc = [];
        foreach ($columnKeys as $idx => $key) {
            if ($key === '') {
                continue;
            }
            $assoc[$key] = trim((string) ($row[$idx] ?? ''));
        }

        return $assoc;
    }

    /**
     * @param  array<string, string>  $assoc
     * @param  array<int, string|null>  $row
     * @param  list<string|null>  $row1
     * @param  list<string|null>  $row2
     * @param  list<string>  $warnings
     * @return array<string, mixed>|null
     */
    private function mapPayload(array $assoc, array $row, array $row1, array $row2, array &$warnings): ?array
    {
        $hospital = trim((string) ($this->firstAssocValue($assoc, ['병원명', '병원명(s_v2)']) ?? ''));
        $doctor = trim((string) ($this->firstAssocValue($assoc, ['선생님 성함', '성함(s_v3)']) ?? ''));
        $legacyPostIdRaw = trim((string) ($this->firstAssocValue($assoc, ['게시글 고유번호']) ?? ''));

        if ($hospital === '' && $doctor === '' && $legacyPostIdRaw === '') {
            return null;
        }

        $legacyPostId = $legacyPostIdRaw !== '' && ctype_digit($legacyPostIdRaw)
            ? (int) $legacyPostIdRaw
            : null;

        $sidoRaw = trim((string) ($this->firstAssocValue($assoc, ['시/도', '지역(s_v1)']) ?? ''));
        $norm = LocalDoctorRegionNormalizer::normalizeSido($sidoRaw);
        if ($norm['warning'] !== null) {
            $warnings[] = $norm['warning'];
        }
        $sido = $norm['sido'] !== '' ? $norm['sido'] : $sidoRaw;

        $address = trim((string) ($this->firstAssocValue($assoc, ['주소', '주소(s_v6)']) ?? ''));
        $sigungu = LocalDoctorRegionNormalizer::guessSigunguFromAddress($address);

        $statusRaw = trim((string) ($this->firstAssocValue($assoc, [
            '상태 등록 -> 운영 중 그 외 -> 미운영',
            '상태 (대기중,확인,임시보류) 중 입력',
        ]) ?? ''));
        $status = str_contains($statusRaw, '등록') ? 'active' : 'inactive';

        $viewCount = $this->readIndexedNumericField($row, $row1, 5, '조회수', $assoc, '조회수');
        $sortOrder = $this->readIndexedNumericField($row, $row1, 6, '출력순서', $assoc, '출력순서 (높은숫자일수록 위쪽으로 출력)');

        $registered = $this->readIndexedStringField($row, $row1, 7, '등록일', $assoc, '등록일');
        $createdAt = null;
        if ($registered !== '') {
            try {
                $createdAt = Carbon::parse($registered)->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                $warnings[] = '등록일 파싱 실패: ' . $registered;
            }
        }

        $license = trim((string) ($this->firstAssocValue($assoc, ['면헙너호', '면허번호(s_v7)', '면허번호']) ?? ''));
        $homepage = trim((string) ($this->firstAssocValue($assoc, ['홈페이지 URL', '홈페이지(s_v8)']) ?? ''));
        $photo = trim((string) ($this->firstAssocValue($assoc, ['사진', '사진(s_v9)']) ?? ''));
        $intro = trim((string) ($this->firstAssocValue($assoc, ['병원 소개', '병원소개(s_t1)']) ?? ''));

        $extras = [];
        foreach ($assoc as $key => $value) {
            if ($value === '') {
                continue;
            }
            if ($this->isCoreAssocKey($key)) {
                continue;
            }
            $extras[$key] = $value;
        }
        $this->mergeDeletedColumnExtras($row1, $row2, $row, $extras);

        $payload = [
            'member_id' => null,
            'allow_member_edit' => true,
            'photo_path' => $photo !== '' ? $photo : null,
            'doctor_name' => $doctor !== '' ? $doctor : '이름 미입력',
            'license_no' => $license !== '' ? $license : null,
            'introduction' => $intro !== '' ? $intro : null,
            'hospital_name' => $hospital !== '' ? $hospital : '병원명 미입력',
            'sido' => $sido !== '' ? $sido : null,
            'sigungu' => $sigungu !== '' ? $sigungu : null,
            'address' => $address !== '' ? $address : null,
            'address_detail' => null,
            'homepage' => $homepage !== '' ? $homepage : null,
            'phone' => trim((string) ($this->firstAssocValue($assoc, ['전화번호', '전화번호(s_v4)']) ?? '')) ?: null,
            'status' => $status,
            'view_count' => $viewCount,
            'sort_order' => $sortOrder,
            'legacy_post_id' => $legacyPostId,
            'legacy_csv_extras' => $extras !== [] ? $extras : null,
            'functional_tests_selected' => null,
            'treatment_areas_selected' => null,
            'other_symptoms' => null,
            'diseases_text' => null,
        ];

        if ($createdAt !== null) {
            $payload['created_at'] = $createdAt;
            $payload['updated_at'] = $createdAt;
        }

        return $payload;
    }

    /**
     * 2행이 삭제인 열은 assoc에서 제외되나, 값은 legacy_csv_extras에 보존한다.
     *
     * @param  list<string|null>  $row1
     * @param  list<string|null>  $row2
     * @param  array<int, string|null>  $row
     * @param  array<string, string>  $extras
     */
    private function mergeDeletedColumnExtras(array $row1, array $row2, array $row, array &$extras): void
    {
        $max = max(count($row1), count($row2), count($row));
        for ($i = 0; $i < $max; $i++) {
            $h2 = $this->normalizeCell((string) ($row2[$i] ?? ''));
            if ($h2 !== '삭제') {
                continue;
            }
            $h1 = $this->normalizeCell((string) ($row1[$i] ?? ''));
            $val = trim((string) ($row[$i] ?? ''));
            if ($h1 === '' || $val === '') {
                continue;
            }
            if (str_contains($h1, '조회수') || str_contains($h1, '출력순서') || str_contains($h1, '등록일')) {
                continue;
            }
            if (! isset($extras[$h1])) {
                $extras[$h1] = $val;
            }
        }
    }

    private function isCoreAssocKey(string $key): bool
    {
        $cores = [
            '시/도', '병원명', '선생님 성함', '전화번호', '주소', '면헙너호', '홈페이지 URL', '사진', '병원 소개',
            '게시글 고유번호', '상태 등록 -> 운영 중 그 외 -> 미운영',
        ];

        return in_array($key, $cores, true);
    }

    /**
     * @param  list<string|null>  $row1
     * @param  array<string, string>  $assoc
     */
    private function readIndexedNumericField(
        array $row,
        array $row1,
        int $index,
        string $row1Contains,
        array $assoc,
        string $assocKeyFallback
    ): int {
        $label = isset($row1[$index]) ? (string) $row1[$index] : '';
        if (str_contains($label, $row1Contains)) {
            return $this->parsePositiveInt((string) ($row[$index] ?? '0'), 0);
        }
        $v = $assoc[$assocKeyFallback] ?? '0';

        return $this->parsePositiveInt((string) $v, 0);
    }

    /**
     * @param  list<string|null>  $row1
     * @param  array<string, string>  $assoc
     */
    private function readIndexedStringField(
        array $row,
        array $row1,
        int $index,
        string $row1Contains,
        array $assoc,
        string $assocKeyFallback
    ): string {
        $label = isset($row1[$index]) ? (string) $row1[$index] : '';
        if (str_contains($label, $row1Contains)) {
            return trim((string) ($row[$index] ?? ''));
        }

        return trim((string) ($assoc[$assocKeyFallback] ?? ''));
    }

    /**
     * @param  array<string, string>  $assoc
     * @param  list<string>  $candidates
     */
    private function firstAssocValue(array $assoc, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (array_key_exists($c, $assoc)) {
                return $assoc[$c];
            }
        }

        return null;
    }

    private function parsePositiveInt(string $value, int $default): int
    {
        $digits = preg_replace('/[^\d]/', '', $value) ?? '';

        return $digits !== '' ? max(0, (int) $digits) : $default;
    }

    private function normalizeCell(string $s): string
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

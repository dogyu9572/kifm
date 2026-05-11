<?php

namespace App\Services\Backoffice;

use App\Models\MembershipPayment;
use App\Models\PaymentPlan;
use App\Models\User;
use Carbon\Carbon;
use RuntimeException;

class MembershipPaymentCsvImporter
{
    /**
     * @return array{created: int, updated: int, skipped: int, missing: int, errors: list<string>}
     */
    public function import(
        string $absolutePath,
        bool $dryRun,
        ?int $limit,
        string $onDuplicate,
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

        // 1행: 안내문(버림)
        $firstRow = fgetcsv($handle);
        if ($firstRow === false) {
            fclose($handle);
            throw new RuntimeException('CSV 헤더가 없습니다.');
        }

        // 2행: 원본 헤더, 3행: 매핑/설명 행
        $row2 = fgetcsv($handle);
        $row3 = fgetcsv($handle);
        if ($row2 === false || $row3 === false) {
            fclose($handle);
            throw new RuntimeException('CSV 2/3행 헤더·매핑 행이 부족합니다.');
        }

        $headers = $this->buildHeadersFromDefinitionRows($row2, $row3);

        $rowNum = 3;

        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'missing' => 0, 'errors' => []];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($this->rowIsEmpty($row)) {
                continue;
            }
            if ($limit !== null && ($stats['created'] + $stats['updated'] + $stats['skipped'] + $stats['missing']) >= $limit) {
                break;
            }

            try {
                $assoc = $this->buildAssoc($headers, $row);
                $loginId = trim((string) $this->assocFirstNonEmpty($assoc, ['아이디', '회원ID']));
                if ($loginId === '') {
                    $stats['skipped']++;
                    $stats['errors'][] = "행 {$rowNum}: 회원ID 없음";
                    continue;
                }

                $member = User::query()->where('role', 'user')->where('login_id', $loginId)->first();
                if (! $member) {
                    $stats['missing']++;
                    if ($onMissing === 'error') {
                        $stats['errors'][] = "행 {$rowNum}: 회원 미존재(login_id={$loginId})";
                    }
                    continue;
                }

                $paymentNo = $this->makePaymentNo($assoc);
                $existing = MembershipPayment::query()->where('payment_no', $paymentNo)->first();
                if ($existing && $onDuplicate === 'skip') {
                    $stats['skipped']++;
                    continue;
                }

                $payload = $this->mapPayload($assoc, $member->id, $member->member_level);

                if ($dryRun) {
                    $existing ? $stats['updated']++ : $stats['created']++;
                    continue;
                }

                if ($existing && $onDuplicate === 'update') {
                    $existing->update($payload);
                    $stats['updated']++;
                    continue;
                }

                MembershipPayment::query()->create($payload);
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "행 {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return $stats;
    }

    /**
     * 2행(원본 헤더) + 3행(매핑/설명)으로 정규화된 헤더 생성.
     * - 3행 값이 '삭제'면 해당 열은 이관 대상에서 제외
     * - 3행이 비어 있거나 설명 행(= 포함 또는 괄호 포함)이면 2행 헤더를 사용
     *
     * @param  list<string|null>  $row2
     * @param  list<string|null>  $row3
     * @return list<string>
     */
    private function buildHeadersFromDefinitionRows(array $row2, array $row3): array
    {
        $headers = [];
        $max = max(count($row2), count($row3));

        for ($i = 0; $i < $max; $i++) {
            $h2 = $this->normalizeHeader((string) ($row2[$i] ?? ''));
            $h3 = $this->normalizeHeader((string) ($row3[$i] ?? ''));

            if ($h3 === '삭제') {
                $headers[$i] = '';
                continue;
            }

            $isExplanation = str_contains($h3, '=')
                || str_starts_with($h3, 'ID 로 ')
                || str_contains($h3, '연동을 위한')
                || in_array($h3, ['미사용', 'DB'], true);
            $key = $h3 !== '' && ! $isExplanation ? $h3 : $h2;
            $headers[$i] = $key;
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
            $val = isset($row[$idx]) ? trim((string) $row[$idx]) : '';
            $assoc[$header] = $val;
        }

        return $assoc;
    }

    private function mapPayload(array $assoc, int $memberId, ?string $memberLevel): array
    {
        $product = trim((string) ($assoc['상품1'] ?? ''));
        $planId = $this->resolveMembershipPlanId($product);
        $amount = $this->parseInt((string) ($assoc['결제 금액'] ?? '0'));
        $status = $this->mapPaymentStatus((string) ($assoc['결제 여부'] ?? ''), (string) ($assoc['상태'] ?? ''));
        $requestedAt = $this->parseDateTime((string) $this->assocFirstNonEmpty($assoc, ['결제신청일', '등록일']));
        $paidAt = $this->parseDateTime((string) $this->assocFirstNonEmpty($assoc, ['결제 완료일(입금일)', '입금일']));

        return [
            'payment_no' => $this->makePaymentNo($assoc),
            'member_id' => $memberId,
            'membership_plan_id' => $planId,
            'amount' => max(0, $amount),
            'member_grade' => $this->normalizeMemberGradeByProduct($product, $memberLevel),
            'payment_method' => 'bank_transfer',
            'payment_status' => $status,
            'requested_at' => $requestedAt,
            'paid_at' => $paidAt,
            'depositor_name' => $assoc['입금자명'] ?? null,
            'receipt_issue' => 'NO',
            'legacy_import_json' => $assoc,
        ];
    }

    private function makePaymentNo(array $assoc): string
    {
        $legacyId = trim((string) ($assoc['고유번호'] ?? ''));
        if ($legacyId !== '') {
            return 'PAY-LEGACY-' . $legacyId;
        }

        $fallback = trim((string) ($assoc['No.'] ?? ''));
        if ($fallback !== '') {
            return 'PAY-LEGACY-NO-' . $fallback;
        }

        return 'PAY-LEGACY-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
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

    private function parseInt(string $value): int
    {
        $digits = preg_replace('/[^\d]/', '', $value) ?? '';
        if ($digits === '') {
            return 0;
        }

        return (int) $digits;
    }

    private function parseDateTime(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '' || $value === '-' || $value === '0') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function mapPaymentStatus(string $paymentYn, string $statusRaw): string
    {
        $value = trim($paymentYn . ' ' . $statusRaw);
        if ($value === '') {
            return 'pending';
        }
        if (str_contains($value, '취소') || str_contains($value, '환불')) {
            return 'cancelled';
        }
        if (str_contains($value, '완료') || str_contains($value, '입금')) {
            return 'completed';
        }

        return 'pending';
    }

    private function normalizeMemberGradeByProduct(string $product, ?string $defaultGrade): ?string
    {
        if (str_contains($product, '준회원')) {
            return 'associate';
        }
        if (str_contains($product, '정회원')) {
            return 'regular';
        }
        if (str_contains($product, '평생')) {
            return 'lifetime';
        }
        if (str_contains($product, '시니어')) {
            return 'senior';
        }

        return $defaultGrade;
    }

    private function resolveMembershipPlanId(string $product): ?int
    {
        if ($product === '') {
            return null;
        }

        return PaymentPlan::query()
            ->where('category', 'membership')
            ->where('use_status', 'active')
            ->where('plan_name', 'like', '%' . $product . '%')
            ->value('id');
    }

    /**
     * @param  array<string, string>  $assoc
     * @param  list<string>  $keys
     */
    private function assocFirstNonEmpty(array $assoc, array $keys): string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $assoc)) {
                continue;
            }
            $value = trim((string) $assoc[$key]);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}


<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEvent;
use App\Models\AcademicEventRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * docs/data-migration/학술행사_학술행사_참가 및 결제 관리.xls.csv 이관.
 * UTF-8(BOM) 또는 CP949 원본을 UTF-8로 정규화한 뒤 fgetcsv로 파싱한다.
 * 데이터 행: 고유번호 열이 숫자만인 행(헤더·설명 행은 자동 제외).
 * 각 데이터 행의 헤더→값 전체를 source_row_json에 보존한다.
 */
class AcademicEventRegistrationCsvImporter
{
    private const HEADER_LEGACY_KEY = '고유번호';

    /**
     * @return array{
     *     created:int,
     *     updated:int,
     *     skipped:int,
     *     data_rows:int,
     *     errors:list<string>,
     *     warnings:list<string>
     * }
     */
    public function import(
        string $absolutePath,
        ?int $academicEventId,
        bool $dryRun,
        ?int $limit,
        string $onDuplicate
    ): array {
        if (! is_readable($absolutePath)) {
            throw new RuntimeException('CSV 파일을 읽을 수 없습니다: ' . $absolutePath);
        }

        // 원본 CSV에 학술행사 식별자가 없는 경우(--academic-event-id 미지정)에는
        // academic_event_id 와 registration_no 를 NULL 로 이관한다.
        if ($academicEventId !== null) {
            $event = AcademicEvent::query()->find($academicEventId);
            if ($event === null) {
                throw new RuntimeException("academic_events.id={$academicEventId} 행사를 찾을 수 없습니다.");
            }
        }

        $raw = file_get_contents($absolutePath);
        if ($raw === false) {
            throw new RuntimeException('CSV 파일 읽기 실패');
        }

        $utf8 = $this->normalizeToUtf8($raw);
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

        $idx = $this->headerIndex($headerRow);
        if (! isset($idx[self::HEADER_LEGACY_KEY])) {
            fclose($handle);
            throw new RuntimeException('CSV에「고유번호」열이 없습니다.');
        }

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'data_rows' => 0,
            'errors' => [],
            'warnings' => [],
        ];

        $rowNum = 1;
        $processedForLimit = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($this->rowIsEmpty($row)) {
                continue;
            }

            if (! $this->isDataRow($row, $idx)) {
                continue;
            }

            if ($limit !== null && $processedForLimit >= $limit) {
                break;
            }

            $stats['data_rows']++;
            $processedForLimit++;

            try {
                $legacyRaw = $this->cell($row, $idx, self::HEADER_LEGACY_KEY);
                if ($legacyRaw === null || $legacyRaw === '' || ! preg_match('/^\d+$/', $legacyRaw)) {
                    $stats['warnings'][] = "행 {$rowNum}: 고유번호 비어 있음 또는 형식 오류 — 스킵";
                    $stats['skipped']++;

                    continue;
                }

                $legacyUniqueNo = $legacyRaw;
                if (strlen($legacyUniqueNo) > 40) {
                    $stats['warnings'][] = "행 {$rowNum}: 고유번호 길이 초과 — 스킵";
                    $stats['skipped']++;

                    continue;
                }

                $sourceRowJson = $this->buildSourceRowJson($headerRow, $row);

                $paymentStatus = $this->mapPaymentStatus($this->cell($row, $idx, '상태'));
                if ($paymentStatus === null) {
                    $rawStatus = (string) ($this->cell($row, $idx, '상태') ?? '');
                    $stats['warnings'][] = "행 {$rowNum}: 미매핑 상태「{$rawStatus}」— pending_payment 로 처리";
                    $paymentStatus = 'pending_payment';
                }

                $regType = $this->mapRegType((string) ($this->cell($row, $idx, '등록구분(s_v9)') ?? ''));
                $paymentMethod = $this->mapPaymentMethod((string) ($this->cell($row, $idx, '공급번호') ?? ''));

                $itemLine = (string) ($this->cell($row, $idx, '등록구분(s_v9)') ?? '');
                $itemPrice = $this->parseAmountWon($itemLine);

                $name = trim((string) ($this->cell($row, $idx, '성명 (한글)(s_v1)') ?? ''));
                if ($name === '') {
                    $name = trim((string) ($this->cell($row, $idx, '회원명') ?? ''));
                }
                if ($name === '') {
                    $name = '미상';
                }

                $memberId = $this->resolveMemberId($this->cell($row, $idx, '회원ID'));

                $registeredAt = $this->parseFirstCarbon([
                    $this->cell($row, $idx, '등록일'),
                    $this->cell($row, $idx, '작성일'),
                ]) ?? Carbon::now();

                $paidAt = null;
                if (in_array($paymentStatus, ['completed'], true)) {
                    $paidAt = $this->parseFirstCarbon([
                        $this->cell($row, $idx, '등록일'),
                        $this->cell($row, $idx, '작성일'),
                    ]) ?? $registeredAt;
                }

                $cancelledAt = $paymentStatus === 'cancelled' ? ($paidAt ?? $registeredAt) : null;

                $adminMemo = $this->joinMemoParts([
                    $this->cell($row, $idx, '메모'),
                    $this->cell($row, $idx, '내용'),
                ]);

                $payload = [
                    'academic_event_id' => $academicEventId,
                    'member_id' => $memberId,
                    'legacy_unique_no' => $legacyUniqueNo,
                    'source_row_json' => $sourceRowJson,
                    'reg_type' => $regType,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'total_amount' => $itemPrice,
                    'name' => $name,
                    'license_no' => $this->cell($row, $idx, '면허번호(s_v2)'),
                    'phone' => $this->firstNonEmpty([
                        $this->cell($row, $idx, '휴대번호(s_v7)'),
                        $this->cell($row, $idx, '전화번호(s_v6)'),
                    ]),
                    'email' => $this->cell($row, $idx, '이메일(s_v8)'),
                    'registered_at' => $registeredAt,
                    'applied_at' => $registeredAt,
                    'paid_at' => $paidAt,
                    'bank_depositor' => $this->cell($row, $idx, '입금자명(s_v11)'),
                    'bank_deposit_date' => $this->parseDateOnly($this->cell($row, $idx, '입금예정일(s_v12)')),
                    'bank_account_text' => $this->cell($row, $idx, '결제은행(s_v10)'),
                    'admin_memo' => $adminMemo,
                    'receipt_issue' => 'NO',
                    'receipt_type' => null,
                    'receipt_number' => null,
                    'refund_bank' => null,
                    'refund_account' => null,
                    'refund_holder' => null,
                    'cancelled_at' => $cancelledAt,
                ];

                $existingQuery = AcademicEventRegistration::query()
                    ->where('legacy_unique_no', $legacyUniqueNo);
                if ($academicEventId === null) {
                    $existingQuery->whereNull('academic_event_id');
                } else {
                    $existingQuery->where('academic_event_id', $academicEventId);
                }
                $existing = $existingQuery->first();

                if ($existing && $onDuplicate === 'skip') {
                    $stats['skipped']++;
                    $stats['warnings'][] = "행 {$rowNum}: 고유번호 {$legacyUniqueNo} — on-duplicate=skip";

                    continue;
                }

                if ($dryRun) {
                    if ($existing && $onDuplicate === 'skip') {
                        $stats['skipped']++;
                    } elseif ($existing && $onDuplicate === 'update') {
                        $stats['updated']++;
                    } elseif (! $existing) {
                        $stats['created']++;
                    } else {
                        $stats['skipped']++;
                    }

                    continue;
                }

                $itemRow = [
                    'payment_plan_id' => null,
                    'item_name' => $itemLine !== '' ? $itemLine : '등록 항목',
                    'category' => null,
                    'member_scope' => null,
                    'price' => $itemPrice,
                ];

                if ($existing && $onDuplicate === 'update') {
                    DB::transaction(function () use ($existing, $payload, $itemRow): void {
                        $existing->update($payload);
                        $existing->items()->delete();
                        $existing->items()->create($itemRow);
                    });
                    $stats['updated']++;

                    continue;
                }

                if ($existing) {
                    $stats['skipped']++;
                    $stats['warnings'][] = "행 {$rowNum}: 고유번호 {$legacyUniqueNo} 중복(정책 불일치)";

                    continue;
                }

                // academic_event_id 가 지정된 경우에만 IMP-{eventId}-{legacy} 접두어로 합성한다.
                // 미지정(NULL) 이관의 경우에는 registration_no 도 NULL 로 둔다 (원본 CSV에 행사 정보가 없음).
                $registrationNo = $academicEventId !== null
                    ? 'IMP-' . $academicEventId . '-' . $legacyUniqueNo
                    : null;

                DB::transaction(function () use ($payload, $registrationNo, $itemRow): void {
                    $registration = AcademicEventRegistration::query()->create(array_merge($payload, [
                        'registration_no' => $registrationNo,
                    ]));
                    $registration->items()->create($itemRow);
                });
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "행 {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $sum = $stats['created'] + $stats['updated'] + $stats['skipped'];
        if ($sum !== $stats['data_rows']) {
            $stats['warnings'][] = "건수 대사: data_rows={$stats['data_rows']}, created+updated+skipped={$sum} (오류/일부 처리로 불일치 가능)";
        }

        return $stats;
    }

    protected function normalizeToUtf8(string $raw): string
    {
        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        }
        if (mb_check_encoding($raw, 'UTF-8')) {
            return $raw;
        }

        return mb_convert_encoding($raw, 'UTF-8', 'CP949');
    }

    /** @param list<string|null> $header */
    protected function buildSourceRowJson(array $header, array $row): array
    {
        $out = [];
        foreach ($header as $i => $name) {
            $key = trim((string) $name);
            if ($key === '') {
                continue;
            }
            $out[$key] = isset($row[$i]) ? (string) $row[$i] : '';
        }

        return $out;
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

    /**
     * @param list<string|null> $row
     */
    protected function isDataRow(array $row, array $idx): bool
    {
        $legacy = $this->cell($row, $idx, self::HEADER_LEGACY_KEY);
        if ($legacy === null || $legacy === '') {
            return false;
        }

        return (bool) preg_match('/^\d+$/', $legacy);
    }

    protected function mapPaymentMethod(string $supplyNo): string
    {
        if ($supplyNo === '' || $supplyNo === '0') {
            return 'bank_transfer';
        }
        if (ctype_digit($supplyNo) && (int) $supplyNo > 0) {
            return 'card';
        }

        return 'bank_transfer';
    }

    protected function mapRegType(string $line): string
    {
        if ($line !== '' && mb_stripos($line, '현장') !== false) {
            return 'onsite';
        }

        return 'pre';
    }

    protected function mapPaymentStatus(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return 'pending_payment';
        }

        $n = preg_replace('/\s+/u', '', $raw) ?? '';

        if ($n === '') {
            return 'pending_payment';
        }

        if (str_contains($n, '입금완료') || str_contains($n, '결제완료')) {
            return 'completed';
        }
        if (str_contains($n, '대기중') || str_contains($n, '입금대기') || str_contains($n, '결제대기')) {
            return 'pending';
        }
        if (str_contains($n, '취소요청')) {
            return 'cancel_requested';
        }
        if (str_contains($n, '취소')) {
            return 'cancelled';
        }
        if ($n === '확인') {
            // 레거시 CSV에서 관리 확인 완료로 쓰이는 경우가 많아 결제 완료로 간주
            return 'completed';
        }

        return null;
    }

    protected function parseAmountWon(string $line): int
    {
        if ($line === '') {
            return 0;
        }
        if (preg_match_all('/([\d,]+)\s*원/u', $line, $m)) {
            $last = end($m[1]);
            $digits = preg_replace('/[^\d]/', '', (string) $last);

            return max(0, (int) $digits);
        }
        if (preg_match('/([\d,]+)/u', $line, $m)) {
            $digits = preg_replace('/[^\d]/', '', $m[1]);

            return max(0, (int) $digits);
        }

        return 0;
    }

    /** @param list<?string> $candidates */
    protected function parseFirstCarbon(array $candidates): ?Carbon
    {
        foreach ($candidates as $c) {
            if ($c === null || $c === '') {
                continue;
            }
            try {
                return Carbon::parse($c);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    protected function parseDateOnly(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        try {
            return Carbon::parse($raw)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /** @param list<?string> $parts */
    protected function joinMemoParts(array $parts): ?string
    {
        $lines = [];
        foreach ($parts as $p) {
            if ($p !== null && trim($p) !== '') {
                $lines[] = trim($p);
            }
        }

        if ($lines === []) {
            return null;
        }

        return implode("\n\n", $lines);
    }

    /** @param list<?string> $vals */
    protected function firstNonEmpty(array $vals): ?string
    {
        foreach ($vals as $v) {
            if ($v !== null && $v !== '') {
                return $v;
            }
        }

        return null;
    }

    protected function resolveMemberId(?string $loginId): ?int
    {
        if ($loginId === null || $loginId === '') {
            return null;
        }
        $user = User::findByLoginId($loginId);

        return $user?->id;
    }
}

<?php

namespace App\Services\Backoffice;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * docs/data-migration/회원관리.csv (CP949) 이관.
 *
 * - 1행: 원본 시스템 컬럼명(참고). 2행이 비어 있으면 해당 열 키로 사용한다.
 * - 2행(매핑 행): 이관 후 항목명·키. 값이 「삭제」인 열은 값·레거시 모두 제외.
 * - 매핑 행 판별: 1행 기준 아이디·이름 열에 각각 문자열 "아이디","이름"이 오면 2행을 헤더로 쓴다.
 */
class MemberCsvImporter
{
    /** @var list<string> C층: 레거시 JSON에도 넣지 않음 */
    private const DROP_HEADER_SUBSTRINGS = [
        '이머니',
        '포인트',
        '쇼핑몰',
        '배너제휴',
        '스탬프',
        '어제 이머니',
        '지난주 이머니',
        '지난달 이머니',
        '어제 포인트',
        '지난주 포인트',
        '지난달 포인트',
    ];

    /**
     * users/매핑 필드로 옮긴 뒤 legacy JSON에서 제외할 키 (2행 기준 + 구 CSV 1행 기준 별칭).
     *
     * @var list<string>
     */
    private const MAPPED_HEADER_EXACT = [
        'No.',
        '회원등급',
        '그룹명 -> 회원등급',
        '그룹명',
        '아이디',
        '이름',
        '회원상태',
        '핸드폰',
        '컬럼 추가',
        '전화번호 -> 컬럼 추가 필요..',
        '전화번호',
        '이메일',
        '이-메일 -> 이메일',
        '이-메일',
        '직장 주소 1',
        '우편번호 -> 직장주소',
        '우편번호',
        '직장 주소 2',
        '주소 -> 직장주소',
        '주소',
        '직장 주소 3',
        '나머지주소 -> 직장주소',
        '나머지주소',
        '직장명',
        '직업 -> 직장명',
        '직업',
        '추가항목4 -> 직장명',
        '추가항목4',
        '의사번호',
        '추가항목1 -> 의사번호',
        '추가항목1',
        '전문의번호',
        '추가항목2 -> 전문의번호',
        '추가항목2',
        '전문과',
        '추가항목3 -> 전문과',
        '추가항목3',
        '출신 대학',
        '추가항목5 -> 출신대학',
        '추가항목5',
        '졸업년도',
        '추가항목6 -> 학교 졸업년도',
        '추가항목6',
        '영문이름',
        '추가항목7 -> 영문이름',
        '추가항목7',
        '연회비 납부 여부',
        '연회비',
        'DB에만 저장',
        '가입일',
        '휴면 계정 확인 용도',
        '최종 로그인 -> 휴면 계정 확인 용도',
        '최종 로그인',
        '회비납부여부 용도로 사용 (회비는 계정당 평생 1회만 냄)',
        '회비납부기준일 -> 회비납부여부 용도로 사용 (회비는 계정당 평생 1회만 냄)',
        '회비납부기준일',
    ];

    /**
     * @return array{created: int, updated: int, skipped: int, errors: list<string>}
     */
    public function import(
        string $absolutePath,
        bool $dryRun,
        ?int $limit,
        string $onDuplicate,
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

        $headerRow1 = fgetcsv($handle);
        if ($headerRow1 === false) {
            fclose($handle);
            throw new RuntimeException('CSV 헤더가 없습니다.');
        }

        $row1Norm = $this->normalizeHeaderRow($headerRow1);

        $secondLine = fgetcsv($handle);
        if ($secondLine === false) {
            fclose($handle);
            throw new RuntimeException('CSV 2행이 없습니다.');
        }

        /** @var list<array{index: int, key: string}> $columnMap */
        $columnMap = [];
        $pendingFirstDataRow = null;
        $rowNum = 1;

        if ($this->isMappingHeaderRow($row1Norm, $secondLine)) {
            $columnMap = $this->buildColumnMapFromMappingRow($headerRow1, $secondLine);
            $rowNum = 2;
        } else {
            $columnMap = $this->buildColumnMapFromLegacyRow1($headerRow1);
            $pendingFirstDataRow = $secondLine;
        }

        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        while (true) {
            if ($pendingFirstDataRow !== null) {
                $row = $pendingFirstDataRow;
                $pendingFirstDataRow = null;
            } else {
                $row = fgetcsv($handle);
                if ($row === false) {
                    break;
                }
            }

            $rowNum++;

            if ($this->rowIsEmpty($row)) {
                continue;
            }
            if ($limit !== null && ($stats['created'] + $stats['updated'] + $stats['skipped']) >= $limit) {
                break;
            }

            try {
                $assoc = $this->buildAssocFromColumnMap($columnMap, $row);
                $payload = $this->mapRow($assoc);
                $loginId = $payload['user']['login_id'] ?? '';
                if ($loginId === '') {
                    $stats['skipped']++;
                    $stats['errors'][] = "행 {$rowNum}: 아이디 없음";

                    continue;
                }

                $existing = User::query()->where('login_id', $loginId)->where('role', 'user')->first();

                if ($existing && $onDuplicate === 'skip') {
                    $stats['skipped']++;
                    continue;
                }

                if ($dryRun) {
                    $existing ? $stats['updated']++ : $stats['created']++;
                    continue;
                }

                if ($existing && $onDuplicate === 'update') {
                    $existing->update($payload['user']);
                    $stats['updated']++;
                    continue;
                }

                if ($existing) {
                    $stats['skipped']++;
                    continue;
                }

                User::create($payload['user']);
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
     * @return list<string>
     */
    private function normalizeHeaderRow(array $row1): array
    {
        $out = [];
        foreach ($row1 as $i => $h) {
            $out[$i] = $this->normalizeHeader((string) $h);
        }

        return $out;
    }

    private function normalizeHeader(string $header): string
    {
        $h = str_replace(["\r\n", "\r", "\n"], ' ', $header);

        return trim(preg_replace('/\s+/u', ' ', $h));
    }

    /**
     * 2행이 「매핑 정의 행」인지: 1행의 아이디·이름 열에 각각 표시 문자열이 오는 형태.
     *
     * @param  list<string>  $row1Norm
     * @param  list<string|null>  $row2
     */
    private function isMappingHeaderRow(array $row1Norm, array $row2): bool
    {
        $loginIdx = array_search('아이디', $row1Norm, true);
        $nameIdx = array_search('이름', $row1Norm, true);
        if ($loginIdx === false || $nameIdx === false) {
            return false;
        }

        $loginCell = isset($row2[$loginIdx]) ? trim((string) $row2[$loginIdx]) : '';
        $nameCell = isset($row2[$nameIdx]) ? trim((string) $row2[$nameIdx]) : '';

        return $loginCell === '아이디' && $nameCell === '이름';
    }

    /**
     * 2행: 「삭제」 열 제외. 2행 셀이 비어 있으면 1행 정규화명을 키로 사용.
     *
     * @param  list<string|null>  $row1
     * @param  list<string|null>  $row2
     * @return list<array{index: int, key: string}>
     */
    private function buildColumnMapFromMappingRow(array $row1, array $row2): array
    {
        $n = max(count($row1), count($row2));
        $map = [];
        for ($i = 0; $i < $n; $i++) {
            $n2 = $this->normalizeHeader((string) ($row2[$i] ?? ''));
            if ($n2 === '삭제') {
                continue;
            }
            $n1 = $this->normalizeHeader((string) ($row1[$i] ?? ''));
            $key = $n2 !== '' ? $n2 : $n1;
            if ($key === '') {
                continue;
            }
            $map[] = ['index' => $i, 'key' => $key];
        }

        return $map;
    }

    /**
     * 구형 CSV: 1행만 헤더.
     *
     * @param  list<string|null>  $row1
     * @return list<array{index: int, key: string}>
     */
    private function buildColumnMapFromLegacyRow1(array $row1): array
    {
        $map = [];
        foreach ($row1 as $i => $cell) {
            $key = $this->normalizeHeader((string) $cell);
            if ($key === '') {
                continue;
            }
            $map[] = ['index' => $i, 'key' => $key];
        }

        return $map;
    }

    /**
     * 동일 키가 여러 열에 있으면, 비어 있지 않은 값으로 덮어쓴다(뒤쪽 열이 우선).
     *
     * @param  list<array{index: int, key: string}>  $columnMap
     * @param  list<string|null>  $row
     * @return array<string, string>
     */
    private function buildAssocFromColumnMap(array $columnMap, array $row): array
    {
        $assoc = [];
        foreach ($columnMap as $spec) {
            $i = $spec['index'];
            $key = $spec['key'];
            $val = isset($row[$i]) ? trim((string) $row[$i]) : '';
            if (! array_key_exists($key, $assoc)) {
                $assoc[$key] = $val;
            } elseif ($val !== '') {
                $assoc[$key] = $val;
            }
        }

        return $assoc;
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

    /**
     * 동일 의미 키 중 비어 있지 않은 첫 값.
     *
     * @param  array<string, string>  $assoc
     * @param  list<string>  $keys
     */
    private function assocFirstNonEmpty(array $assoc, array $keys): string
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $assoc)) {
                continue;
            }
            $v = trim((string) $assoc[$k]);
            if ($v !== '') {
                return $v;
            }
        }

        return '';
    }

    /**
     * @param  array<string, string>  $assoc
     * @return array{user: array<string, mixed>, legacy: array<string, string>}
     */
    private function mapRow(array $assoc): array
    {
        $g = function (string $k) use ($assoc): string {
            return $assoc[$k] ?? '';
        };

        $loginId = $g('아이디');
        $name = $g('이름');
        $emailRaw = $this->assocFirstNonEmpty($assoc, ['이메일', '이-메일 -> 이메일', '이-메일']);
        $email = $emailRaw !== '' ? $emailRaw : ($loginId !== '' ? $loginId . '@migrated.invalid' : 'user_' . Str::lower(Str::random(10)) . '@migrated.invalid');
        if (User::query()->where('email', $email)->exists()) {
            $email = 'dup_' . Str::lower(Str::random(8)) . '_' . preg_replace('/[^a-zA-Z0-9@._-]/', '_', $email);
        }

        $phoneDigits = $this->normalizeDigits($g('핸드폰'));
        $phone = $phoneDigits !== '' ? $phoneDigits : 'sns_import_' . ($loginId !== '' ? $loginId : Str::random(8));

        $memberLevel = $this->mapMemberLevel($this->assocFirstNonEmpty($assoc, ['회원등급', '그룹명 -> 회원등급', '그룹명']));
        $jobRaw = $this->assocFirstNonEmpty($assoc, ['회원상태']);
        $jobType = $this->mapJobType($jobRaw);

        $workplaceZip = $this->assocFirstNonEmpty($assoc, ['직장 주소 1', '우편번호 -> 직장주소', '우편번호']);
        $workplaceAddr = $this->assocFirstNonEmpty($assoc, ['직장 주소 2', '주소 -> 직장주소', '주소']);
        $workplaceDetail = $this->assocFirstNonEmpty($assoc, ['직장 주소 3', '나머지주소 -> 직장주소', '나머지주소']);
        $workplaceFromExtra4 = $this->assocFirstNonEmpty($assoc, ['추가항목4 -> 직장명', '추가항목4']);
        $workplaceFromJobCol = $this->assocFirstNonEmpty($assoc, ['직업 -> 직장명', '직업']);
        $workplaceNameMerged = $this->assocFirstNonEmpty($assoc, ['직장명']);
        $workplaceName = $workplaceNameMerged !== '' ? $workplaceNameMerged : ($workplaceFromExtra4 !== '' ? $workplaceFromExtra4 : $workplaceFromJobCol);
        $workplacePhone = $this->assocFirstNonEmpty($assoc, ['컬럼 추가', '전화번호 -> 컬럼 추가 필요..', '전화번호']);

        $feeBasis = $this->parseDateFlexible($this->assocFirstNonEmpty($assoc, [
            '회비납부여부 용도로 사용 (회비는 계정당 평생 1회만 냄)',
            '회비납부기준일 -> 회비납부여부 용도로 사용 (회비는 계정당 평생 1회만 냄)',
            '회비납부기준일',
        ]));
        $annualFee = $this->mapAnnualFeeStatus($this->assocFirstNonEmpty($assoc, ['연회비 납부 여부', '연회비']));

        $lastLogin = $this->parseDateTimeFlexible($this->assocFirstNonEmpty($assoc, [
            '휴면 계정 확인 용도',
            '최종 로그인 -> 휴면 계정 확인 용도',
            '최종 로그인',
        ]));
        $joinedAt = $this->parseDateTimeFlexible($this->assocFirstNonEmpty($assoc, ['DB에만 저장', '가입일']));

        $extra1 = $this->assocFirstNonEmpty($assoc, ['의사번호', '추가항목1 -> 의사번호', '추가항목1']);
        $extra2 = $this->assocFirstNonEmpty($assoc, ['전문의번호', '추가항목2 -> 전문의번호', '추가항목2']);
        $extra3 = $this->assocFirstNonEmpty($assoc, ['전문과', '추가항목3 -> 전문과', '추가항목3']);
        $extra5 = $this->assocFirstNonEmpty($assoc, ['출신 대학', '추가항목5 -> 출신대학', '추가항목5']);
        $extra6 = $this->assocFirstNonEmpty($assoc, ['졸업년도', '추가항목6 -> 학교 졸업년도', '추가항목6']);
        $extra7 = $this->assocFirstNonEmpty($assoc, ['영문이름', '추가항목7 -> 영문이름', '추가항목7']);

        $schoolName = $extra5 !== '' ? mb_substr($extra5, 0, 255) : '—';

        $legacy = [];
        foreach ($assoc as $key => $val) {
            if ($val === '') {
                continue;
            }
            if ($this->shouldDropLegacyKey($key)) {
                continue;
            }
            if (in_array($key, self::MAPPED_HEADER_EXACT, true)) {
                continue;
            }
            $legacy[$key] = $val;
        }

        $legacyCsvNo = ctype_digit($g('No.')) ? (int) $g('No.') : null;

        $graduateYear = $this->normalizeGraduateYear($extra6);

        $user = [
            'join_type' => 'email',
            'login_id' => $loginId,
            'name' => $name !== '' ? mb_substr($name, 0, 20) : $loginId,
            'name_en' => $extra7 !== '' ? mb_substr($extra7, 0, 100) : null,
            'email' => $email,
            'password' => Hash::make(Str::password(32)),
            'phone_number' => $phone,
            'role' => 'user',
            'is_active' => true,
            'terms_agreed_at' => $joinedAt ?? now(),
            'member_level' => $memberLevel,
            'job_type' => $jobType,
            'member_status_raw' => $jobRaw !== '' ? mb_substr($jobRaw, 0, 255) : null,
            'license_number' => $extra1 !== '' ? mb_substr($extra1, 0, 80) : null,
            'specialist_number' => $extra2 !== '' ? mb_substr($extra2, 0, 80) : null,
            'specialty' => $extra3 !== '' ? mb_substr($extra3, 0, 120) : null,
            'workplace_name' => $workplaceName !== '' ? mb_substr($workplaceName, 0, 200) : null,
            'workplace_phone' => $workplacePhone !== '' ? mb_substr($workplacePhone, 0, 40) : null,
            'workplace_zipcode' => $workplaceZip !== '' ? mb_substr($workplaceZip, 0, 20) : null,
            'workplace_address' => $workplaceAddr !== '' ? $workplaceAddr : null,
            'workplace_address_detail' => $workplaceDetail !== '' ? $workplaceDetail : null,
            'school_name' => $schoolName,
            'graduate_year' => $graduateYear,
            'membership_fee_basis_at' => $feeBasis,
            'annual_fee_status' => $annualFee,
            'certified_instructor' => false,
            'committee_codes' => null,
            'legacy_import_json' => $legacy === [] ? null : $legacy,
            'legacy_csv_no' => $legacyCsvNo,
            'withdrawn_at' => null,
        ];

        if ($lastLogin) {
            $user['last_login_at'] = $lastLogin;
        }
        if ($joinedAt) {
            $user['created_at'] = $joinedAt;
            $user['updated_at'] = $joinedAt;
        }

        return ['user' => $user, 'legacy' => $legacy];
    }

    private function shouldDropLegacyKey(string $key): bool
    {
        foreach (self::DROP_HEADER_SUBSTRINGS as $sub) {
            if (str_contains($key, $sub)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeDigits(string $s): string
    {
        return preg_replace('/\D/', '', $s) ?? '';
    }

    private function mapMemberLevel(string $label): ?string
    {
        if ($label === '') {
            return 'regular';
        }
        if (str_contains($label, '가입대기') || str_contains($label, '대기')) {
            return 'pending';
        }
        if (str_contains($label, '준회원')) {
            return 'associate';
        }
        if (str_contains($label, '평생')) {
            return 'lifetime';
        }
        if (str_contains($label, '시니어')) {
            return 'senior';
        }
        if (str_contains($label, '정회원')) {
            return 'regular';
        }

        return 'regular';
    }

    private function mapJobType(string $raw): ?string
    {
        if ($raw === '') {
            return null;
        }
        $map = [
            '전문의' => 'specialist',
            '전공의' => 'resident',
            '공보의' => 'public_doctor',
            '군의관' => 'military_doctor',
            '간호사' => 'nurse',
            '기타' => 'other',
        ];
        foreach ($map as $ko => $code) {
            if (str_contains($raw, $ko)) {
                return $code;
            }
        }

        return 'other';
    }

    private function mapAnnualFeeStatus(string $raw): ?string
    {
        if ($raw === '') {
            return null;
        }
        if (str_contains($raw, '미납')) {
            return 'unpaid';
        }
        if (str_contains($raw, '완납') || str_contains($raw, '납부')) {
            return 'paid';
        }
        if (str_contains($raw, '없')) {
            return 'none';
        }

        return null;
    }

    private function parseDateFlexible(string $s): ?string
    {
        $s = trim($s);
        if ($s === '' || $s === '-') {
            return null;
        }
        try {
            return Carbon::parse(str_replace('.', '-', $s))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseDateTimeFlexible(string $s): ?Carbon
    {
        $s = trim($s);
        if ($s === '' || $s === '-' || $s === '0' || $s === '0000-00-00' || $s === '0000.00.00') {
            return null;
        }
        try {
            $parsed = Carbon::parse(str_replace('.', '-', $s));
            // CSV의 0/오염 값이 epoch(1970)로 파싱되는 경우는 유효 가입일로 보지 않는다.
            if ($parsed->year <= 1970) {
                return null;
            }

            return $parsed;
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeGraduateYear(string $raw): ?int
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $raw) ?? '';
        if (strlen($digits) !== 4) {
            return null;
        }

        $year = (int) $digits;
        $maxYear = (int) now()->format('Y') + 1;
        if ($year < 1900 || $year > $maxYear) {
            return null;
        }

        return $year;
    }
}

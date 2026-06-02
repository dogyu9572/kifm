<?php

namespace App\Services\Backoffice;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class StatsMemberService
{
    /**
     * 회원 등급 코드 → 한글 라벨 매핑.
     * member_level enum: pending|associate|regular|lifetime|senior
     */
    private const GRADE_LABELS = [
        'pending' => '가입대기회원',
        'associate' => '준회원',
        'regular' => '정회원',
        'lifetime' => '평생회원',
        'senior' => '시니어 회원',
    ];

    /**
     * 요약 카드 데이터.
     *
     * @return array{totalMembers:int, periodJoin:int, periodLeave:int, activeMembers:int}
     */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        $base = $this->baseQuery();
        $totalMembers = (clone $base)->count();
        $activeMembers = (clone $base)->whereNull('withdrawn_at')->count();

        $periodJoin = (clone $base)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();

        $periodLeave = (clone $base)
            ->whereNotNull('withdrawn_at')
            ->whereBetween('withdrawn_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();

        return [
            'totalMembers' => $totalMembers,
            'periodJoin' => $periodJoin,
            'periodLeave' => $periodLeave,
            'activeMembers' => $activeMembers,
        ];
    }

    /**
     * 기간 내 월별 신규 가입(created_at) / 탈퇴(withdrawn_at) 집계.
     * 데이터가 없는 월도 0으로 채워서 반환한다.
     *
     * @return array{rows:list<array{yearMonth:string, yearMonthLabel:string, joinCount:int, leaveCount:int}>, totalJoin:int, totalLeave:int}
     */
    public function monthly(CarbonInterface $from, CarbonInterface $to): array
    {
        $startMonth = $from->copy()->startOfMonth();
        $endMonth = $to->copy()->endOfMonth();
        $range = [$from->copy()->startOfDay(), $to->copy()->endOfDay()];

        $joinRows = $this->baseQuery()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c")
            ->whereBetween('created_at', $range)
            ->groupBy('ym')
            ->pluck('c', 'ym');

        $leaveRows = $this->baseQuery()
            ->selectRaw("DATE_FORMAT(withdrawn_at, '%Y-%m') AS ym, COUNT(*) AS c")
            ->whereNotNull('withdrawn_at')
            ->whereBetween('withdrawn_at', $range)
            ->groupBy('ym')
            ->pluck('c', 'ym');

        $rows = [];
        $totalJoin = 0;
        $totalLeave = 0;

        $cursor = $startMonth->copy();
        while ($cursor->lessThanOrEqualTo($endMonth)) {
            $ym = $cursor->format('Y-m');
            $joinCount = (int) ($joinRows[$ym] ?? 0);
            $leaveCount = (int) ($leaveRows[$ym] ?? 0);
            $totalJoin += $joinCount;
            $totalLeave += $leaveCount;
            $rows[] = [
                'yearMonth' => $ym,
                'yearMonthLabel' => $cursor->format('Y') . '년 ' . (int) $cursor->format('n') . '월',
                'joinCount' => $joinCount,
                'leaveCount' => $leaveCount,
            ];
            $cursor->addMonth();
        }

        return [
            'rows' => $rows,
            'totalJoin' => $totalJoin,
            'totalLeave' => $totalLeave,
        ];
    }

    /**
     * 회원 등급별 분포 (현재 시점 스냅샷, 기간 필터 무관).
     * member_level NULL 인 회원은 pending(가입대기) 으로 합산한다.
     *
     * @return array{rows:list<array{gradeCode:string, gradeLabel:string, count:int, ratio:float}>, total:int}
     */
    public function gradeDistribution(): array
    {
        $rawCounts = $this->baseQuery()
            ->selectRaw('COALESCE(member_level, ?) AS grade_code, COUNT(*) AS c', ['pending'])
            ->groupBy('grade_code')
            ->pluck('c', 'grade_code');

        $total = (int) $rawCounts->sum();

        $rows = [];
        foreach (self::GRADE_LABELS as $code => $label) {
            $count = (int) ($rawCounts[$code] ?? 0);
            $rows[] = [
                'gradeCode' => $code,
                'gradeLabel' => $label,
                'count' => $count,
                'ratio' => $total > 0 ? round($count * 100 / $total, 1) : 0.0,
            ];
        }

        return [
            'rows' => $rows,
            'total' => $total,
        ];
    }

    /**
     * 일반 회원(role='user') 만을 대상으로 하는 기본 쿼리.
     */
    protected function baseQuery()
    {
        return DB::table('users')->where('role', 'user');
    }

    /**
     * 기본 기간(오늘 기준 1년 전 ~ 오늘) 반환.
     *
     * @return array{from:Carbon, to:Carbon}
     */
    public function defaultRange(): array
    {
        $to = Carbon::today();
        $from = $to->copy()->subYear();

        return ['from' => $from, 'to' => $to];
    }

    /**
     * 입력 일자 문자열을 Carbon 으로 정규화하고 from <= to 보장.
     *
     * @return array{from:Carbon, to:Carbon}
     */
    public function normalizeRange(?string $fromInput, ?string $toInput): array
    {
        $default = $this->defaultRange();
        $from = $this->parseDate($fromInput) ?? $default['from'];
        $to = $this->parseDate($toInput) ?? $default['to'];

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return ['from' => $from, 'to' => $to];
    }

    protected function parseDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}

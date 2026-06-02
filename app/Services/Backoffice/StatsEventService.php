<?php

namespace App\Services\Backoffice;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class StatsEventService
{
    /**
     * 기본 기간(오늘 기준 1년 전 ~ 오늘).
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
     * 입력 일자 정규화 + from <= to 보장.
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

    /**
     * 요약 카드 데이터.
     * 기간 필터와 집계 기준은 행사별 참가 현황 표와 동일하게 registered_at/cancelled 제외 기준을 사용한다.
     *
     * @return array{eventCount:int, totalParticipants:int, totalAmount:int}
     */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        return $this->summaryFromByEvent($this->byEvent($from, $to));
    }

    /**
     * @param array{rows:list<array{eventId:int, name:string, eventDate:?string, applied:int, paid:int, amount:int}>, totals:array{applied:int, paid:int, amount:int}} $byEvent
     * @return array{eventCount:int, totalParticipants:int, totalAmount:int}
     */
    public function summaryFromByEvent(array $byEvent): array
    {
        return [
            'eventCount' => count($byEvent['rows']),
            'totalParticipants' => $byEvent['totals']['applied'],
            'totalAmount' => $byEvent['totals']['amount'],
        ];
    }

    /**
     * 행사별 참가 현황 표 데이터.
     *
     * @return array{rows:list<array{eventId:int, name:string, eventDate:?string, applied:int, paid:int, amount:int}>, totals:array{applied:int, paid:int, amount:int}}
     */
    public function byEvent(CarbonInterface $from, CarbonInterface $to): array
    {
        $range = $this->rangeBoundaries($from, $to);

        $aggregates = DB::table('academic_event_registrations')
            ->whereBetween('registered_at', $range)
            ->where('payment_status', '!=', 'cancelled')
            ->selectRaw('academic_event_id,
                COUNT(*) AS applied,
                SUM(CASE WHEN payment_status = "completed" THEN 1 ELSE 0 END) AS paid,
                SUM(CASE WHEN payment_status = "completed" THEN total_amount ELSE 0 END) AS amount')
            ->groupBy('academic_event_id')
            ->get()
            ->keyBy('academic_event_id');

        if ($aggregates->isEmpty()) {
            return [
                'rows' => [],
                'totals' => ['applied' => 0, 'paid' => 0, 'amount' => 0],
            ];
        }

        $events = DB::table('academic_events')
            ->whereIn('id', $aggregates->keys())
            ->orderByDesc('id')
            ->get(['id', 'title', 'start_at']);

        $rows = [];
        $totalApplied = 0;
        $totalPaid = 0;
        $totalAmount = 0;

        foreach ($events as $event) {
            $agg = $aggregates[$event->id] ?? null;
            $applied = (int) ($agg->applied ?? 0);
            $paid = (int) ($agg->paid ?? 0);
            $amount = (int) ($agg->amount ?? 0);

            $totalApplied += $applied;
            $totalPaid += $paid;
            $totalAmount += $amount;

            $rows[] = [
                'eventId' => (int) $event->id,
                'name' => (string) $event->title,
                'eventDate' => $event->start_at ? Carbon::parse($event->start_at)->format('Y-m-d') : null,
                'applied' => $applied,
                'paid' => $paid,
                'amount' => $amount,
            ];
        }

        return [
            'rows' => $rows,
            'totals' => [
                'applied' => $totalApplied,
                'paid' => $totalPaid,
                'amount' => $totalAmount,
            ],
        ];
    }

    /**
     * @return array{0:Carbon,1:Carbon}
     */
    protected function rangeBoundaries(CarbonInterface $from, CarbonInterface $to): array
    {
        return [$from->copy()->startOfDay(), $to->copy()->endOfDay()];
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

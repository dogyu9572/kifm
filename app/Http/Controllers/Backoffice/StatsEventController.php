<?php

namespace App\Http\Controllers\Backoffice;

use App\Services\Backoffice\StatsEventService;
use Illuminate\Http\Request;

class StatsEventController extends BaseController
{
    public function __construct(
        private StatsEventService $statsEventService
    ) {}

    /**
     * 행사 통계 메인 페이지.
     */
    public function index(Request $request)
    {
        $range = $this->statsEventService->normalizeRange(
            $request->query('date_from'),
            $request->query('date_to'),
        );

        $byEvent = $this->statsEventService->byEvent($range['from'], $range['to']);
        $summary = $this->statsEventService->summaryFromByEvent($byEvent);

        return $this->view('backoffice.stats.events.index', [
            'dateFrom' => $range['from']->format('Y-m-d'),
            'dateTo' => $range['to']->format('Y-m-d'),
            'summary' => $summary,
            'byEvent' => $byEvent,
        ]);
    }
}

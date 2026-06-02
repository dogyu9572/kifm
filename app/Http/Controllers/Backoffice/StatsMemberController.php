<?php

namespace App\Http\Controllers\Backoffice;

use App\Services\Backoffice\StatsMemberService;
use Illuminate\Http\Request;

class StatsMemberController extends BaseController
{
    public function __construct(
        private StatsMemberService $statsMemberService
    ) {}

    /**
     * 회원 통계 메인 페이지.
     */
    public function index(Request $request)
    {
        $range = $this->statsMemberService->normalizeRange(
            $request->query('date_from'),
            $request->query('date_to'),
        );

        $summary = $this->statsMemberService->summary($range['from'], $range['to']);
        $monthly = $this->statsMemberService->monthly($range['from'], $range['to']);
        $summary['periodJoin'] = $monthly['totalJoin'];
        $summary['periodLeave'] = $monthly['totalLeave'];
        $gradeDistribution = $this->statsMemberService->gradeDistribution();

        return $this->view('backoffice.stats.members.index', [
            'dateFrom' => $range['from']->format('Y-m-d'),
            'dateTo' => $range['to']->format('Y-m-d'),
            'summary' => $summary,
            'monthly' => $monthly,
            'gradeDistribution' => $gradeDistribution,
        ]);
    }
}

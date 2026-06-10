<?php

namespace App\Services\Frontend;

use App\Models\User;
use App\Services\Certified\CertifiedQualificationEvaluator;

/**
 * 마이페이지 개인정보 상단 — 인증의 유지 위젯용 집계.
 */
class MypageCertificationSummaryService
{
    public function __construct(
        private readonly CertifiedQualificationEvaluator $evaluator,
    ) {}

    /**
     * @return array{
     *   has_certified_member: bool,
     *   acquisition: array<string, mixed>,
     *   renewal: array<string, mixed>,
     *   progress_percent: float
     * }
     */
    public function summarize(User $user): array
    {
        $summary = $this->evaluator->evaluate($user);
        unset($summary['certified_member']);

        return $summary;
    }
}

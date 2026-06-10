<?php

namespace App\Services\Certified;

use App\Models\CertifiedMember;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CertifiedQualificationSyncService
{
    public function __construct(
        private readonly CertifiedQualificationEvaluator $evaluator,
    ) {}

    public function syncForMember(?User $user): ?CertifiedMember
    {
        if (! $user || $user->role !== 'user' || $user->withdrawn_at) {
            return null;
        }

        return DB::transaction(function () use ($user): ?CertifiedMember {
            $result = $this->evaluator->evaluate($user);
            $certifiedMember = $result['certified_member'];

            if (! $certifiedMember) {
                return $this->createAcquisitionIfSatisfied($user, $result['acquisition']);
            }

            $this->createRenewalIfSatisfied($certifiedMember, $result['renewal']);

            return $certifiedMember->refresh();
        });
    }

    /**
     * @param array<string, mixed> $acquisition
     */
    private function createAcquisitionIfSatisfied(User $user, array $acquisition): ?CertifiedMember
    {
        if (empty($acquisition['completed']) || empty($acquisition['completed_at'])) {
            return null;
        }

        $acquiredDate = $acquisition['completed_at'] instanceof Carbon
            ? $acquisition['completed_at']->copy()
            : Carbon::parse((string) $acquisition['completed_at']);
        $validityStart = $acquiredDate->copy()->startOfMonth();
        $validityEnd = $validityStart->copy()->addYears(5)->subDay();

        $certifiedMember = CertifiedMember::query()->create([
            'member_id' => $user->id,
            'validity_start_date' => $validityStart->toDateString(),
            'validity_end_date' => $validityEnd->toDateString(),
            'acquired_date' => $acquiredDate->toDateString(),
            'acquired_validity_start' => $validityStart->toDateString(),
            'acquired_validity_end' => $validityEnd->toDateString(),
            'winter_course_completed' => true,
            'exam_passed' => false,
        ]);

        $user->forceFill(['certified_instructor' => true])->save();

        return $certifiedMember;
    }

    /**
     * @param array<string, mixed> $renewal
     */
    private function createRenewalIfSatisfied(CertifiedMember $certifiedMember, array $renewal): void
    {
        if (empty($renewal['completed']) || empty($renewal['completed_at'])) {
            return;
        }

        $baseEnd = $certifiedMember->validity_end_date->copy();
        $renewalStart = $baseEnd->copy()->addDay();
        $renewalEnd = $baseEnd->copy()->addYears(5);

        $exists = $certifiedMember->renewals()
            ->whereDate('renewal_validity_start', $renewalStart->toDateString())
            ->whereDate('renewal_validity_end', $renewalEnd->toDateString())
            ->exists();
        if ($exists) {
            return;
        }

        $renewalDate = $renewal['completed_at'] instanceof Carbon
            ? $renewal['completed_at']->copy()
            : Carbon::parse((string) $renewal['completed_at']);
        $nextSeq = ((int) $certifiedMember->renewals()->max('renewal_seq')) + 1;

        $certifiedMember->renewals()->create([
            'renewal_seq' => $nextSeq,
            'renewal_date' => $renewalDate->toDateString(),
            'renewal_validity_start' => $renewalStart->toDateString(),
            'renewal_validity_end' => $renewalEnd->toDateString(),
            'attendance_general' => (int) ($renewal['general_count'] ?? 0),
            'attendance_winter' => (int) ($renewal['winter_count'] ?? 0),
        ]);

        $certifiedMember->update([
            'validity_start_date' => $renewalStart->toDateString(),
            'validity_end_date' => $renewalEnd->toDateString(),
        ]);
    }
}

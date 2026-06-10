<?php

namespace App\Services\Certified;

use App\Models\AcademicEventRegistration;
use App\Models\CertifiedMember;
use App\Models\EduTrainingPayment;
use App\Models\User;
use App\Services\Frontend\PublicTrainingCourseService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CertifiedQualificationEvaluator
{
    private const COMPLETED_PAYMENT_STATUS = 'completed';
    private const REGULAR_TRAINING_SEASONS = ['spring', 'summer', 'fall'];
    private const RENEWAL_ACADEMIC_SEASONS = ['spring', 'fall'];
    private const REQUIRED_REGULAR_COUNT = 2;
    private const REQUIRED_RENEWAL_GENERAL_COUNT = 4;
    private const REQUIRED_WINTER_ROUND_COUNT = 3;

    /**
     * @return array{
     *   has_certified_member: bool,
     *   certified_member: CertifiedMember|null,
     *   acquisition: array<string, mixed>,
     *   renewal: array<string, mixed>,
     *   progress_percent: float
     * }
     */
    public function evaluate(User $user): array
    {
        $certifiedMember = CertifiedMember::query()
            ->with('renewals')
            ->where('member_id', $user->id)
            ->first();
        $trainingPayments = $this->completedTrainingPayments($user);
        $acquisition = $this->acquisitionSummary($trainingPayments, $certifiedMember);
        $renewal = $this->renewalSummary($user, $trainingPayments, $certifiedMember);

        return [
            'has_certified_member' => $certifiedMember !== null,
            'certified_member' => $certifiedMember,
            'acquisition' => $acquisition,
            'renewal' => $renewal,
            'progress_percent' => $renewal['general_percent'],
        ];
    }

    private function completedTrainingPayments(User $user): Collection
    {
        return EduTrainingPayment::query()
            ->with(['training.rounds', 'items'])
            ->where('member_id', $user->id)
            ->where('payment_status', self::COMPLETED_PAYMENT_STATUS)
            ->whereNull('cancelled_at')
            ->get();
    }

    private function acquisitionSummary(Collection $trainingPayments, ?CertifiedMember $certifiedMember): array
    {
        $regulars = [];
        foreach ($trainingPayments as $payment) {
            $training = $payment->training;
            if (! $training || ! in_array((string) $training->season, self::REGULAR_TRAINING_SEASONS, true)) {
                continue;
            }

            $date = $this->trainingPaymentDate($payment);
            $year = $date ? (int) $date->format('Y') : ($training->year ? (int) $training->year : null);
            if ($year !== null) {
                $regulars[(int) $training->id] = [
                    'year' => $year,
                    'date' => $date,
                ];
            }
        }

        $regularCount = count($regulars);
        $regularCollection = collect($regulars);
        $evenRegular = $regularCollection
            ->filter(fn (array $item): bool => ((int) $item['year']) % 2 === 0)
            ->sortBy('date')
            ->first();
        $oddRegular = $regularCollection
            ->filter(fn (array $item): bool => ((int) $item['year']) % 2 === 1)
            ->sortBy('date')
            ->first();
        $regularCompleted = $regularCount >= self::REQUIRED_REGULAR_COUNT && $evenRegular && $oddRegular;
        $regularCompletedAt = $regularCompleted
            ? $this->latestDate([$evenRegular['date'] ?? null, $oddRegular['date'] ?? null])
            : null;

        $winterCompletions = $this->winterFullCompletions($trainingPayments);
        $winterCompleted = $winterCompletions->isNotEmpty() || (bool) ($certifiedMember?->winter_course_completed ?? false);
        $winterCount = max($winterCompletions->count(), $winterCompleted ? 1 : 0);
        $winterCompletedAt = $this->earliestDate($winterCompletions->pluck('completed_at')->all());
        $examEligible = $regularCompleted && $winterCompleted;
        $completedAt = $examEligible ? $this->latestDate([$regularCompletedAt, $winterCompletedAt]) : null;

        return [
            'regular_count' => $regularCount,
            'regular_required' => self::REQUIRED_REGULAR_COUNT,
            'regular_completed' => $regularCompleted,
            'regular_even_completed' => (bool) $evenRegular,
            'regular_odd_completed' => (bool) $oddRegular,
            'winter_count' => $winterCount,
            'winter_completed' => $winterCompleted,
            'exam_eligible' => $examEligible,
            'exam_passed' => (bool) ($certifiedMember?->exam_passed ?? false),
            'completed' => $examEligible,
            'completed_at' => $completedAt,
        ];
    }

    private function renewalSummary(User $user, Collection $trainingPayments, ?CertifiedMember $certifiedMember): array
    {
        if (! $certifiedMember) {
            return [
                'validity_period' => '',
                'remaining_days' => null,
                'd_day_label' => '',
                'general_count' => 0,
                'general_required' => self::REQUIRED_RENEWAL_GENERAL_COUNT,
                'general_percent' => 0.0,
                'winter_count' => 0,
                'winter_required' => 1,
                'winter_percent' => 0.0,
                'completed' => false,
                'completed_at' => null,
            ];
        }

        $start = $certifiedMember->validity_start_date;
        $end = $certifiedMember->validity_end_date;
        $academicResult = $this->renewalAcademicResult($user, $start, $end);
        $summerResult = $this->renewalSummerTrainingResult($trainingPayments, $start, $end);
        $winterResult = $this->renewalWinterResult($trainingPayments, $start, $end);
        $generalCount = $academicResult['count'] + $summerResult['count'];
        $winterCount = $winterResult['count'];
        $remainingDays = Carbon::today()->diffInDays($end, false);
        $completed = $generalCount >= self::REQUIRED_RENEWAL_GENERAL_COUNT && $winterCount >= 1;
        $completedAt = $completed
            ? $this->latestDate([$academicResult['completed_at'], $summerResult['completed_at'], $winterResult['completed_at']])
            : null;

        return [
            'validity_period' => $this->periodText($start, $end),
            'remaining_days' => $remainingDays,
            'd_day_label' => $remainingDays >= 0 ? 'D-' . $remainingDays : '만료',
            'general_count' => $generalCount,
            'general_required' => self::REQUIRED_RENEWAL_GENERAL_COUNT,
            'general_percent' => $this->percent($generalCount, self::REQUIRED_RENEWAL_GENERAL_COUNT),
            'winter_count' => $winterCount,
            'winter_required' => 1,
            'winter_percent' => $this->percent($winterCount, 1),
            'completed' => $completed,
            'completed_at' => $completedAt,
        ];
    }

    /**
     * @return array{count: int, completed_at: Carbon|null}
     */
    private function renewalAcademicResult(User $user, Carbon $start, Carbon $end): array
    {
        $rows = AcademicEventRegistration::query()
            ->with('event')
            ->where('member_id', $user->id)
            ->where('payment_status', self::COMPLETED_PAYMENT_STATUS)
            ->whereNull('cancelled_at')
            ->get()
            ->filter(function (AcademicEventRegistration $registration) use ($start, $end): bool {
                $event = $registration->event;
                if (! $event || ! in_array((string) $event->season, self::RENEWAL_ACADEMIC_SEASONS, true)) {
                    return false;
                }

                return $this->isBetween($event->start_at ?: $registration->registered_at, $start, $end);
            })
            ->map(fn (AcademicEventRegistration $registration): array => [
                'id' => (int) $registration->academic_event_id,
                'date' => $registration->event?->start_at ?: $registration->registered_at,
            ])
            ->unique('id')
            ->values();

        return [
            'count' => $rows->count(),
            'completed_at' => $this->latestDate($rows->pluck('date')->all()),
        ];
    }

    /**
     * @return array{count: int, completed_at: Carbon|null}
     */
    private function renewalSummerTrainingResult(Collection $trainingPayments, Carbon $start, Carbon $end): array
    {
        $rows = $trainingPayments
            ->filter(function (EduTrainingPayment $payment) use ($start, $end): bool {
                return ($payment->training?->season === 'summer')
                    && $this->isBetween($this->trainingPaymentDate($payment), $start, $end);
            })
            ->map(fn (EduTrainingPayment $payment): array => [
                'id' => (int) $payment->edu_training_id,
                'date' => $this->trainingPaymentDate($payment),
            ])
            ->unique('id')
            ->values();

        return [
            'count' => $rows->count(),
            'completed_at' => $this->latestDate($rows->pluck('date')->all()),
        ];
    }

    /**
     * @return array{count: int, completed_at: Carbon|null}
     */
    private function renewalWinterResult(Collection $trainingPayments, Carbon $start, Carbon $end): array
    {
        $rounds = [];
        foreach ($trainingPayments as $payment) {
            if ($payment->training?->season !== 'winter') {
                continue;
            }

            foreach ($this->paymentRounds($payment) as $round) {
                $date = $round->lecture_date ?: $this->trainingPaymentDate($payment);
                if ($this->isBetween($date, $start, $end)) {
                    $rounds[(int) $round->id] = $date;
                }
            }
        }

        return [
            'count' => intdiv(count($rounds), self::REQUIRED_WINTER_ROUND_COUNT),
            'completed_at' => $this->latestDate(array_values($rounds)),
        ];
    }

    private function winterFullCompletions(Collection $trainingPayments): Collection
    {
        $groups = [];
        foreach ($trainingPayments as $payment) {
            if ($payment->training?->season !== 'winter') {
                continue;
            }

            $trainingId = (int) $payment->edu_training_id;
            $groups[$trainingId] ??= [
                'round_orders' => [],
                'dates' => [],
            ];

            foreach ($this->paymentRounds($payment) as $round) {
                $groups[$trainingId]['round_orders'][(int) $round->round_order] = true;
                $groups[$trainingId]['dates'][] = $round->lecture_date ?: $this->trainingPaymentDate($payment);
            }
        }

        return collect($groups)
            ->filter(fn (array $group): bool => isset($group['round_orders'][1], $group['round_orders'][2], $group['round_orders'][3]))
            ->map(fn (array $group): array => [
                'completed_at' => $this->latestDate($group['dates']),
            ])
            ->values();
    }

    private function paymentRounds(EduTrainingPayment $payment): Collection
    {
        $rounds = $payment->training?->rounds ?? collect();
        $roundsById = $rounds->keyBy('id');

        return $payment->items
            ->map(function ($item) use ($roundsById) {
                $roundId = $this->roundIdFromCategory((string) $item->category);

                return $roundId ? $roundsById->get($roundId) : null;
            })
            ->filter()
            ->values();
    }

    private function roundIdFromCategory(string $category): ?int
    {
        $prefix = PublicTrainingCourseService::ROUND_ITEM_CATEGORY_PREFIX;
        if (! str_starts_with($category, $prefix)) {
            return null;
        }

        $roundId = (int) str_replace($prefix, '', $category);

        return $roundId > 0 ? $roundId : null;
    }

    private function trainingPaymentDate(EduTrainingPayment $payment): ?Carbon
    {
        $round = $this->paymentRounds($payment)
            ->sortBy('lecture_date')
            ->first();

        return $round?->lecture_date ?: $payment->paid_at ?: $payment->registered_at;
    }

    private function isBetween(mixed $date, Carbon $start, Carbon $end): bool
    {
        if (! $date) {
            return false;
        }

        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $date->betweenIncluded($start->copy()->startOfDay(), $end->copy()->endOfDay());
    }

    private function latestDate(array $dates): ?Carbon
    {
        return collect($dates)
            ->filter()
            ->map(fn ($date) => $date instanceof Carbon ? $date : Carbon::parse($date))
            ->sortBy(fn (Carbon $date): int => $date->timestamp)
            ->last();
    }

    private function earliestDate(array $dates): ?Carbon
    {
        return collect($dates)
            ->filter()
            ->map(fn ($date) => $date instanceof Carbon ? $date : Carbon::parse($date))
            ->sortBy(fn (Carbon $date): int => $date->timestamp)
            ->first();
    }

    private function periodText(?Carbon $start, ?Carbon $end): string
    {
        if (! $start || ! $end) {
            return '';
        }

        return $start->format('Y.m') . ' - ' . $end->format('Y.m');
    }

    private function percent(int $count, int $required): float
    {
        if ($required <= 0) {
            return 0.0;
        }

        return min(100.0, ($count / $required) * 100.0);
    }
}

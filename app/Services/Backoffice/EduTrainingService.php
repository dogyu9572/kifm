<?php

namespace App\Services\Backoffice;

use App\Models\EduTraining;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EduTrainingService
{
    public const BUNDLE_GRADE_PRICE_KEY = '_bundle';

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        $query = EduTraining::query()->with('rounds');
        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('year')) {
            $query->where('year', (int) $request->input('year'));
        }

        if ($request->filled('round_type')) {
            $roundType = (string) $request->input('round_type');
            $query->where(function (Builder $sub) use ($roundType) {
                $sub->where('round_type', $roundType)
                    ->orWhereHas('rounds', function (Builder $roundQuery) use ($roundType) {
                        $roundQuery->where('round_label', $roundType);
                    });
            });
        }

        if ($request->filled('training_method')) {
            $query->where('training_method', (string) $request->input('training_method'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));
            if ($keyword !== '') {
                $query->where('title', 'like', '%'.$keyword.'%');
            }
        }
    }

    public function create(array $validated): EduTraining
    {
        return DB::transaction(function () use ($validated) {
            $training = new EduTraining;
            $this->fillTraining($training, $validated);
            $training->save();
            $this->syncRounds($training, $validated);

            return $training->fresh(['rounds']);
        });
    }

    public function update(EduTraining $eduTraining, array $validated): void
    {
        DB::transaction(function () use ($eduTraining, $validated): void {
            $this->fillTraining($eduTraining, $validated);
            $eduTraining->save();
            $this->syncRounds($eduTraining, $validated);
        });
    }

    /**
     * @param  list<int>  $ids
     */
    public function deleteMany(array $ids): int
    {
        $trainings = EduTraining::query()->whereIn('id', $ids)->get();
        foreach ($trainings as $training) {
            $training->delete();
        }

        return $trainings->count();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function fillTraining(EduTraining $training, array $validated): void
    {
        $training->year = (int) $validated['year'];
        $training->season = (string) $validated['season'];
        $training->title = (string) $validated['title'];
        $training->use_round = (bool) ($validated['use_round'] ?? true);
        $training->status = (string) $validated['status'];
        $training->overview = isset($validated['overview']) ? (string) $validated['overview'] : null;
        $training->program = isset($validated['program']) ? (string) $validated['program'] : null;
        $training->registration_info = isset($validated['registration_info']) ? (string) $validated['registration_info'] : null;
        $training->introduction = isset($validated['introduction']) ? (string) $validated['introduction'] : null;

        $rounds = $this->normalizedRounds($validated);
        $training->round_type = $training->use_round
            ? ($validated['round_type'] ?? ($rounds[0]['round_label'] ?? null))
            : '전체';
        $training->training_method = $this->resolvedTrainingMethod($validated, $rounds);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return list<array<string, mixed>>
     */
    protected function normalizedRounds(array $validated): array
    {
        $rounds = $validated['rounds'] ?? [];
        if (! is_array($rounds)) {
            return [];
        }

        $normalized = [];
        foreach ($rounds as $index => $round) {
            if (! is_array($round)) {
                continue;
            }
            $normalized[] = [
                'round_order' => $index + 1,
                'round_label' => (string) ($round['round_label'] ?? ($index + 1).'차'),
                'training_method' => (string) ($round['training_method'] ?? 'offline'),
                'lecture_date' => $round['lecture_date'] ?? now()->toDateString(),
                'location_link' => (string) ($round['location_link'] ?? ''),
                'apply_start_date' => $round['apply_start_date'] ?: null,
                'apply_end_date' => $round['apply_end_date'] ?: null,
                'capacity' => ! empty($round['is_capacity_unlimited']) ? null : ($round['capacity'] ?: null),
                'is_capacity_unlimited' => (bool) ($round['is_capacity_unlimited'] ?? false),
                'duration_hours' => (float) ($round['duration_hours'] ?? 0),
                'score' => (float) ($round['score'] ?? 0),
                'grade_prices' => $this->compactGradePrices($round['grades'] ?? []),
                'status' => (string) ($round['status'] ?? 'PUBLIC'),
            ];
        }

        return $normalized;
    }

    /**
     * @param  list<array<string, mixed>>  $rounds
     * @return list<array<string, mixed>>
     */
    protected function roundsForStorage(EduTraining $training, array $validated, array $rounds): array
    {
        if ($rounds === []) {
            return [];
        }

        if (! $training->use_round) {
            $single = $rounds[0];
            $single['round_order'] = 1;
            $single['round_label'] = '전체';
            $single['training_method'] = (string) ($validated['training_method'] ?? $single['training_method'] ?? 'offline');

            return [$single];
        }

        $bundlePrices = $this->compactGradePrices($validated['bundle_grades'] ?? []);
        if ($this->hasEligibleGradePrice($bundlePrices)) {
            $rounds[0]['grade_prices'][self::BUNDLE_GRADE_PRICE_KEY] = $bundlePrices;
        }

        return $rounds;
    }

    /**
     * @param  array<string, mixed>  $grades
     * @return array<string, array{eligible: bool, price: float|null}>
     */
    protected function compactGradePrices(array $grades): array
    {
        $out = [];
        foreach (array_keys(self::registrationGradeLabels()) as $key) {
            $block = is_array($grades[$key] ?? null) ? $grades[$key] : [];
            $eligible = filter_var($block['eligible'] ?? false, FILTER_VALIDATE_BOOL);
            $priceRaw = $block['price'] ?? null;
            $price = ($priceRaw === '' || $priceRaw === null) ? null : (float) $priceRaw;
            if (! $eligible) {
                $price = null;
            }
            $out[$key] = [
                'eligible' => $eligible,
                'price' => $price,
            ];
        }

        return $out;
    }

    protected function hasEligibleGradePrice(array $gradePrices): bool
    {
        foreach ($gradePrices as $block) {
            if (
                is_array($block)
                && filter_var($block['eligible'] ?? false, FILTER_VALIDATE_BOOL)
                && ($block['price'] ?? null) !== null
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  list<array<string, mixed>>  $rounds
     */
    protected function resolvedTrainingMethod(array $validated, array $rounds): string
    {
        if (count($rounds) === 0) {
            return (string) ($validated['training_method'] ?? 'offline');
        }

        $methods = array_values(array_unique(array_map(
            static fn (array $row): string => (string) $row['training_method'],
            $rounds
        )));

        if (count($methods) === 1) {
            return $methods[0];
        }

        return 'hybrid';
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function syncRounds(EduTraining $training, array $validated): void
    {
        $rounds = $this->roundsForStorage($training, $validated, $this->normalizedRounds($validated));

        $training->rounds()->delete();
        foreach ($rounds as $roundData) {
            $training->rounds()->create($roundData);
        }
    }

    public function replaceTextbookFile(EduTraining $training, string $tmpPath): void
    {
        if ($training->textbook_file_path && Storage::disk('public')->exists($training->textbook_file_path)) {
            Storage::disk('public')->delete($training->textbook_file_path);
        }

        $training->textbook_file_path = $tmpPath;
        $training->save();
    }

    /**
     * @return array<string, string>
     */
    public static function seasonLabels(): array
    {
        return [
            'spring' => '춘계',
            'summer' => '하계',
            'fall' => '추계',
            'winter' => '동계',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roundTypeLabels(): array
    {
        return [
            '1차' => '1차',
            '2차' => '2차',
            '3차' => '3차',
            '심화과정' => '심화과정',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function methodLabels(): array
    {
        return [
            'offline' => '오프라인',
            'online' => '온라인',
            'hybrid' => '온/오프라인 병행',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'PUBLIC' => '공개',
            'PRIVATE' => '비공개',
        ];
    }

    /**
     * 연수 등록비(프로토타입 등급 구분)
     *
     * @return array<string, string>
     */
    public static function registrationGradeLabels(): array
    {
        return [
            'guest' => '비회원',
            'associate' => '준회원',
            'regular' => '정회원',
            'lifetime' => '평생회원',
            'senior' => '시니어회원',
        ];
    }
}

<?php

namespace App\Services\Frontend;

use App\Models\AcademicEvent;
use App\Models\AnnualSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicAcademicEventService
{
    public const FALLBACK_HEAD_IMAGE = 'images/img_sample_conference_top.jpg';

    /** @return array<string, string> */
    public function statusLabels(): array
    {
        return [
            'all' => '전체보기',
            'upcoming' => '모집 예정',
            'ongoing' => '모집 중',
            'closed' => '신청마감',
        ];
    }

    public function featuredConference(): ?AcademicEvent
    {
        return AcademicEvent::query()
            ->where('is_public', 'Y')
            ->where('main_exposure', 'Y')
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->first();
    }

    public function paginateConferences(Request $request, int $perPage = 6): LengthAwarePaginator
    {
        $query = AcademicEvent::query()->where('is_public', 'Y');
        $this->applyFilters($query, $request);

        return $query
            ->orderByRaw(
                'CASE WHEN pre_reg_start IS NOT NULL AND pre_reg_end IS NOT NULL AND DATE(pre_reg_start) <= ? AND DATE(pre_reg_end) >= ? THEN 0 ELSE 1 END',
                [Carbon::today()->toDateString(), Carbon::today()->toDateString()]
            )
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return list<int> */
    public function yearOptions(): array
    {
        return AcademicEvent::query()
            ->where('is_public', 'Y')
            ->select('year')
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(static fn ($year): int => (int) $year)
            ->all();
    }

    /**
     * @return list<array{start: string, end: string, class: string, type: string, title: string, url: string|null}>
     */
    public function annualCalendarSchedules(): array
    {
        return AnnualSchedule::query()
            ->where('is_visible', true)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get()
            ->map(function (AnnualSchedule $schedule): array {
                $start = $schedule->start_date instanceof Carbon
                    ? $schedule->start_date
                    : Carbon::parse($schedule->start_date);
                $end = $schedule->end_date instanceof Carbon
                    ? $schedule->end_date
                    : Carbon::parse($schedule->end_date ?: $schedule->start_date);
                $title = trim((string) $schedule->title);
                $type = (string) ($schedule->schedule_type ?? '');
                if (! in_array($type, ['academic_conference', 'training_course'], true)) {
                    $type = $this->annualScheduleTypeFromTitle($title);
                }

                return [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                    'class' => $type === 'training_course' ? 'c2' : 'c1',
                    'type' => $type,
                    'title' => $title,
                    'url' => $schedule->link_url ?: null,
                ];
            })
            ->values()
            ->all();
    }

    /** @return array<string, string> */
    public function filters(Request $request): array
    {
        $status = (string) $request->query('status', 'all');
        if (! array_key_exists($status, $this->statusLabels())) {
            $status = 'all';
        }

        return [
            'status' => $status,
            'year' => (string) $request->query('year', ''),
            'keyword' => trim((string) $request->query('keyword', '')),
        ];
    }

    public function eventUrl(AcademicEvent $event, ?string $path = null): string
    {
        $base = url('/academic_conference/' . $event->folder_name);

        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }

    public function registrationUrl(AcademicEvent $event): string
    {
        return $this->eventUrl($event, 'registration');
    }

    public function imageUrl(?string $path, string $fallback = self::FALLBACK_HEAD_IMAGE): string
    {
        if ($path === null || $path === '') {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function headlineText(AcademicEvent $event): string
    {
        $text = trim(strip_tags((string) ($event->event_material_description ?: $event->greeting_title ?: '')));

        return $text !== '' ? Str::limit($text, 120) : $event->title;
    }

    public function eventDateText(AcademicEvent $event): string
    {
        if (! $event->start_at) {
            return '-';
        }
        if ($event->end_at && ! $event->start_at->isSameDay($event->end_at)) {
            return $this->dateText($event->start_at) . ' ~ ' . $this->dateText($event->end_at);
        }

        return $this->dateText($event->start_at);
    }

    public function preRegistrationText(AcademicEvent $event): string
    {
        if (! $event->pre_reg_start || ! $event->pre_reg_end) {
            return '-';
        }

        return $this->dateText($event->pre_reg_start) . ' ~ ' . $this->dateText($event->pre_reg_end);
    }

    /** @return array{code: string, label: string, class: string} */
    public function status(AcademicEvent $event): array
    {
        $today = Carbon::today();

        if ($event->pre_reg_start && $today->lt($event->pre_reg_start)) {
            return ['code' => 'upcoming', 'label' => '모집예정', 'class' => 'expected'];
        }
        if ($event->pre_reg_start && $event->pre_reg_end && $today->betweenIncluded($event->pre_reg_start, $event->pre_reg_end)) {
            return ['code' => 'ongoing', 'label' => '모집 중', 'class' => 'ing'];
        }

        return ['code' => 'closed', 'label' => '신청마감', 'class' => 'end'];
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $filters = $this->filters($request);

        if ($filters['year'] !== '') {
            $query->where('year', (int) $filters['year']);
        }
        if ($filters['keyword'] !== '') {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%');
        }

        $this->applyStatusFilter($query, $filters['status']);
    }

    private function applyStatusFilter(Builder $query, string $status): void
    {
        $today = Carbon::today()->toDateString();

        if ($status === 'upcoming') {
            $query->whereNotNull('pre_reg_start')->whereDate('pre_reg_start', '>', $today);
            return;
        }
        if ($status === 'ongoing') {
            $query->whereNotNull('pre_reg_start')
                ->whereNotNull('pre_reg_end')
                ->whereDate('pre_reg_start', '<=', $today)
                ->whereDate('pre_reg_end', '>=', $today);
            return;
        }
        if ($status === 'closed') {
            $query->where(function (Builder $q) use ($today): void {
                $q->whereNull('pre_reg_start')
                    ->orWhere(function (Builder $inner) use ($today): void {
                        $inner->whereDate('pre_reg_start', '<=', $today)
                            ->where(function (Builder $dateQuery) use ($today): void {
                                $dateQuery->whereNull('pre_reg_end')->orWhereDate('pre_reg_end', '<', $today);
                            });
                    });
            });
        }
    }

    private function dateText(Carbon $date): string
    {
        $weekdays = ['일', '월', '화', '수', '목', '금', '토'];

        return $date->format('Y년 n월 j일') . ' (' . $weekdays[(int) $date->dayOfWeek] . ')';
    }

    private function annualScheduleTypeFromTitle(string $title): string
    {
        return preg_match('/(연수|강좌|교육)/u', $title) === 1 ? 'training_course' : 'academic_conference';
    }
}

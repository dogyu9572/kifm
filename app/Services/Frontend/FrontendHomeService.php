<?php

namespace App\Services\Frontend;

use App\Models\AcademicEvent;
use App\Models\Banner;
use App\Models\CertifiedMember;
use App\Models\EduTrainingRound;
use App\Models\MemberExecutive;
use App\Models\Popup;
use App\Models\User;
use App\Services\Backoffice\MemberService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class FrontendHomeService
{
    public function __construct(
        private readonly MypageCertificationSummaryService $certificationSummaryService,
        private readonly PublicOnlineAcademyService $onlineAcademyService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function viewData(?User $user): array
    {
        return [
            'page_type' => 'professional',
            'gNum' => 'main',
            'gName' => '대한기능의학회 메인',
            'sName' => '',
            'popups' => $this->activePopups(),
            'banners' => $this->activeBanners(),
            'noticePosts' => $this->latestBoardPosts('member_square_notices', 4),
            'journalPosts' => $this->latestAcademicJournalPosts(4),
            'memberArchivePost' => $this->latestMemberArchivePosts(1)->first(),
            'academyCourse' => $this->onlineAcademyService->featuredCourses(1)->first(),
            'eventScheduleItems' => $this->eventScheduleItems(),
            'calendarSchedules' => $this->calendarSchedules(),
            'memberCard' => $this->memberCard($user),
        ];
    }

    public function bannerImageUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return asset('images/img_main_visual_01.jpg');
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function storageImageUrl(?string $path, string $fallback): string
    {
        if ($path === null || $path === '') {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    private function activeBanners(): Collection
    {
        return Banner::active()->inPeriod()->ordered()->get();
    }

    private function activePopups(): Collection
    {
        return Popup::query()
            ->select('id', 'title', 'popup_type', 'popup_display_type', 'popup_image', 'popup_content', 'url', 'url_target', 'width', 'height', 'position_top', 'position_left')
            ->forMenuScope(Popup::MENU_SCOPE_SITE)
            ->active()
            ->inPeriod()
            ->ordered()
            ->get()
            ->filter(static fn (Popup $popup): bool => ! Popup::isHiddenByTodayCookie((int) $popup->id));
    }

    private function latestBoardPosts(string $slug, int $limit): Collection
    {
        $table = 'board_'.$slug;
        if (! Schema::hasTable($table)) {
            return collect();
        }

        return DB::table($table)
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false)
            ->orderBy('is_notice', 'desc')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    private function latestMemberArchivePosts(int $limit, ?string $archiveType = null): Collection
    {
        if (! Schema::hasTable('board_member_archive')) {
            return collect();
        }

        $query = DB::table('board_member_archive')
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false);

        if ($archiveType !== null) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.\"archive_type\"')) = ?", [$archiveType]);
        }

        return $query
            ->orderBy('is_notice', 'desc')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    private function latestAcademicJournalPosts(int $limit): Collection
    {
        return $this->latestBoardPosts('academic_journals', $limit);
    }

    private function eventScheduleItems(): Collection
    {
        return AcademicEvent::query()
            ->where('is_public', 'Y')
            ->whereNotNull('start_at')
            ->orderBy('start_at')
            ->get()
            ->map(fn (AcademicEvent $event): array => $this->academicEventScheduleItem($event))
            ->values();
    }

    private function academicEventScheduleItem(AcademicEvent $event): array
    {
        $start = $event->start_at instanceof Carbon ? $event->start_at : Carbon::parse($event->start_at);
        $end = $event->end_at ? ($event->end_at instanceof Carbon ? $event->end_at : Carbon::parse($event->end_at)) : null;

        return [
            'title' => $event->title,
            'url' => url('/academic_conference/'.$event->folder_name),
            'start_date' => $start->toDateString(),
            'end_date' => $end?->toDateString(),
            'sort_date' => $start->toDateString(),
            'day' => $start->format('d'),
            'weekday' => $this->weekday($start),
            'date_text' => $this->periodText($start, $end),
            'type_label' => '학술대회',
            'type_class' => 't2',
        ];
    }

    private function calendarSchedules(): array
    {
        $academicEvents = AcademicEvent::query()
            ->where('is_public', 'Y')
            ->whereNotNull('start_at')
            ->orderBy('start_at')
            ->orderBy('id')
            ->get()
            ->map(fn (AcademicEvent $event): array => $this->academicEventCalendarSchedule($event));

        $trainingRounds = EduTrainingRound::query()
            ->with('training:id,title,status')
            ->where('status', 'PUBLIC')
            ->whereNotNull('lecture_date')
            ->whereHas('training', static fn ($query) => $query->where('status', 'PUBLIC'))
            ->orderBy('lecture_date')
            ->orderBy('id')
            ->get()
            ->map(fn (EduTrainingRound $round): array => $this->trainingRoundCalendarSchedule($round));

        return $academicEvents
            ->merge($trainingRounds)
            ->filter(static fn (array $schedule): bool => ! empty($schedule['start']))
            ->sortBy([['start', 'asc'], ['id', 'asc']])
            ->values()
            ->all();
    }

    private function academicEventCalendarSchedule(AcademicEvent $event): array
    {
        $start = $event->start_at instanceof Carbon ? $event->start_at : Carbon::parse($event->start_at);
        $end = $event->end_at ? ($event->end_at instanceof Carbon ? $event->end_at : Carbon::parse($event->end_at)) : $start;

        return [
            'id' => 'academic-event-'.$event->id,
            'title' => $event->title,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'content' => '학술대회',
        ];
    }

    private function trainingRoundCalendarSchedule(EduTrainingRound $round): array
    {
        $lectureDate = $round->lecture_date instanceof Carbon ? $round->lecture_date : Carbon::parse($round->lecture_date);
        $roundLabel = trim((string) $round->round_label);
        $title = trim((string) ($round->training?->title ?? '연수교육'));

        if ($roundLabel !== '') {
            $title .= ' '.$roundLabel;
        }

        return [
            'id' => 'training-round-'.$round->id,
            'title' => $title,
            'start' => $lectureDate->toDateString(),
            'end' => $lectureDate->toDateString(),
            'content' => '연수교육',
        ];
    }

    private function memberCard(?User $user): array
    {
        if ($user === null) {
            return ['is_logged_in' => false];
        }

        $labels = MemberService::memberLevelLabels();
        $memberLevelLabel = $labels[$user->member_level ?? ''] ?? (string) ($user->member_level ?? '회원');
        $memberTypeLabels = [$memberLevelLabel];

        if ((bool) ($user->certified_instructor ?? false)) {
            $memberTypeLabels[] = '인정의';
        }
        if ($this->hasActiveExecutiveRole($user)) {
            $memberTypeLabels[] = '임원';
        }

        $certifiedMember = CertifiedMember::query()
            ->where('member_id', $user->id)
            ->orderByDesc('validity_end_date')
            ->first();

        return [
            'is_logged_in' => true,
            'name' => $user->name ?: $user->login_id ?: '회원',
            'member_level_labels' => array_values(array_unique(array_filter($memberTypeLabels))),
            'certification' => $this->certificationSummaryService->summarize($user),
            'certification_period' => $certifiedMember
                ? $this->periodText($certifiedMember->validity_start_date, $certifiedMember->validity_end_date)
                : null,
        ];
    }

    private function hasActiveExecutiveRole(User $user): bool
    {
        return MemberExecutive::query()
            ->where('member_id', $user->id)
            ->where('is_active', true)
            ->where(function ($query): void {
                $today = Carbon::today()->toDateString();
                $query->where('is_indefinite', true)
                    ->orWhereNull('term_end_date')
                    ->orWhereDate('term_end_date', '>=', $today);
            })
            ->exists();
    }

    private function periodText(?Carbon $start, ?Carbon $end): string
    {
        if (! $start) {
            return '';
        }
        if ($end && ! $start->isSameDay($end)) {
            return $start->format('Y.m.d').' ~ '.$end->format('Y.m.d');
        }

        return $start->format('Y.m.d');
    }

    private function weekday(Carbon $date): string
    {
        return ['일', '월', '화', '수', '목', '금', '토'][(int) $date->dayOfWeek];
    }
}

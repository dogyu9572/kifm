<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEvent;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicEventService
{
    /** @return array<string, string> */
    public static function seasonLabels(): array
    {
        return [
            'spring' => '춘계',
            'summer' => '하계',
            'fall' => '추계',
            'winter' => '동계',
        ];
    }

    /** @return array<string, string> */
    public static function preRegStatusLabels(): array
    {
        return [
            'upcoming' => '진행 예정',
            'ongoing' => '모집 중',
            'closed' => '신청 마감',
        ];
    }

    /** @return array<string, string> */
    public static function presentationTypeLabels(): array
    {
        return [
            'oral' => '구연 발표',
            'poster' => '포스터 발표',
            'video' => '비디오 발표',
            'designated_discussion' => '지정 토론',
        ];
    }

    /** @return array<string, string> */
    public static function sponsorLevelLabels(): array
    {
        return [
            'vip' => 'VIP',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'exhibitors' => 'Exhibitors',
        ];
    }

    /** @return array<string, string> */
    public static function mainPlacementLabels(): array
    {
        return self::sponsorLevelLabels();
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $query = AcademicEvent::query()->withCount('sessions');
        $this->applyFilters($query, $request);

        return $query->orderByDesc('year')->orderByDesc('id')->paginate($perPage)->withQueryString();
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('year')) {
            $query->where('year', (int) $request->input('year'));
        }
        if ($request->filled('season')) {
            $query->where('season', $request->string('season')->toString());
        }
        if ($request->filled('keyword')) {
            $kw = '%' . trim($request->string('keyword')->toString()) . '%';
            $query->where('title', 'like', $kw);
        }
        if ($request->filled('pre_reg_status')) {
            $this->applyPreRegStatusFilter($query, $request->string('pre_reg_status')->toString());
        }
    }

    protected function applyPreRegStatusFilter(Builder $query, string $status): void
    {
        $today = Carbon::today()->toDateString();
        if ($status === 'upcoming') {
            $query->whereNotNull('pre_reg_start')
                ->whereDate('pre_reg_start', '>', $today);

            return;
        }
        if ($status === 'ongoing') {
            $query->whereNotNull('pre_reg_start')->whereNotNull('pre_reg_end')
                ->whereDate('pre_reg_start', '<=', $today)
                ->whereDate('pre_reg_end', '>=', $today);

            return;
        }
        if ($status === 'closed') {
            $query->whereNotNull('pre_reg_end')
                ->whereDate('pre_reg_end', '<', $today);
        }
    }

    public function create(array $validated): AcademicEvent
    {
        return DB::transaction(function () use ($validated) {
            $event = new AcademicEvent();
            $this->fillEventAttributes($event, $validated);
            $event->save();
            $this->syncChildData($event, $validated);

            return $event->fresh([
                'venueFloors',
                'fields',
                'sponsors',
                'mainSponsorSlots',
                'speakers',
            ]);
        });
    }

    public function update(AcademicEvent $event, array $validated): AcademicEvent
    {
        return DB::transaction(function () use ($event, $validated) {
            $this->fillEventAttributes($event, $validated);
            $event->save();
            $this->syncChildData($event, $validated);

            return $event->fresh([
                'venueFloors',
                'fields',
                'sponsors',
                'mainSponsorSlots',
                'speakers',
            ]);
        });
    }

    public function deleteMany(array $ids): int
    {
        return AcademicEvent::destroy($ids);
    }

    /**
     * 사전등록 진행 상태 코드: upcoming | ongoing | closed | null(미설정)
     */
    public function computePreRegStatus(?AcademicEvent $event): ?string
    {
        if (! $event || ! $event->pre_reg_start || ! $event->pre_reg_end) {
            return null;
        }
        $today = Carbon::today();
        if ($today->lt($event->pre_reg_start)) {
            return 'upcoming';
        }
        if ($today->lte($event->pre_reg_end)) {
            return 'ongoing';
        }

        return 'closed';
    }

    /**
     * 초록 접수 상태: null(미설정) | pending | ongoing | closed
     */
    public function computeAbstractRegStatus(?AcademicEvent $event): ?string
    {
        if (! $event || ! $event->abstract_start || ! $event->abstract_end) {
            return null;
        }
        $today = Carbon::today();
        if ($today->lt($event->abstract_start)) {
            return 'pending';
        }
        if ($today->lte($event->abstract_end)) {
            return 'ongoing';
        }

        return 'closed';
    }

    protected function fillEventAttributes(AcademicEvent $event, array $v): void
    {
        $keys = [
            'legacy_post_id', 'year', 'season', 'folder_name', 'title',
            'event_material_path', 'event_material_description',
            'event_type', 'online_url', 'is_public', 'main_exposure', 'venue',
            'start_at', 'end_at', 'start_time_omit', 'end_time_omit',
            'greeting_title', 'greeting_content', 'greeting_image_path',
            'committee_content', 'pc_banner_path', 'thumbnail_path',
            'address', 'address_detail', 'address_lat', 'address_lng',
            'walking_guide', 'shuttle_guide',
            'pre_reg_start', 'pre_reg_end', 'onsite_reg_start', 'onsite_reg_end',
            'onsite_reg_allowed', 'pre_reg_guide', 'reg_fee_guide', 'cert_doc_guide', 'reg_info_guide',
            'abstract_start', 'abstract_end', 'abstract_revision_end', 'abstract_judging_end',
            'abstract_result_date', 'abstract_book_path', 'presentation_types',
            'submission_guide', 'abstract_notes',
        ];
        foreach ($keys as $key) {
            if (array_key_exists($key, $v)) {
                $event->{$key} = $v[$key];
            }
        }
    }

    protected function syncChildData(AcademicEvent $event, array $v): void
    {
        $event->venueFloors()->delete();
        foreach ($v['venue_floors'] ?? [] as $i => $row) {
            $name = trim((string) ($row['floor_name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $event->venueFloors()->create([
                'floor_name' => $name,
                'file_path' => $row['file_path'] ?? null,
                'sort_order' => (int) ($row['sort_order'] ?? $i),
            ]);
        }

        $event->fields()->delete();
        foreach ($v['abstract_fields'] ?? [] as $i => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $event->fields()->create([
                'name' => $name,
                'sort_order' => (int) ($row['sort_order'] ?? $i),
            ]);
        }

        $event->mainSponsorSlots()->delete();
        $event->sponsors()->delete();

        $sponsorIdsInOrder = [];
        foreach ($v['sponsors'] ?? [] as $i => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                $sponsorIdsInOrder[] = null;

                continue;
            }
            $s = $event->sponsors()->create([
                'academic_sponsor_master_id' => ! empty($row['academic_sponsor_master_id']) ? (int) $row['academic_sponsor_master_id'] : null,
                'name' => $name,
                'logo_path' => $row['logo_path'] ?? null,
                'level' => $row['level'] ?? 'exhibitors',
                'sort_order' => (int) ($row['sort_order'] ?? $i),
            ]);
            $sponsorIdsInOrder[] = $s->id;
        }

        foreach ($v['main_sponsor_slots'] ?? [] as $i => $row) {
            $activeRaw = $row['active'] ?? '1';
            $isActive = $activeRaw === '1' || $activeRaw === 1 || $activeRaw === true;
            if (! $isActive) {
                continue;
            }
            $placement = (string) ($row['placement'] ?? '');
            if ($placement === '' || ! isset(self::mainPlacementLabels()[$placement])) {
                continue;
            }
            $idx = isset($row['sponsor_index']) ? (int) $row['sponsor_index'] : -1;
            $sponsorId = $idx >= 0 && $idx < count($sponsorIdsInOrder) ? $sponsorIdsInOrder[$idx] : null;
            if (! $sponsorId) {
                continue;
            }
            $event->mainSponsorSlots()->create([
                'academic_event_sponsor_id' => $sponsorId,
                'placement' => $placement,
                'sort_order' => (int) ($row['sort_order'] ?? $i),
            ]);
        }

        $event->speakers()->delete();
        foreach ($v['speakers'] ?? [] as $i => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $event->speakers()->create([
                'source' => $row['source'] ?? 'manual',
                'member_id' => ! empty($row['member_id']) ? (int) $row['member_id'] : null,
                'academic_event_abstract_id' => ! empty($row['academic_event_abstract_id']) ? (int) $row['academic_event_abstract_id'] : null,
                'name' => $name,
                'affiliation' => $row['affiliation'] ?? null,
                'position' => $row['position'] ?? null,
                'image_path' => $row['image_path'] ?? null,
                'abstract_title' => $row['abstract_title'] ?? null,
                'bio' => $row['bio'] ?? null,
                'sort_order' => (int) ($row['sort_order'] ?? $i),
            ]);
        }
    }
}

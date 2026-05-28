<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\AcademicEventRequest;
use App\Models\AcademicEvent;
use App\Models\User;
use App\Services\Backoffice\AcademicEventAbstractService;
use App\Services\Backoffice\AcademicEventService;
use App\Support\BackofficeFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AcademicEventController extends Controller
{
    public function __construct(
        protected AcademicEventService $academicEventService,
        protected AcademicEventAbstractService $academicEventAbstractService
    ) {}

    public function index(Request $request)
    {
        $events = $this->academicEventService->paginateFiltered($request);
        $svc = $this->academicEventService;
        $events->getCollection()->each(function (AcademicEvent $e) use ($svc) {
            $e->setAttribute('list_pre_reg', $svc->computePreRegStatus($e));
            $e->setAttribute('list_abs', $svc->computeAbstractRegStatus($e));
        });

        return view('backoffice.academic-events.index', [
            'events' => $events,
            'perPage' => $events->perPage(),
            'seasonLabels' => AcademicEventService::seasonLabels(),
            'preRegStatusLabels' => AcademicEventService::preRegStatusLabels(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function create(Request $request)
    {
        return view('backoffice.academic-events.create', [
            'event' => new AcademicEvent([
                'year' => now()->year,
                'season' => null,
                'event_type' => 'offline',
                'is_public' => 'Y',
                'main_exposure' => 'N',
                'onsite_reg_allowed' => 'allowed',
                'start_time_omit' => false,
                'end_time_omit' => false,
            ]),
            'seasonLabels' => AcademicEventService::seasonLabels(),
            'presentationTypeLabels' => AcademicEventService::presentationTypeLabels(),
            'sponsorLevelLabels' => AcademicEventService::sponsorLevelLabels(),
            'mainPlacementLabels' => AcademicEventService::mainPlacementLabels(),
            'yearOptions' => $this->yearOptions(),
            'cancelUrl' => $this->cancelUrl($request),
        ]);
    }

    public function store(AcademicEventRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['presentation_types'] = array_values($validated['presentation_types'] ?? []);
        $event = $this->academicEventService->create($validated);
        $this->syncUploadedFiles($request, $event);

        return redirect()->route('backoffice.academic-events.index')
            ->with('success', '학술행사가 등록되었습니다.');
    }

    public function edit(Request $request, AcademicEvent $academic_event)
    {
        $academic_event->load([
            'venueFloors',
            'fields',
            'sponsors',
            'mainSponsorSlots.sponsor',
            'speakers',
            'sessions',
        ]);

        return view('backoffice.academic-events.edit', [
            'event' => $academic_event,
            'seasonLabels' => AcademicEventService::seasonLabels(),
            'presentationTypeLabels' => AcademicEventService::presentationTypeLabels(),
            'sponsorLevelLabels' => AcademicEventService::sponsorLevelLabels(),
            'mainPlacementLabels' => AcademicEventService::mainPlacementLabels(),
            'yearOptions' => $this->yearOptions(),
            'cancelUrl' => $this->cancelUrl($request),
        ]);
    }

    public function update(AcademicEventRequest $request, AcademicEvent $academic_event): RedirectResponse
    {
        $validated = $request->validated();
        $validated['presentation_types'] = array_values($validated['presentation_types'] ?? []);
        $this->academicEventService->update($academic_event, $validated);
        $fresh = AcademicEvent::query()->findOrFail($academic_event->id);
        $this->syncUploadedFiles($request, $fresh);

        $activeTab = (string) $request->input('active_tab', 'base');
        if (! in_array($activeTab, ['base', 'prereg', 'abstract', 'speakers', 'sponsors', 'sessions'], true)) {
            $activeTab = 'base';
        }

        return redirect()->route('backoffice.academic-events.edit', [
            'academic_event' => $academic_event,
            'tab' => $activeTab,
            'return_url' => $request->input('return_url', $request->query('return_url')),
        ])
            ->with('success', '학술행사가 수정되었습니다.');
    }

    public function destroy(Request $request, AcademicEvent $academic_event): RedirectResponse
    {
        if ((string) $request->input('confirm_delete') !== '1') {
            Log::warning('Blocked academic event delete without explicit confirmation.', [
                'event_id' => $academic_event->id,
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', '삭제 확인 값이 없어 학술행사 삭제를 중단했습니다.');
        }

        Log::warning('Academic event delete requested.', [
            'event_id' => $academic_event->id,
            'title' => $academic_event->title,
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);
        $this->deleteEventFiles($academic_event);
        $academic_event->delete();

        return redirect()->route('backoffice.academic-events.index')
            ->with('success', '학술행사가 삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        if ((string) $request->input('confirm_delete') !== '1') {
            Log::warning('Blocked bulk academic event delete without explicit confirmation.', [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '삭제 확인 값이 없어 학술행사 삭제를 중단했습니다.',
            ], 422);
        }

        $validated = $request->validate([
            'event_ids' => ['required', 'array'],
            'event_ids.*' => ['integer', 'exists:academic_events,id'],
        ]);

        $events = AcademicEvent::query()->findMany($validated['event_ids']);
        Log::warning('Bulk academic event delete requested.', [
            'event_ids' => $events->pluck('id')->all(),
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);
        foreach ($events as $event) {
            $this->deleteEventFiles($event);
        }
        $deleted = $this->academicEventService->deleteMany($validated['event_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건 삭제되었습니다.',
            'deleted_count' => $deleted,
            'skipped_ids' => [],
        ]);
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $field = (string) $request->get('search_field', 'all');
        $members = User::query()
            ->select(['id', 'name', 'email', 'workplace_name', 'position', 'login_id', 'phone_number'])
            ->notWithdrawn()
            ->when($keyword !== '', function ($query) use ($keyword, $field) {
                $query->where(function ($q) use ($keyword, $field) {
                    $like = '%' . $keyword . '%';
                    if ($field === 'name') {
                        $q->where('name', 'like', $like);

                        return;
                    }
                    if ($field === 'email') {
                        $q->where('email', 'like', $like);

                        return;
                    }
                    if ($field === 'id') {
                        $q->where('login_id', 'like', $like);

                        return;
                    }
                    if ($field === 'phone') {
                        $q->where('phone_number', 'like', $like);

                        return;
                    }
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('login_id', 'like', $like)
                        ->orWhere('workplace_name', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'data' => $members->getCollection()->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'organization' => $member->workplace_name,
                'position' => $member->position,
                'login_id' => $member->login_id,
                'phone_number' => $member->phone_number,
            ]),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'total' => $members->total(),
                'per_page' => $members->perPage(),
            ],
        ]);
    }

    /**
     * 학술행사 연자 등록용: 해당 행사의 초록 목록(초록 관리 목록과 동일 필터) JSON
     */
    public function searchAbstracts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_event_id' => ['required', 'integer', 'exists:academic_events,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'in:10,20,50,100'],
            'presentation_type' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'string', 'max:30'],
            'search_keyword' => ['nullable', 'string', 'max:200'],
        ]);

        $inner = Request::create('/', 'GET', [
            'academic_event_id' => (string) $validated['academic_event_id'],
            'presentation_type' => (string) $request->query('presentation_type', 'all'),
            'status' => (string) $request->query('status', 'all'),
            'search_keyword' => (string) $request->query('search_keyword', ''),
            'per_page' => (string) $request->query('per_page', '10'),
            'page' => (string) $request->query('page', '1'),
        ]);

        $paginator = $this->academicEventAbstractService->paginateIndex($inner);

        $ptLabels = AcademicEventAbstractService::presentationTypeLabels();
        $stLabels = AcademicEventAbstractService::statusLabels();
        $regLabels = AcademicEventAbstractService::registeredByListLabels();
        $fileLabels = AcademicEventAbstractService::fileReceiptLabels();

        return response()->json([
            'data' => $paginator->getCollection()->map(function ($a) use ($ptLabels, $stLabels, $regLabels, $fileLabels) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'author_name' => $a->author_name,
                    'presentation_type' => $a->presentation_type,
                    'presentation_type_label' => $ptLabels[$a->presentation_type] ?? $a->presentation_type,
                    'status' => $a->status,
                    'status_label' => $stLabels[$a->status] ?? $a->status,
                    'registered_by' => $a->registered_by,
                    'registered_by_label' => $regLabels[$a->registered_by] ?? $a->registered_by,
                    'file_receipt_status' => $a->file_receipt_status,
                    'file_receipt_label' => $fileLabels[$a->file_receipt_status] ?? $a->file_receipt_status,
                    'submitted_at' => $a->submitted_at?->format('Y-m-d H:i'),
                    'member_id' => $a->member_id,
                ];
            }),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    protected function syncUploadedFiles(Request $request, AcademicEvent $event): void
    {
        $disk = 'public';
        $dirty = false;

        foreach ([
            'greeting_image' => 'greeting_image_path',
            'pc_banner' => 'pc_banner_path',
            'thumbnail' => 'thumbnail_path',
            'exhibition_image' => 'exhibition_image_path',
            'event_material' => 'event_material_path',
            'abstract_book' => 'abstract_book_path',
        ] as $input => $col) {
            $delKey = 'delete_' . str_replace('_path', '', $col);
            if ($request->boolean($delKey) && $event->{$col}) {
                Storage::disk($disk)->delete($event->{$col});
                $event->{$col} = null;
                $dirty = true;
            }
            if ($request->hasFile($input)) {
                if ($event->{$col}) {
                    Storage::disk($disk)->delete($event->{$col});
                }
                $dir = 'backoffice/academic-events/' . str_replace('_path', '', $col);
                $event->{$col} = BackofficeFile::storeWithOriginalName($request->file($input), $dir, $disk);
                $dirty = true;
            }
        }

        if ($request->hasFile('venue_floor_files')) {
            $files = $request->file('venue_floor_files', []);
            $floors = $event->venueFloors()->orderBy('sort_order')->get();
            foreach ($files as $idx => $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                $floor = $floors->get($idx);
                if (! $floor) {
                    continue;
                }
                if ($floor->file_path) {
                    Storage::disk($disk)->delete($floor->file_path);
                }
                $floor->file_path = BackofficeFile::storeWithOriginalName($file, 'backoffice/academic-events/venue-floors', $disk);
                $floor->save();
            }
            $dirty = true;
        }

        if ($request->hasFile('speaker_images')) {
            $files = $request->file('speaker_images', []);
            $speakers = $event->speakers()->orderBy('sort_order')->get();
            foreach ($files as $idx => $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                $sp = $speakers->firstWhere('sort_order', ((int) $idx) + 1) ?: $speakers->get((int) $idx);
                if (! $sp) {
                    continue;
                }
                if ($sp->image_path) {
                    Storage::disk($disk)->delete($sp->image_path);
                }
                $sp->image_path = BackofficeFile::storeWithOriginalName($file, 'backoffice/academic-events/speakers', $disk);
                $sp->save();
            }
            $dirty = true;
        }

        if ($request->hasFile('sponsor_logos')) {
            $files = $request->file('sponsor_logos', []);
            $sponsors = $event->sponsors()->orderBy('sort_order')->get();
            foreach ($files as $idx => $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                $sp = $sponsors->firstWhere('sort_order', ((int) $idx) + 1) ?: $sponsors->get((int) $idx);
                if (! $sp) {
                    continue;
                }
                if ($sp->logo_path) {
                    Storage::disk($disk)->delete($sp->logo_path);
                }
                $sp->logo_path = BackofficeFile::storeWithOriginalName($file, 'backoffice/academic-events/sponsors', $disk);
                $sp->save();
            }
            $dirty = true;
        }

        if ($dirty) {
            $event->save();
        }
    }

    protected function deleteEventFiles(AcademicEvent $event): void
    {
        $disk = 'public';
        foreach ([
            'greeting_image_path',
            'pc_banner_path',
            'thumbnail_path',
            'exhibition_image_path',
            'event_material_path',
            'abstract_book_path',
        ] as $col) {
            if ($event->{$col}) {
                Storage::disk($disk)->delete($event->{$col});
            }
        }
        $event->load(['venueFloors', 'speakers', 'sponsors']);
        foreach ($event->venueFloors as $f) {
            if ($f->file_path) {
                Storage::disk($disk)->delete($f->file_path);
            }
        }
        foreach ($event->speakers as $s) {
            if ($s->image_path) {
                Storage::disk($disk)->delete($s->image_path);
            }
        }
        foreach ($event->sponsors as $s) {
            if ($s->logo_path) {
                Storage::disk($disk)->delete($s->logo_path);
            }
        }
    }

    /** @return list<int> */
    protected function yearOptions(): array
    {
        $base = (int) now()->format('Y');
        $years = [];
        for ($y = $base; $y >= 2010; $y--) {
            $years[] = $y;
        }

        return $years;
    }

    protected function cancelUrl(Request $request): string
    {
        $fallback = route('backoffice.academic-events.index');
        $raw = $request->query('return_url');
        if (! is_string($raw) || $raw === '') {
            return $fallback;
        }
        $decoded = urldecode($raw);
        if (str_starts_with($decoded, '/backoffice/') && ! str_starts_with($decoded, '//')) {
            return $decoded;
        }
        $parts = parse_url($decoded);
        if (! empty($parts['path']) && str_starts_with($parts['path'], '/backoffice/')) {
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';

            return $parts['path'] . $query;
        }

        return $fallback;
    }
}

<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\StoreAcademicEventAbstractRequest;
use App\Http\Requests\Backoffice\UpdateAcademicEventAbstractRequest;
use App\Models\AcademicEvent;
use App\Models\AcademicEventAbstract;
use App\Models\User;
use App\Services\Backoffice\AcademicEventAbstractService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AcademicEventAbstractController extends Controller
{
    public function __construct(
        protected AcademicEventAbstractService $abstractService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $abstracts = $this->abstractService->paginateIndex($request);

        return view('backoffice.academic-event-abstracts.index', [
            'abstracts' => $abstracts,
            'perPage' => $perPage,
            'events' => AcademicEvent::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'statusLabels' => AcademicEventAbstractService::statusLabels(),
            'fileReceiptLabels' => AcademicEventAbstractService::fileReceiptLabels(),
            'registeredByLabels' => AcademicEventAbstractService::registeredByLabels(),
            'registeredByListLabels' => AcademicEventAbstractService::registeredByListLabels(),
            'presentationTypeLabels' => AcademicEventAbstractService::presentationTypeLabels(),
        ]);
    }

    public function create(Request $request): View
    {
        $abstract = new AcademicEventAbstract([
            'status' => 'receipt',
            'file_receipt_status' => 'not_submitted',
            'registered_by' => 'user',
            'presentation_type' => 'oral',
            'submitted_at' => now(),
        ]);

        return view('backoffice.academic-event-abstracts.create', [
            'abstract' => $abstract,
            'events' => AcademicEvent::query()->with(['fields' => fn ($q) => $q->orderBy('sort_order')])->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'statusLabels' => AcademicEventAbstractService::statusLabels(),
            'fileReceiptLabels' => AcademicEventAbstractService::fileReceiptLabels(),
            'registeredByLabels' => AcademicEventAbstractService::registeredByLabels(),
            'presentationTypeLabels' => AcademicEventAbstractService::presentationTypeLabels(),
            'eventsFieldsMap' => $this->abstractService->eventFieldsMap(),
            'returnUrl' => $request->query('return_url', route('backoffice.academic-event-abstracts.index')),
        ]);
    }

    public function store(StoreAcademicEventAbstractRequest $request): RedirectResponse
    {
        $data = $this->payloadFromRequest($request);

        DB::transaction(function () use ($request, $data) {
            $abstract = AcademicEventAbstract::query()->create($data);
            $this->abstractService->storeUploadedFiles($abstract, (array) $request->file('attachments', []));
        });

        return redirect()->route('backoffice.academic-event-abstracts.index')
            ->with('success', '초록이 등록되었습니다.');
    }

    public function edit(Request $request, AcademicEventAbstract $academic_event_abstract): View
    {
        $academic_event_abstract->load(['event', 'member', 'files']);

        return view('backoffice.academic-event-abstracts.edit', [
            'abstract' => $academic_event_abstract,
            'events' => AcademicEvent::query()->with(['fields' => fn ($q) => $q->orderBy('sort_order')])->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'statusLabels' => AcademicEventAbstractService::statusLabels(),
            'fileReceiptLabels' => AcademicEventAbstractService::fileReceiptLabels(),
            'registeredByLabels' => AcademicEventAbstractService::registeredByLabels(),
            'presentationTypeLabels' => AcademicEventAbstractService::presentationTypeLabels(),
            'eventsFieldsMap' => $this->abstractService->eventFieldsMap(),
            'returnUrl' => $request->query('return_url', route('backoffice.academic-event-abstracts.index')),
        ]);
    }

    public function update(UpdateAcademicEventAbstractRequest $request, AcademicEventAbstract $academic_event_abstract): RedirectResponse
    {
        $data = $this->payloadFromRequest($request);

        DB::transaction(function () use ($request, $academic_event_abstract, $data) {
            $academic_event_abstract->update($data);
            $removeIds = array_map('intval', (array) $request->input('remove_file_ids', []));
            $this->abstractService->deleteFilesForAbstract($academic_event_abstract, $removeIds);
            $this->abstractService->storeUploadedFiles($academic_event_abstract, (array) $request->file('attachments', []));
        });

        return redirect()->route('backoffice.academic-event-abstracts.index')
            ->with('success', '초록이 수정되었습니다.');
    }

    public function destroy(AcademicEventAbstract $academic_event_abstract): RedirectResponse
    {
        if (! $this->abstractService->canDeleteAbstract($academic_event_abstract)) {
            return redirect()->route('backoffice.academic-event-abstracts.index')
                ->with('error', '삭제할 수 없는 초록입니다.');
        }
        $academic_event_abstract->delete();

        return redirect()->route('backoffice.academic-event-abstracts.index')
            ->with('success', '삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'abstract_ids' => ['required', 'array'],
            'abstract_ids.*' => ['integer', 'exists:academic_event_abstracts,id'],
        ]);

        $deleted = $this->abstractService->deleteMany($validated['abstract_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $searchField = (string) $request->get('search_field', 'all');
        $perPage = 10;

        $query = User::query()
            ->select(['id', 'name', 'login_id', 'email', 'phone_number', 'license_number'])
            ->notWithdrawn();

        if ($keyword !== '' && $searchField !== 'all') {
            $query->where(function ($q) use ($keyword, $searchField) {
                if ($searchField === 'name') {
                    $q->where('name', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'id') {
                    $q->where('login_id', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'email') {
                    $q->where('email', 'like', '%' . $keyword . '%');
                } elseif ($searchField === 'phone') {
                    $q->where('phone_number', 'like', '%' . $keyword . '%');
                }
            });
        } elseif ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('login_id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone_number', 'like', '%' . $keyword . '%');
            });
        }

        $members = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $members->getCollection()->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'login_id' => $member->login_id,
                'email' => $member->email,
                'phone_number' => $member->phone_number,
                'license_number' => $member->license_number,
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
     * @return array<string, mixed>
     */
    private function payloadFromRequest(Request $request): array
    {
        return [
            'academic_event_id' => (int) $request->input('academic_event_id'),
            'member_id' => $request->filled('member_id') ? (int) $request->input('member_id') : null,
            'registered_by' => (string) $request->input('registered_by'),
            'status' => (string) $request->input('status'),
            'file_receipt_status' => (string) $request->input('file_receipt_status'),
            'author_name' => (string) $request->input('author_name'),
            'author_name_en' => $request->input('author_name_en'),
            'author_phone' => $request->input('author_phone'),
            'author_mobile' => $request->input('author_mobile'),
            'author_email' => $request->input('author_email'),
            'title' => (string) $request->input('title'),
            'presentation_type' => (string) $request->input('presentation_type'),
            'academic_event_field_id' => $request->filled('academic_event_field_id')
                ? (int) $request->input('academic_event_field_id')
                : null,
            'note' => $request->input('note'),
            'submitted_at' => Carbon::parse((string) $request->input('submitted_at')),
        ];
    }
}

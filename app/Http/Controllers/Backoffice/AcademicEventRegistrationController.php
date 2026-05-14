<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\AcademicEventRegistrationRequest;
use App\Models\AcademicEvent;
use App\Models\AcademicEventRegistration;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Services\Backoffice\AcademicEventRegistrationService;
use App\Services\Backoffice\PaymentPlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class AcademicEventRegistrationController extends Controller
{
    public function __construct(
        protected AcademicEventRegistrationService $registrationService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $registrations = $this->registrationService->paginateIndex($request);

        return view('backoffice.academic-event-registrations.index', [
            'registrations' => $registrations,
            'perPage' => $perPage,
            'events' => AcademicEvent::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'paymentStatusLabels' => AcademicEventRegistrationService::paymentStatusLabels(),
            'regTypeLabels' => AcademicEventRegistrationService::regTypeLabels(),
            'paymentMethodLabels' => AcademicEventRegistrationService::paymentMethodLabels(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('backoffice.academic-event-registrations.create', [
            'registration' => new AcademicEventRegistration([
                'reg_type' => 'pre',
                'payment_status' => 'pending_payment',
                'payment_method' => 'bank_transfer',
                'receipt_issue' => 'NO',
                'registered_at' => now(),
            ]),
            'events' => AcademicEvent::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'paymentStatusLabels' => AcademicEventRegistrationService::paymentStatusLabels(),
            'regTypeLabels' => AcademicEventRegistrationService::regTypeLabels(),
            'paymentMethodLabels' => AcademicEventRegistrationService::paymentMethodLabels(),
            'returnUrl' => $request->query('return_url', route('backoffice.academic-event-registrations.index')),
            'selectedItemsJson' => '[]',
        ]);
    }

    public function store(AcademicEventRegistrationRequest $request): RedirectResponse
    {
        $items = $this->registrationService->parseItemsPayload($request->input('payment_items_payload'));
        if ($items === []) {
            throw ValidationException::withMessages(['payment_items_payload' => '결제 항목을 1개 이상 선택해주세요.']);
        }

        $event = AcademicEvent::query()->findOrFail((int) $request->input('academic_event_id'));

        DB::transaction(function () use ($request, $items, $event) {
            $registration = AcademicEventRegistration::query()->create([
                'registration_no' => $this->registrationService->nextRegistrationNo($event),
                'academic_event_id' => $event->id,
                'member_id' => $request->filled('member_id') ? (int) $request->input('member_id') : null,
                'name' => (string) $request->input('name'),
                'license_no' => $request->input('license_no'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'reg_type' => (string) $request->input('reg_type'),
                'payment_method' => (string) $request->input('payment_method'),
                'payment_status' => (string) $request->input('payment_status'),
                'registered_at' => Carbon::parse((string) $request->input('registered_at')),
                'applied_at' => now(),
                'paid_at' => $request->input('payment_status') === 'completed' ? now() : null,
                'bank_depositor' => $request->input('bank_depositor'),
                'bank_deposit_date' => $request->input('bank_deposit_date'),
                'bank_account_text' => $request->input('bank_account_text'),
                'admin_memo' => $request->input('admin_memo'),
                'receipt_issue' => (string) $request->input('receipt_issue'),
                'receipt_type' => $request->input('receipt_type'),
                'receipt_number' => $request->input('receipt_number'),
                'refund_bank' => $request->input('refund_bank'),
                'refund_account' => $request->input('refund_account'),
                'refund_holder' => $request->input('refund_holder'),
                'cancelled_at' => $request->input('payment_status') === 'cancelled' ? now() : null,
                'total_amount' => $this->registrationService->sumItems($items),
            ]);
            $this->registrationService->syncItems($registration, $items);
        });

        return redirect()->route('backoffice.academic-event-registrations.index')
            ->with('success', '참가 및 결제 정보가 등록되었습니다.');
    }

    public function edit(Request $request, AcademicEventRegistration $academic_event_registration): View
    {
        $academic_event_registration->load(['event', 'member', 'items']);

        $selectedItems = $academic_event_registration->items->map(function ($item) {
            return [
                'payment_plan_id' => $item->payment_plan_id,
                'item_name' => $item->item_name,
                'category' => $item->category,
                'member_scope' => $item->member_scope,
                'price' => (int) $item->price,
            ];
        })->values()->all();

        return view('backoffice.academic-event-registrations.edit', [
            'registration' => $academic_event_registration,
            'events' => AcademicEvent::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'paymentStatusLabels' => AcademicEventRegistrationService::paymentStatusLabels(),
            'regTypeLabels' => AcademicEventRegistrationService::regTypeLabels(),
            'paymentMethodLabels' => AcademicEventRegistrationService::paymentMethodLabels(),
            'returnUrl' => $request->query('return_url', route('backoffice.academic-event-registrations.index')),
            'selectedItemsJson' => json_encode($selectedItems, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function update(AcademicEventRegistrationRequest $request, AcademicEventRegistration $academic_event_registration): RedirectResponse
    {
        $items = $this->registrationService->parseItemsPayload($request->input('payment_items_payload'));
        if ($items === []) {
            throw ValidationException::withMessages(['payment_items_payload' => '결제 항목을 1개 이상 선택해주세요.']);
        }

        DB::transaction(function () use ($request, $academic_event_registration, $items) {
            $academic_event_registration->update([
                'academic_event_id' => (int) $request->input('academic_event_id'),
                'member_id' => $request->filled('member_id') ? (int) $request->input('member_id') : null,
                'name' => (string) $request->input('name'),
                'license_no' => $request->input('license_no'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'reg_type' => (string) $request->input('reg_type'),
                'payment_method' => (string) $request->input('payment_method'),
                'payment_status' => (string) $request->input('payment_status'),
                'registered_at' => Carbon::parse((string) $request->input('registered_at')),
                'paid_at' => $request->input('payment_status') === 'completed'
                    ? ($academic_event_registration->paid_at ?? now())
                    : null,
                'bank_depositor' => $request->input('bank_depositor'),
                'bank_deposit_date' => $request->input('bank_deposit_date'),
                'bank_account_text' => $request->input('bank_account_text'),
                'admin_memo' => $request->input('admin_memo'),
                'receipt_issue' => (string) $request->input('receipt_issue'),
                'receipt_type' => $request->input('receipt_type'),
                'receipt_number' => $request->input('receipt_number'),
                'refund_bank' => $request->input('refund_bank'),
                'refund_account' => $request->input('refund_account'),
                'refund_holder' => $request->input('refund_holder'),
                'cancelled_at' => $request->input('payment_status') === 'cancelled' ? now() : null,
                'total_amount' => $this->registrationService->sumItems($items),
            ]);
            $this->registrationService->syncItems($academic_event_registration, $items);
        });

        return redirect()->route('backoffice.academic-event-registrations.index')
            ->with('success', '참가 및 결제 정보가 수정되었습니다.');
    }

    public function destroy(AcademicEventRegistration $academic_event_registration): RedirectResponse
    {
        $academic_event_registration->delete();

        return redirect()->route('backoffice.academic-event-registrations.index')
            ->with('success', '삭제되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'registration_ids' => ['required', 'array'],
            'registration_ids.*' => ['integer', 'exists:academic_event_registrations,id'],
        ]);

        $deleted = $this->registrationService->deleteMany($validated['registration_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    public function export(Request $request)
    {
        $query = AcademicEventRegistration::query()->with('event')->orderByDesc('id');
        if ($request->filled('academic_event_id')) {
            $query->where('academic_event_id', (int) $request->get('academic_event_id'));
        }
        if ($request->filled('payment_status') && $request->get('payment_status') !== 'all') {
            $query->where('payment_status', (string) $request->get('payment_status'));
        }
        if ($request->filled('reg_type') && $request->get('reg_type') !== 'all') {
            $query->where('reg_type', (string) $request->get('reg_type'));
        }
        $keyword = trim((string) $request->get('search_keyword', ''));
        if ($keyword !== '') {
            $query->where(function (Builder $q) use ($keyword) {
                $like = '%' . $keyword . '%';
                $q->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $rows = $query->limit(5000)->get();
        $statusLabels = AcademicEventRegistrationService::paymentStatusLabels();
        $regLabels = AcademicEventRegistrationService::regTypeLabels();
        $methodLabels = AcademicEventRegistrationService::paymentMethodLabels();

        $csv = [];
        $csv[] = '참가번호,행사명,이름,휴대폰,이메일,등록구분,결제상태,결제수단,등록일시';
        foreach ($rows as $row) {
            $csv[] = implode(',', [
                $this->escapeCsv($row->registration_no),
                $this->escapeCsv((string) ($row->event->title ?? '')),
                $this->escapeCsv($row->name),
                $this->escapeCsv((string) $row->phone),
                $this->escapeCsv((string) $row->email),
                $this->escapeCsv($regLabels[$row->reg_type] ?? $row->reg_type),
                $this->escapeCsv($statusLabels[$row->payment_status] ?? $row->payment_status),
                $this->escapeCsv($methodLabels[$row->payment_method] ?? $row->payment_method),
                $this->escapeCsv(optional($row->registered_at)->format('Y-m-d H:i:s') ?? ''),
            ]);
        }

        $filename = 'academic_event_registrations_' . now()->format('Ymd_His') . '.csv';
        $content = "\xEF\xBB\xBF" . implode("\n", $csv);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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

    public function searchPaymentPlans(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $perPage = 10;

        $query = PaymentPlan::query()->orderByDesc('id');
        if ($keyword !== '') {
            $query->where('plan_name', 'like', '%' . $keyword . '%');
        }

        $plans = $query->paginate($perPage)->withQueryString();
        $categoryLabels = PaymentPlanService::categoryLabels();

        return response()->json([
            'data' => $plans->getCollection()->map(function (PaymentPlan $plan) use ($categoryLabels) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->plan_name,
                    'category' => $plan->category,
                    'category_label' => $categoryLabels[$plan->category] ?? $plan->category,
                    'member_scope' => $plan->member_status,
                    'member_scope_label' => $plan->member_status === 'member' ? '회원' : '비회원',
                    'price' => $this->registrationService->resolvePlanSelectablePrice($plan),
                    'price_display' => $this->registrationService->formatPlanPriceDisplay($plan),
                ];
            }),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'total' => $plans->total(),
                'per_page' => $plans->perPage(),
            ],
        ]);
    }

    private function escapeCsv(string $value): string
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
}

<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\EduTrainingPaymentRequest;
use App\Models\EduTraining;
use App\Models\EduTrainingPayment;
use App\Models\PaymentPlan;
use App\Models\User;
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

class EduTrainingPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        $query = EduTrainingPayment::query()
            ->with(['training', 'items'])
            ->orderByDesc('registered_at')
            ->orderByDesc('id');

        if ($request->filled('training_id')) {
            $query->where('edu_training_id', (int) $request->get('training_id'));
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
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%');
            });
        }

        $payments = $query->paginate($perPage)->withQueryString();

        return view('backoffice.edu-training-payments.index', [
            'payments' => $payments,
            'perPage' => $perPage,
            'trainings' => EduTraining::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'statusLabels' => $this->statusLabels(),
            'regTypeLabels' => $this->regTypeLabels(),
            'methodLabels' => $this->methodLabels(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('backoffice.edu-training-payments.create', [
            'payment' => new EduTrainingPayment([
                'reg_type' => 'pre',
                'payment_status' => 'pending_payment',
                'payment_method' => 'card',
                'receipt_issue' => 'NO',
                'registered_at' => now(),
            ]),
            'trainings' => EduTraining::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'statusLabels' => $this->statusLabels(),
            'regTypeLabels' => $this->regTypeLabels(),
            'methodLabels' => $this->methodLabels(),
            'returnUrl' => $request->query('return_url', route('backoffice.edu-training-payments.index')),
            'selectedItemsJson' => '[]',
        ]);
    }

    public function store(EduTrainingPaymentRequest $request): RedirectResponse
    {
        $items = $this->parseItems($request->input('payment_items_payload'));
        if ($items === []) {
            throw ValidationException::withMessages(['payment_items_payload' => '결제 항목을 1개 이상 선택해주세요.']);
        }

        $payment = DB::transaction(function () use ($request, $items) {
            $payment = EduTrainingPayment::query()->create([
                'order_no' => $this->nextOrderNo(),
                'edu_training_id' => (int) $request->input('edu_training_id'),
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
                'admin_memo' => $request->input('admin_memo'),
                'receipt_issue' => (string) $request->input('receipt_issue'),
                'receipt_type' => $request->input('receipt_type'),
                'receipt_number' => $request->input('receipt_number'),
                'refund_bank' => $request->input('refund_bank'),
                'refund_account' => $request->input('refund_account'),
                'refund_holder' => $request->input('refund_holder'),
                'cancelled_at' => $request->input('payment_status') === 'cancelled' ? now() : null,
                'total_amount' => $this->sumItems($items),
            ]);

            $this->syncItems($payment, $items);

            return $payment;
        });

        return redirect()->route('backoffice.edu-training-payments.index')
            ->with('success', '참가 및 결제 정보가 등록되었습니다.');
    }

    public function edit(Request $request, EduTrainingPayment $eduTrainingPayment): View
    {
        $eduTrainingPayment->load(['training', 'member', 'items']);

        $selectedItems = $eduTrainingPayment->items->map(function ($item) {
            return [
                'payment_plan_id' => $item->payment_plan_id,
                'item_name' => $item->item_name,
                'category' => $item->category,
                'member_scope' => $item->member_scope,
                'price' => (int) $item->price,
            ];
        })->values()->all();

        return view('backoffice.edu-training-payments.edit', [
            'payment' => $eduTrainingPayment,
            'trainings' => EduTraining::query()->orderByDesc('year')->orderByDesc('id')->get(['id', 'title', 'year']),
            'statusLabels' => $this->statusLabels(),
            'regTypeLabels' => $this->regTypeLabels(),
            'methodLabels' => $this->methodLabels(),
            'returnUrl' => $request->query('return_url', route('backoffice.edu-training-payments.index')),
            'selectedItemsJson' => json_encode($selectedItems, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function update(EduTrainingPaymentRequest $request, EduTrainingPayment $eduTrainingPayment): RedirectResponse
    {
        $items = $this->parseItems($request->input('payment_items_payload'));
        if ($items === []) {
            throw ValidationException::withMessages(['payment_items_payload' => '결제 항목을 1개 이상 선택해주세요.']);
        }

        DB::transaction(function () use ($request, $eduTrainingPayment, $items) {
            $eduTrainingPayment->update([
                'edu_training_id' => (int) $request->input('edu_training_id'),
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
                    ? ($eduTrainingPayment->paid_at ?? now())
                    : null,
                'bank_depositor' => $request->input('bank_depositor'),
                'bank_deposit_date' => $request->input('bank_deposit_date'),
                'admin_memo' => $request->input('admin_memo'),
                'receipt_issue' => (string) $request->input('receipt_issue'),
                'receipt_type' => $request->input('receipt_type'),
                'receipt_number' => $request->input('receipt_number'),
                'refund_bank' => $request->input('refund_bank'),
                'refund_account' => $request->input('refund_account'),
                'refund_holder' => $request->input('refund_holder'),
                'cancelled_at' => $request->input('payment_status') === 'cancelled' ? now() : null,
                'total_amount' => $this->sumItems($items),
            ]);

            $this->syncItems($eduTrainingPayment, $items);
        });

        return redirect()->route('backoffice.edu-training-payments.index')
            ->with('success', '참가 및 결제 정보가 수정되었습니다.');
    }

    public function confirmDeposit(Request $request, EduTrainingPayment $eduTrainingPayment): JsonResponse
    {
        $validated = $request->validate([
            'bank_depositor' => ['required', 'string', 'max:100'],
            'bank_deposit_date' => ['required', 'date'],
        ]);

        $eduTrainingPayment->update([
            'payment_method' => 'bank_transfer',
            'bank_depositor' => $validated['bank_depositor'],
            'bank_deposit_date' => $validated['bank_deposit_date'],
            'payment_status' => 'completed',
            'paid_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function cancel(EduTrainingPayment $eduTrainingPayment): RedirectResponse
    {
        $eduTrainingPayment->update([
            'payment_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('backoffice.edu-training-payments.index')
            ->with('success', '참가 내역이 취소(환불) 완료 처리되었습니다.');
    }

    public function bulkCancel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_ids' => ['required', 'array'],
            'payment_ids.*' => ['integer', 'exists:edu_training_payments,id'],
        ]);

        EduTrainingPayment::query()
            ->whereIn('id', $validated['payment_ids'])
            ->update([
                'payment_status' => 'cancelled',
                'cancelled_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('backoffice.edu-training-payments.index')
            ->with('success', '선택 항목이 취소(환불) 완료 처리되었습니다.');
    }

    public function export(Request $request)
    {
        $query = EduTrainingPayment::query()->with('training')->orderByDesc('id');
        if ($request->filled('training_id')) {
            $query->where('edu_training_id', (int) $request->get('training_id'));
        }
        if ($request->filled('payment_status') && $request->get('payment_status') !== 'all') {
            $query->where('payment_status', (string) $request->get('payment_status'));
        }
        if ($request->filled('reg_type') && $request->get('reg_type') !== 'all') {
            $query->where('reg_type', (string) $request->get('reg_type'));
        }

        $rows = $query->limit(3000)->get();

        $csv = [];
        $csv[] = '주문번호,연수명,이름,휴대폰번호,이메일,등록구분,결제상태,결제수단,등록일시';
        foreach ($rows as $row) {
            $csv[] = implode(',', [
                $this->escapeCsv($row->order_no),
                $this->escapeCsv((string) ($row->training->title ?? '')),
                $this->escapeCsv($row->name),
                $this->escapeCsv((string) $row->phone),
                $this->escapeCsv((string) $row->email),
                $this->escapeCsv($this->regTypeLabels()[$row->reg_type] ?? $row->reg_type),
                $this->escapeCsv($this->statusLabels()[$row->payment_status] ?? $row->payment_status),
                $this->escapeCsv($this->methodLabels()[$row->payment_method] ?? $row->payment_method),
                $this->escapeCsv(optional($row->registered_at)->format('Y-m-d H:i:s') ?? ''),
            ]);
        }

        $filename = 'edu_training_payments_' . now()->format('Ymd_His') . '.csv';
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
                    'price' => $this->resolvePlanSelectablePrice($plan),
                    'price_display' => $this->formatPlanPriceDisplay($plan),
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

    private function nextOrderNo(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $latest = EduTrainingPayment::query()
            ->where('order_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('order_no');

        $seq = 1;
        if (is_string($latest) && preg_match('/-(\d{3})$/', $latest, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    /** @return array<int, array<string,mixed>> */
    private function parseItems(string $payload): array
    {
        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            return [];
        }

        $items = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['item_name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $items[] = [
                'payment_plan_id' => isset($row['payment_plan_id']) && $row['payment_plan_id'] !== '' ? (int) $row['payment_plan_id'] : null,
                'item_name' => $name,
                'category' => (string) ($row['category'] ?? ''),
                'member_scope' => (string) ($row['member_scope'] ?? ''),
                'price' => max(0, (int) ($row['price'] ?? 0)),
            ];
        }

        return $items;
    }

    /** @param array<int, array<string,mixed>> $items */
    private function syncItems(EduTrainingPayment $payment, array $items): void
    {
        $payment->items()->delete();
        foreach ($items as $item) {
            $payment->items()->create($item);
        }
    }

    /** @param array<int, array<string,mixed>> $items */
    private function sumItems(array $items): int
    {
        return array_reduce($items, fn (int $sum, array $item) => $sum + (int) $item['price'], 0);
    }

    private function escapeCsv(string $value): string
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }

    private function resolvePlanSelectablePrice(PaymentPlan $plan): int
    {
        if ($plan->category === 'conference') {
            $early = (int) ($plan->price_early ?? 0);
            $site = (int) ($plan->price_site ?? 0);

            return $early > 0 ? $early : $site;
        }

        return (int) ($plan->price ?? 0);
    }

    private function formatPlanPriceDisplay(PaymentPlan $plan): string
    {
        if ($plan->category === 'conference') {
            return '사전 ' . number_format((int) $plan->price_early) . '원 / 현장 ' . number_format((int) $plan->price_site) . '원';
        }

        return number_format((int) ($plan->price ?? 0)) . '원';
    }

    /** @return array<string,string> */
    private function statusLabels(): array
    {
        return [
            'pending_payment' => '결제 대기',
            'pending' => '입금 대기',
            'completed' => '결제 완료',
            'cancel_requested' => '취소 요청',
            'cancelled' => '취소(환불) 완료',
        ];
    }

    /** @return array<string,string> */
    private function regTypeLabels(): array
    {
        return [
            'pre' => '사전등록',
            'onsite' => '현장등록',
        ];
    }

    /** @return array<string,string> */
    private function methodLabels(): array
    {
        return [
            'card' => '신용카드',
            'bank_transfer' => '무통장 입금',
        ];
    }
}


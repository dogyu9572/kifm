<?php

namespace App\Services\Frontend;

use App\Models\Coupon;
use App\Models\EduTraining;
use App\Models\EduTrainingPayment;
use App\Models\EduTrainingRound;
use App\Models\User;
use App\Services\Backoffice\EduTrainingService;
use App\Services\Backoffice\MemberService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PublicTrainingCourseService
{
    public const FALLBACK_HEAD_IMAGE = 'images/img_sample_training_course_top.jpg';
    public const ROUND_ITEM_CATEGORY_PREFIX = 'training_round:';

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

    public function featuredTraining(): ?EduTraining
    {
        return EduTraining::query()
            ->with('rounds')
            ->where('status', 'PUBLIC')
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->first();
    }

    public function paginateVisible(Request $request, int $perPage = 6): LengthAwarePaginator
    {
        $query = EduTraining::query()->with('rounds')->where('status', 'PUBLIC');
        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findVisible(int $id): EduTraining
    {
        return EduTraining::query()
            ->with(['rounds', 'attachments'])
            ->where('status', 'PUBLIC')
            ->findOrFail($id);
    }

    /** @return list<int> */
    public function yearOptions(): array
    {
        return EduTraining::query()
            ->where('status', 'PUBLIC')
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(static fn ($year): int => (int) $year)
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

    public function detailUrl(EduTraining $training): string
    {
        return route('academic_event.training_course_view', ['training' => $training->id]);
    }

    public function paymentUrl(EduTraining $training): string
    {
        return route('academic_event.training_course_payment', ['training' => $training->id]);
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

    public function fileUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function seasonLabel(?string $season): string
    {
        return EduTrainingService::seasonLabels()[$season ?? ''] ?? (string) $season;
    }

    public function methodLabel(?string $method): string
    {
        return EduTrainingService::methodLabels()[$method ?? ''] ?? (string) $method;
    }

    public function headlineText(EduTraining $training): string
    {
        $text = trim(strip_tags((string) ($training->introduction ?: $training->overview ?: $training->registration_info ?: '')));

        return $text !== '' ? Str::limit($text, 120) : $training->title;
    }

    public function roundDateText(?EduTrainingRound $round): string
    {
        if (! $round || ! $round->lecture_date) {
            return '-';
        }

        return $this->dateText($round->lecture_date);
    }

    public function applicationPeriodText(?EduTrainingRound $round): string
    {
        if (! $round || (! $round->apply_start_date && ! $round->apply_end_date)) {
            return '-';
        }

        if ($round->apply_start_date && $round->apply_end_date) {
            return $this->dateText($round->apply_start_date) . ' ~ ' . $this->dateText($round->apply_end_date);
        }

        return $round->apply_start_date
            ? $this->dateText($round->apply_start_date) . ' ~'
            : '~ ' . $this->dateText($round->apply_end_date);
    }

    /** @return array{code: string, label: string, class: string} */
    public function status(EduTraining $training): array
    {
        $rounds = $this->publicRounds($training);
        if ($rounds->isEmpty()) {
            return ['code' => 'closed', 'label' => '신청마감', 'class' => 'end'];
        }
        if ($rounds->contains(fn (EduTrainingRound $round): bool => $this->roundStatus($round)['code'] === 'ongoing')) {
            return ['code' => 'ongoing', 'label' => '모집 중', 'class' => 'ing'];
        }
        if ($rounds->contains(fn (EduTrainingRound $round): bool => $this->roundStatus($round)['code'] === 'upcoming')) {
            return ['code' => 'upcoming', 'label' => '모집예정', 'class' => 'expected'];
        }

        return ['code' => 'closed', 'label' => '신청마감', 'class' => 'end'];
    }

    /** @return array{code: string, label: string, class: string} */
    public function roundStatus(EduTrainingRound $round): array
    {
        $today = Carbon::today();
        if ($round->apply_start_date && $today->lt($round->apply_start_date)) {
            return ['code' => 'upcoming', 'label' => '모집예정', 'class' => 'expected'];
        }
        if ($round->apply_start_date && $round->apply_end_date && $today->betweenIncluded($round->apply_start_date, $round->apply_end_date)) {
            return ['code' => 'ongoing', 'label' => '모집 중', 'class' => 'ing'];
        }
        if (! $round->apply_start_date && (! $round->apply_end_date || $today->lte($round->apply_end_date))) {
            return ['code' => 'ongoing', 'label' => '모집 중', 'class' => 'ing'];
        }

        return ['code' => 'closed', 'label' => '신청마감', 'class' => 'end'];
    }

    /** @return Collection<int, EduTrainingRound> */
    public function publicRounds(EduTraining $training): Collection
    {
        $training->loadMissing('rounds');

        return $training->rounds
            ->filter(static fn (EduTrainingRound $round): bool => $round->status === 'PUBLIC')
            ->values();
    }

    /** @return array{code: string, label: string} */
    public function memberGrade(?User $user): array
    {
        if (! $user || $user->role !== 'user') {
            return ['code' => 'guest', 'label' => '비회원'];
        }

        $code = trim((string) $user->member_level);
        $labels = MemberService::memberLevelLabels();

        return [
            'code' => $code,
            'label' => $labels[$code] ?? ($code !== '' ? $code : '회원'),
        ];
    }

    /**
     * @return array{eligible: bool, price: int, message: string, grade_code: string, grade_label: string}
     */
    public function priceForRound(EduTrainingRound $round, ?User $user): array
    {
        $grade = $this->memberGrade($user);
        $prices = is_array($round->grade_prices) ? $round->grade_prices : [];
        $block = is_array($prices[$grade['code']] ?? null) ? $prices[$grade['code']] : null;

        if (! $block || ! filter_var($block['eligible'] ?? false, FILTER_VALIDATE_BOOL)) {
            return [
                'eligible' => false,
                'price' => 0,
                'message' => '현재 회원 등급으로 신청할 수 없는 차수입니다.',
                'grade_code' => $grade['code'],
                'grade_label' => $grade['label'],
            ];
        }

        return [
            'eligible' => true,
            'price' => max(0, (int) ($block['price'] ?? 0)),
            'message' => '',
            'grade_code' => $grade['code'],
            'grade_label' => $grade['label'],
        ];
    }

    public function isRoundFull(EduTrainingRound $round): bool
    {
        if ($round->is_capacity_unlimited || ! $round->capacity) {
            return false;
        }

        return $this->activeRoundPaymentCount($round) >= (int) $round->capacity;
    }

    public function canApplyRound(EduTrainingRound $round, ?User $user): bool
    {
        return $round->status === 'PUBLIC'
            && $this->roundStatus($round)['code'] === 'ongoing'
            && ! $this->isRoundFull($round)
            && $this->priceForRound($round, $user)['eligible'];
    }

    /** @param list<int> $roundIds */
    public function selectedRoundSummary(EduTraining $training, array $roundIds, ?User $user): array
    {
        $rounds = $this->publicRounds($training)
            ->filter(fn (EduTrainingRound $round): bool => in_array((int) $round->id, $roundIds, true))
            ->values();
        $items = $rounds->map(function (EduTrainingRound $round) use ($user, $training): array {
            $pricing = $this->priceForRound($round, $user);

            return [
                'round' => $round,
                'name' => $training->title . ' - ' . $round->round_label,
                'price' => (int) $pricing['price'],
                'pricing' => $pricing,
            ];
        });
        $subtotal = $items->sum('price');

        return [
            'items' => $items,
            'subtotal' => (int) $subtotal,
        ];
    }

    /**
     * @return array{coupon: Coupon, discount: int, final_amount: int}|null
     */
    public function resolveCoupon(mixed $code, int $subtotal): ?array
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '' || $subtotal <= 0) {
            return null;
        }

        $coupon = Coupon::query()
            ->with('paymentCategories')
            ->where('coupon_code', $code)
            ->where('status', 'ACTIVE')
            ->whereDate('valid_from', '<=', now()->toDateString())
            ->whereDate('valid_to', '>=', now()->toDateString())
            ->first();

        if (! $coupon) {
            return null;
        }

        $categories = $coupon->paymentCategories->pluck('payment_category')->all();
        if (! in_array('education', $categories, true)) {
            return null;
        }

        $discount = $coupon->discount_type === 'RATE'
            ? (int) floor($subtotal * ((float) $coupon->discount_value / 100))
            : (int) $coupon->discount_value;
        $discount = max(0, min($subtotal, $discount));

        return [
            'coupon' => $coupon,
            'discount' => $discount,
            'final_amount' => max(0, $subtotal - $discount),
        ];
    }

    /** @param array<string, mixed> $data */
    public function createPayment(EduTraining $training, array $data, ?User $user): EduTrainingPayment
    {
        $roundIds = array_map('intval', (array) ($data['round_ids'] ?? []));
        $summary = $this->selectedRoundSummary($training, $roundIds, $user);
        if ($summary['items']->isEmpty()) {
            throw new \RuntimeException('결제 항목을 1개 이상 선택해주세요.');
        }

        foreach ($summary['items'] as $item) {
            if (! $this->canApplyRound($item['round'], $user)) {
                throw new \RuntimeException($item['round']->round_label . ' 차수는 현재 신청할 수 없습니다.');
            }
        }

        $subtotal = (int) $summary['subtotal'];
        $couponResult = $this->resolveCoupon($data['coupon_code'] ?? null, $subtotal);
        $discount = (int) ($couponResult['discount'] ?? 0);
        $coupon = $couponResult['coupon'] ?? null;
        $finalAmount = max(0, $subtotal - $discount);
        $method = (string) ($data['payment_method'] ?? 'card');
        $paymentStatus = $finalAmount <= 0 ? 'completed' : ($method === 'card' ? 'pending_payment' : 'pending');

        return DB::transaction(function () use ($training, $data, $user, $summary, $subtotal, $discount, $coupon, $finalAmount, $method, $paymentStatus): EduTrainingPayment {
            $payment = EduTrainingPayment::query()->create([
                'order_no' => $this->nextOrderNo(),
                'edu_training_id' => $training->id,
                'member_id' => $user?->id,
                'name' => (string) ($data['name'] ?? ''),
                'license_no' => $data['license_no'] ?? null,
                'phone' => $this->normalizePhone($data['phone'] ?? null),
                'email' => $data['email'] ?? null,
                'reg_type' => 'pre',
                'payment_method' => $method,
                'payment_status' => $paymentStatus,
                'total_amount' => $finalAmount,
                'registered_at' => now(),
                'applied_at' => now(),
                'paid_at' => $paymentStatus === 'completed' ? now() : null,
                'bank_depositor' => $method === 'bank_transfer' ? ($data['bank_depositor'] ?? null) : null,
                'bank_deposit_date' => $method === 'bank_transfer' ? ($data['bank_deposit_date'] ?? null) : null,
                'admin_memo' => $this->paymentMemo($subtotal, $discount, $coupon?->coupon_code),
                'receipt_issue' => (string) ($data['receipt_issue'] ?? 'NO'),
                'receipt_type' => ($data['receipt_issue'] ?? 'NO') === 'YES' ? ($data['receipt_type'] ?? null) : null,
                'receipt_number' => ($data['receipt_issue'] ?? 'NO') === 'YES' ? ($data['receipt_number'] ?? null) : null,
                'refund_bank' => $data['refund_bank'] ?? null,
                'refund_account' => $data['refund_account'] ?? null,
                'refund_holder' => $data['refund_holder'] ?? null,
            ]);

            foreach ($summary['items'] as $item) {
                $payment->items()->create([
                    'payment_plan_id' => null,
                    'item_name' => $item['name'],
                    'category' => self::ROUND_ITEM_CATEGORY_PREFIX . $item['round']->id,
                    'member_scope' => $item['pricing']['grade_code'],
                    'price' => $item['price'],
                ]);
            }

            if ($coupon && ($paymentStatus === 'completed' || $method === 'bank_transfer')) {
                $coupon->increment('usage_count');
            }

            return $payment->fresh(['training', 'items']);
        });
    }

    public function confirmTossPayment(string $orderId, string $paymentKey, int $amount): EduTrainingPayment
    {
        $payment = EduTrainingPayment::query()
            ->with(['training', 'items'])
            ->where('order_no', $orderId)
            ->where('payment_method', 'card')
            ->first();

        if (! $payment) {
            throw new RuntimeException('확인할 연수교육 카드 결제 신청 내역이 없습니다.');
        }
        if ((int) $payment->total_amount !== $amount) {
            throw new RuntimeException('결제 금액이 신청 금액과 일치하지 않습니다.');
        }
        if ($payment->payment_status === 'completed') {
            return $payment;
        }

        $payload = $this->requestTossConfirm($paymentKey, $orderId, $amount);
        $isCompleted = ($payload['status'] ?? null) === 'DONE';
        $memo = (string) ($payment->admin_memo ?? '');
        if ($isCompleted && preg_match('/coupon=([A-Z0-9_-]+)/', $memo, $matches) && ! str_contains($memo, 'coupon_counted=1')) {
            Coupon::query()->where('coupon_code', $matches[1])->increment('usage_count');
            $memo = trim($memo . ';coupon_counted=1', ';');
        }

        $payment->update([
            'payment_status' => $isCompleted ? 'completed' : 'pending_payment',
            'paid_at' => $isCompleted ? now() : null,
            'admin_memo' => trim($memo . ';toss_payment_key=' . $paymentKey, ';'),
        ]);

        return $payment->refresh()->loadMissing(['training', 'items']);
    }

    private function requestTossConfirm(string $paymentKey, string $orderId, int $amount): array
    {
        $secretKey = (string) config('services.toss.secret_key');
        if ($secretKey === '') {
            throw new RuntimeException('토스페이먼츠 시크릿 키가 설정되어 있지 않습니다.');
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->post('https://api.tosspayments.com/v1/payments/confirm', [
                'paymentKey' => $paymentKey,
                'orderId' => $orderId,
                'amount' => $amount,
            ]);

        $payload = $response->json();
        if (! $response->successful()) {
            throw new RuntimeException((string) ($payload['message'] ?? '토스페이먼츠 결제 승인에 실패했습니다.'));
        }

        return is_array($payload) ? $payload : [];
    }

    public function paymentSummary(EduTrainingPayment $payment): array
    {
        $final = (int) $payment->total_amount;
        $subtotal = (int) $payment->items->sum('price');
        if ($subtotal <= 0 && $final > 0) {
            $subtotal = $final;
        }
        $discount = max(0, (int) $subtotal - $final);
        $couponCode = '';
        if (preg_match('/coupon=([A-Z0-9_-]+)/', (string) $payment->admin_memo, $matches)) {
            $couponCode = $matches[1];
        }

        return [
            'subtotal' => (int) $subtotal,
            'discount' => $discount,
            'final_amount' => $final,
            'coupon_code' => $couponCode,
        ];
    }

    public function findPayment(int $id): EduTrainingPayment
    {
        return EduTrainingPayment::query()
            ->with(['training', 'items'])
            ->findOrFail($id);
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
        if ($status === 'all') {
            return;
        }

        $query->whereHas('rounds', function (Builder $roundQuery) use ($status): void {
            $roundQuery->where('status', 'PUBLIC');
            $today = Carbon::today()->toDateString();
            if ($status === 'upcoming') {
                $roundQuery->whereNotNull('apply_start_date')->whereDate('apply_start_date', '>', $today);
            } elseif ($status === 'ongoing') {
                $roundQuery->where(function (Builder $q) use ($today): void {
                    $q->where(function (Builder $dateQ) use ($today): void {
                        $dateQ->whereNotNull('apply_start_date')
                            ->whereNotNull('apply_end_date')
                            ->whereDate('apply_start_date', '<=', $today)
                            ->whereDate('apply_end_date', '>=', $today);
                    })->orWhere(function (Builder $dateQ) use ($today): void {
                        $dateQ->whereNull('apply_start_date')
                            ->where(function (Builder $endQ) use ($today): void {
                                $endQ->whereNull('apply_end_date')->orWhereDate('apply_end_date', '>=', $today);
                            });
                    });
                });
            } elseif ($status === 'closed') {
                $roundQuery->whereNotNull('apply_end_date')
                    ->whereDate('apply_end_date', '<', $today);
            }
        });
    }

    private function activeRoundPaymentCount(EduTrainingRound $round): int
    {
        return EduTrainingPayment::query()
            ->where('edu_training_id', $round->edu_training_id)
            ->whereNotIn('payment_status', ['cancel_requested', 'cancelled'])
            ->whereHas('items', function (Builder $query) use ($round): void {
                $query->where('category', self::ROUND_ITEM_CATEGORY_PREFIX . $round->id);
            })
            ->count();
    }

    private function nextOrderNo(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $latest = EduTrainingPayment::query()
            ->where('order_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('order_no');

        $seq = 1;
        if (is_string($latest) && preg_match('/-(\d{3})$/', $latest, $matches)) {
            $seq = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    private function normalizePhone(mixed $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return $digits !== '' ? $digits : null;
    }

    private function paymentMemo(int $subtotal, int $discount, ?string $couponCode): string
    {
        return 'subtotal=' . $subtotal . ';discount=' . $discount . ';coupon=' . ($couponCode ?: '');
    }

    private function dateText(Carbon $date): string
    {
        $weekdays = ['일', '월', '화', '수', '목', '금', '토'];

        return $date->format('Y년 n월 j일') . ' (' . $weekdays[(int) $date->dayOfWeek] . ')';
    }
}

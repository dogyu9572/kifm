<?php

namespace App\Services\Backoffice;

use App\Models\AcademicEvent;
use App\Models\AcademicEventRegistration;
use App\Models\PaymentPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicEventRegistrationService
{
    /** @return array<string, string> */
    public static function paymentStatusLabels(): array
    {
        return [
            'pending_payment' => '결제 대기',
            'pending' => '입금 대기',
            'completed' => '결제 완료',
            'cancel_requested' => '취소 요청',
            'cancelled' => '취소(환불) 완료',
        ];
    }

    /** @return array<string, string> */
    public static function regTypeLabels(): array
    {
        return [
            'pre' => '사전등록',
            'onsite' => '현장등록',
        ];
    }

    /** @return array<string, string> */
    public static function paymentMethodLabels(): array
    {
        return [
            'card' => '신용카드',
            'bank_transfer' => '무통장 입금',
            'onsite' => '현장결제',
        ];
    }

    public function paginateIndex(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $query = $this->indexFilterQuery($request)->with(['event', 'items']);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * 목록·엑셀 등에 공통으로 사용하는 필터 쿼리 (페이지네이션 전).
     */
    public function indexFilterQuery(Request $request): Builder
    {
        $query = AcademicEventRegistration::query()
            ->orderByDesc('registered_at')
            ->orderByDesc('id');

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
                    ->orWhere('email', 'like', $like)
                    ->orWhere('registration_no', 'like', $like);
            });
        }

        return $query;
    }

    /**
     * 선택한 참가 등록 ID를 삭제한다. (하위 결제 항목은 FK CASCADE)
     *
     * @param  list<int|string>  $ids
     * @return int 삭제된 행 수
     */
    public function deleteMany(array $ids): int
    {
        $unique = array_values(array_unique(array_map(static fn ($id) => (int) $id, $ids)));
        $unique = array_values(array_filter($unique, static fn (int $id) => $id > 0));
        if ($unique === []) {
            return 0;
        }

        return (int) DB::transaction(static fn () => AcademicEventRegistration::query()->whereIn('id', $unique)->delete());
    }

    public function nextRegistrationNo(AcademicEvent $event): string
    {
        $prefix = 'REG-' . $event->id . '-' . now()->format('Ymd') . '-';
        $latest = AcademicEventRegistration::query()
            ->where('registration_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('registration_no');
        $seq = 1;
        if (is_string($latest) && preg_match('/-(\d{3})$/', $latest, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    /** @param array<int, array<string, mixed>> $items */
    public function syncItems(AcademicEventRegistration $registration, array $items): void
    {
        $registration->items()->delete();
        foreach ($items as $item) {
            $registration->items()->create($item);
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function parseItemsPayload(?string $payload): array
    {
        if ($payload === null || $payload === '') {
            return [];
        }
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

    /** @param array<int, array<string, mixed>> $items */
    public function sumItems(array $items): int
    {
        return array_reduce($items, fn (int $sum, array $item) => $sum + (int) $item['price'], 0);
    }

    public function resolvePlanSelectablePrice(PaymentPlan $plan): int
    {
        if ($plan->category === 'conference') {
            $early = (int) ($plan->price_early ?? 0);
            $site = (int) ($plan->price_site ?? 0);

            return $early > 0 ? $early : $site;
        }

        return (int) ($plan->price ?? 0);
    }

    public function formatPlanPriceDisplay(PaymentPlan $plan): string
    {
        if ($plan->category === 'conference') {
            return '사전 ' . number_format((int) $plan->price_early) . '원 / 현장 ' . number_format((int) $plan->price_site) . '원';
        }

        return number_format((int) ($plan->price ?? 0)) . '원';
    }
}

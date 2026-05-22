<?php

namespace App\Services\Frontend;

use App\Models\AcademicEvent;
use App\Models\AcademicEventRegistration;
use App\Models\Coupon;
use App\Models\MemberExecutive;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Services\Backoffice\AcademicEventRegistrationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicAcademicConferenceRegistrationService
{
    public function __construct(
        private readonly AcademicEventRegistrationService $registrationService,
    ) {}

    /** @return Collection<int, PaymentPlan> */
    public function matchingConferencePlans(User $user): Collection
    {
        $grade = trim((string) $user->member_level);
        $memberType = trim((string) $user->job_type);
        $memberType = $memberType !== '' ? $memberType : 'none';
        $executive = $this->isExecutive($user) ? 'executive' : 'non-executive';

        return PaymentPlan::query()
            ->with(['grades', 'types'])
            ->active()
            ->where('category', 'conference')
            ->where('member_status', 'member')
            ->where(function ($query) use ($executive) {
                $query->whereNull('executive')->orWhere('executive', $executive);
            })
            ->whereHas('grades', fn ($query) => $query->where('grade', $grade))
            ->whereHas('types', fn ($query) => $query->where('member_type', $memberType))
            ->orderBy('price_early')
            ->orderBy('id')
            ->get();
    }

    /** @return Collection<int, PaymentPlan> */
    public function nonMemberConferencePlans(): Collection
    {
        return PaymentPlan::query()
            ->active()
            ->where('category', 'conference')
            ->where('member_status', 'non-member')
            ->orderBy('price_early')
            ->orderBy('id')
            ->get();
    }

    /** @param array<int|string> $ids */
    public function selectedPlansForUser(User $user, array $ids): Collection
    {
        $allowed = $this->matchingConferencePlans($user)->keyBy('id');
        $selected = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0 && $allowed->has($id))
            ->unique()
            ->values();

        return $selected->map(fn (int $id) => $allowed->get($id))->filter()->values();
    }

    /** @param array<int|string> $ids */
    public function selectedPlansForNonMember(array $ids): Collection
    {
        $allowed = $this->nonMemberConferencePlans()->keyBy('id');
        $selected = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0 && $allowed->has($id))
            ->unique()
            ->values();

        return $selected->map(fn (int $id) => $allowed->get($id))->filter()->values();
    }

    public function findMemberRegistration(AcademicEvent $event, User $user, ?int $registrationId = null): ?AcademicEventRegistration
    {
        $query = AcademicEventRegistration::query()
            ->with(['items', 'member'])
            ->where('academic_event_id', $event->id)
            ->where('member_id', $user->id);

        if ($registrationId !== null && $registrationId > 0) {
            return (clone $query)->whereKey($registrationId)->first();
        }

        return $query
            ->orderByDesc('registered_at')
            ->orderByDesc('id')
            ->first();
    }

    public function findNonMemberRegistration(AcademicEvent $event, string $name, string $email, string $phone): ?AcademicEventRegistration
    {
        $phone = preg_replace('/\D+/', '', $phone);

        return AcademicEventRegistration::query()
            ->with(['items', 'member'])
            ->where('academic_event_id', $event->id)
            ->whereNull('member_id')
            ->where('name', trim($name))
            ->where('email', trim($email))
            ->where('phone', $phone)
            ->orderByDesc('registered_at')
            ->orderByDesc('id')
            ->first();
    }

    public function findRegistrationForLookup(AcademicEvent $event, ?User $user, ?int $registrationId): ?AcademicEventRegistration
    {
        if ($user?->role === 'user') {
            return $this->findMemberRegistration($event, $user, $registrationId);
        }

        if ($registrationId === null || $registrationId < 1) {
            return null;
        }

        return AcademicEventRegistration::query()
            ->with(['items', 'member'])
            ->where('academic_event_id', $event->id)
            ->whereKey($registrationId)
            ->first();
    }

    public function findNonMemberRegistrationById(AcademicEvent $event, ?int $registrationId): ?AcademicEventRegistration
    {
        if ($registrationId === null || $registrationId < 1) {
            return null;
        }

        return AcademicEventRegistration::query()
            ->with(['items', 'member'])
            ->where('academic_event_id', $event->id)
            ->whereNull('member_id')
            ->whereKey($registrationId)
            ->first();
    }

    public function canAccessRegistration(AcademicEventRegistration $registration, AcademicEvent $event, ?User $user, ?int $lookupId): bool
    {
        if ((int) $registration->academic_event_id !== (int) $event->id) {
            return false;
        }
        if ($user?->role === 'user' && (int) $registration->member_id === (int) $user->id) {
            return true;
        }

        return $lookupId !== null && $lookupId > 0 && (int) $registration->id === $lookupId;
    }

    public function markCancelRequested(AcademicEventRegistration $registration): void
    {
        if (in_array($registration->payment_status, ['cancel_requested', 'cancelled'], true)) {
            return;
        }

        $registration->update(['payment_status' => 'cancel_requested']);
    }

    public function registrationSummary(AcademicEventRegistration $registration): array
    {
        $subtotal = (int) ($registration->source_row_json['subtotal_amount'] ?? $registration->items->sum('price'));
        $discount = (int) ($registration->source_row_json['discount_amount'] ?? max(0, $subtotal - (int) $registration->total_amount));

        return [
            'item_names' => $registration->items->pluck('item_name')->filter()->implode(', '),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => (int) $registration->total_amount,
            'coupon_code' => (string) ($registration->source_row_json['coupon_code'] ?? ''),
        ];
    }

    /** @param Collection<int, PaymentPlan> $plans */
    public function totalForPlans(Collection $plans): int
    {
        return (int) $plans->sum(fn (PaymentPlan $plan) => (int) $plan->price_early);
    }

    /** @param Collection<int, PaymentPlan> $plans */
    public function resolveCoupon(?string $code, Collection $plans): ?array
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '' || $plans->isEmpty()) {
            return null;
        }

        $coupon = Coupon::query()
            ->with('paymentCategories')
            ->where('coupon_code', $code)
            ->where('status', 'ACTIVE')
            ->whereDate('valid_from', '<=', Carbon::today())
            ->whereDate('valid_to', '>=', Carbon::today())
            ->first();

        if (! $coupon) {
            return null;
        }

        $categories = $coupon->paymentCategories->pluck('payment_category')->all();
        if (! in_array('conference', $categories, true)) {
            return null;
        }

        $subtotal = $this->totalForPlans($plans);
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

    /** @param Collection<int, PaymentPlan> $plans */
    public function createBankTransferRegistration(AcademicEvent $event, User $user, Collection $plans, array $data): AcademicEventRegistration
    {
        $subtotal = $this->totalForPlans($plans);
        $couponResult = $this->resolveCoupon($data['coupon_code'] ?? null, $plans);
        $discount = (int) ($couponResult['discount'] ?? 0);
        $coupon = $couponResult['coupon'] ?? null;
        $finalAmount = max(0, $subtotal - $discount);

        return DB::transaction(function () use ($event, $user, $plans, $data, $subtotal, $discount, $coupon, $finalAmount) {
            $registration = AcademicEventRegistration::query()->create([
                'registration_no' => $this->registrationService->nextRegistrationNo($event),
                'academic_event_id' => $event->id,
                'member_id' => $user->id,
                'name' => (string) $user->name,
                'license_no' => $user->license_number,
                'phone' => preg_replace('/\D+/', '', (string) ($data['phone'] ?? $user->phone_number)),
                'email' => (string) ($data['email'] ?? $user->email),
                'reg_type' => 'pre',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'total_amount' => $finalAmount,
                'registered_at' => now(),
                'applied_at' => now(),
                'bank_depositor' => $data['bank_depositor'] ?? null,
                'bank_deposit_date' => $data['bank_deposit_date'] ?? null,
                'bank_account_text' => $data['bank_account_text'] ?? null,
                'receipt_issue' => (string) ($data['receipt_issue'] ?? 'NO'),
                'receipt_type' => $data['receipt_type'] ?? null,
                'receipt_number' => $data['receipt_number'] ?? null,
                'source_row_json' => [
                    'subtotal_amount' => $subtotal,
                    'coupon_code' => $coupon?->coupon_code,
                    'coupon_name' => $coupon?->coupon_name,
                    'discount_amount' => $discount,
                ],
            ]);

            $items = $plans->map(fn (PaymentPlan $plan) => [
                'payment_plan_id' => $plan->id,
                'item_name' => $plan->plan_name,
                'category' => $plan->category,
                'member_scope' => $plan->member_status,
                'price' => (int) $plan->price_early,
            ])->all();
            $this->registrationService->syncItems($registration, $items);

            if ($coupon) {
                $coupon->increment('usage_count');
            }

            return $registration;
        });
    }

    /** @param Collection<int, PaymentPlan> $plans */
    public function createBankTransferNonMemberRegistration(AcademicEvent $event, Collection $plans, array $data): AcademicEventRegistration
    {
        $subtotal = $this->totalForPlans($plans);
        $couponResult = $this->resolveCoupon($data['coupon_code'] ?? null, $plans);
        $discount = (int) ($couponResult['discount'] ?? 0);
        $coupon = $couponResult['coupon'] ?? null;
        $finalAmount = max(0, $subtotal - $discount);

        return DB::transaction(function () use ($event, $plans, $data, $subtotal, $discount, $coupon, $finalAmount) {
            $registration = AcademicEventRegistration::query()->create([
                'registration_no' => $this->registrationService->nextRegistrationNo($event),
                'academic_event_id' => $event->id,
                'member_id' => null,
                'name' => (string) $data['name'],
                'license_no' => $data['license_no'] ?? null,
                'phone' => preg_replace('/\D+/', '', (string) ($data['phone'] ?? '')),
                'email' => (string) ($data['email'] ?? ''),
                'reg_type' => 'pre',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'total_amount' => $finalAmount,
                'registered_at' => now(),
                'applied_at' => now(),
                'bank_depositor' => $data['bank_depositor'] ?? null,
                'bank_deposit_date' => $data['bank_deposit_date'] ?? null,
                'bank_account_text' => $data['bank_account_text'] ?? null,
                'receipt_issue' => (string) ($data['receipt_issue'] ?? 'NO'),
                'receipt_type' => $data['receipt_type'] ?? null,
                'receipt_number' => $data['receipt_number'] ?? null,
                'source_row_json' => [
                    'registration_scope' => 'non-member',
                    'name_en' => $data['name_en'] ?? null,
                    'major_subject' => $data['major_subject'] ?? null,
                    'affiliated_hospital' => $data['affiliated_hospital'] ?? null,
                    'address_postcode' => $data['address_postcode'] ?? null,
                    'address_base' => $data['address_base'] ?? null,
                    'address_detail' => $data['address_detail'] ?? null,
                    'subtotal_amount' => $subtotal,
                    'coupon_code' => $coupon?->coupon_code,
                    'coupon_name' => $coupon?->coupon_name,
                    'discount_amount' => $discount,
                ],
            ]);

            $items = $plans->map(fn (PaymentPlan $plan) => [
                'payment_plan_id' => $plan->id,
                'item_name' => $plan->plan_name,
                'category' => $plan->category,
                'member_scope' => $plan->member_status,
                'price' => (int) $plan->price_early,
            ])->all();
            $this->registrationService->syncItems($registration, $items);

            if ($coupon) {
                $coupon->increment('usage_count');
            }

            return $registration;
        });
    }

    private function isExecutive(User $user): bool
    {
        $today = Carbon::today();

        return MemberExecutive::query()
            ->where('member_id', $user->id)
            ->where('is_active', true)
            ->whereDate('term_start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->where('is_indefinite', true)
                    ->orWhereDate('term_end_date', '>=', $today);
            })
            ->exists();
    }
}

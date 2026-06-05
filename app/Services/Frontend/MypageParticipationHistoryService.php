<?php

namespace App\Services\Frontend;

use App\Models\AcademicEventRegistration;
use App\Models\EduTrainingPayment;
use App\Models\User;
use App\Services\Backoffice\MembershipPaymentService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class MypageParticipationHistoryService
{
    public function paginate(User $user, Request $request): LengthAwarePaginator
    {
        $perPage = 20;
        $registrations = AcademicEventRegistration::query()
            ->with('event')
            ->where('member_id', $user->id);

        if ($request->filled('year')) {
            $year = (int) $request->get('year');
            $registrations->whereYear('registered_at', $year);
        }
        if ($request->filled('month')) {
            $month = (int) $request->get('month');
            $registrations->whereMonth('registered_at', $month);
        }

        $trainingPayments = EduTrainingPayment::query()
            ->with('training')
            ->where('member_id', $user->id);

        if ($request->filled('year')) {
            $year = (int) $request->get('year');
            $trainingPayments->whereYear('registered_at', $year);
        }
        if ($request->filled('month')) {
            $month = (int) $request->get('month');
            $trainingPayments->whereMonth('registered_at', $month);
        }

        $rows = collect($registrations->get()
            ->map(fn (AcademicEventRegistration $registration): object => $this->registrationRow($registration))
            ->all())
            ->merge($trainingPayments->get()->map(fn (EduTrainingPayment $payment): object => $this->trainingPaymentRow($payment))->all())
            ->sortByDesc(fn (object $row): int => $row->registered_at instanceof Carbon ? $row->registered_at->timestamp : 0)
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();

        return (new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        ))->withQueryString();
    }

    public function findForMember(User $user, int $id): ?AcademicEventRegistration
    {
        return AcademicEventRegistration::query()
            ->with(['event', 'items', 'member'])
            ->where('member_id', $user->id)
            ->whereKey($id)
            ->first();
    }

    /** @return array<string, string> */
    public function paymentStatusLabels(): array
    {
        return [
            'pending_payment' => '결제대기',
            'pending' => '입금대기',
            'completed' => '등록완료',
            'cancel_requested' => '취소요청',
            'cancelled' => '취소',
        ];
    }

    /** @return array<string, string> */
    public function paymentMethodLabels(): array
    {
        return array_merge(MembershipPaymentService::paymentMethodLabels(), [
            'bank' => '무통장입금',
            'bank_transfer' => '무통장입금',
        ]);
    }

    private function registrationRow(AcademicEventRegistration $registration): object
    {
        return (object) [
            'type' => 'academic_event',
            'id' => $registration->id,
            'title' => $registration->event?->title ?? '-',
            'credit' => '-',
            'total_amount' => (int) $registration->total_amount,
            'payment_method' => $registration->payment_method,
            'payment_status' => $registration->payment_status,
            'registered_at' => $registration->registered_at,
            'paid_at' => $registration->paid_at,
            'participation_print_url' => route('mypage.print_participation', ['registration_id' => $registration->id]),
            'receipt_url' => route('mypage.print_receipt_save', ['registration_id' => $registration->id]),
            'detail_url' => route('mypage.participation_history_view', ['id' => $registration->id]),
        ];
    }

    private function trainingPaymentRow(EduTrainingPayment $payment): object
    {
        return (object) [
            'type' => 'training_course',
            'id' => $payment->id,
            'title' => $payment->training?->title ?? '-',
            'credit' => '-',
            'total_amount' => (int) $payment->total_amount,
            'payment_method' => $payment->payment_method,
            'payment_status' => $payment->payment_status,
            'registered_at' => $payment->registered_at,
            'paid_at' => $payment->paid_at,
            'participation_print_url' => null,
            'receipt_url' => route('mypage.print_receipt_save', ['training_payment_id' => $payment->id]),
            'detail_url' => route('academic_event.training_course_end', ['payment' => $payment->id]),
        ];
    }
}

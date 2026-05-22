<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Models\MemberExecutive;
use App\Services\Backoffice\MemberService;
use App\Services\Frontend\MypagePrintService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrintController extends Controller
{
    use RendersMypageViews;

    public function __construct(
        private readonly MypagePrintService $printService,
    ) {}

    public function receipt(Request $request): View
    {
        $paymentId = $request->filled('payment_id') ? (int) $request->get('payment_id') : null;
        $payment = $this->printService->membershipReceipt($this->currentMember(), $paymentId);
        abort_if($payment === null, 404);

        return $this->renderPrint('print_receipt', '영수증', 'print_receipt', [
            'payment' => $payment,
            'methodLabels' => $this->printService->paymentMethodLabels(),
        ]);
    }

    public function receiptSave(Request $request): View
    {
        $enrollmentId = (int) $request->query('enrollment_id', 0);
        if ($enrollmentId > 0) {
            $enrollment = $this->printService->courseReceipt($this->currentMember(), $enrollmentId);
            abort_if($enrollment === null, 404);

            return $this->renderPrint('print_receipt_save', '영수증', 'print_receipt_save', [
                'enrollment' => $enrollment,
                'methodLabels' => $this->printService->paymentMethodLabels(),
            ]);
        }

        $registrationId = (int) $request->query('registration_id', 0);
        $registration = $this->printService->registrationReceipt($this->currentMember(), $registrationId);
        abort_if($registration === null, 404);

        return $this->renderPrint('print_receipt_save', '영수증', 'print_receipt_save', [
            'registration' => $registration,
            'methodLabels' => $this->printService->paymentMethodLabels(),
        ]);
    }

    public function participation(Request $request): View
    {
        $registrationId = (int) $request->query('registration_id', 0);
        $registration = $this->printService->participationCertificate($this->currentMember(), $registrationId);
        abort_if($registration === null, 404);

        return $this->renderPrint('print_participation', '참가증명서', 'print_participation', [
            'registration' => $registration,
            'user' => $this->currentMember(),
        ]);
    }

    public function completion(Request $request): View
    {
        $enrollmentId = (int) $request->query('enrollment_id', 0);
        $enrollment = $this->printService->courseCompletion($this->currentMember(), $enrollmentId);
        abort_if($enrollment === null, 404);

        return $this->renderPrint('print_completion', '이수증', 'print_completion', [
            'enrollment' => $enrollment,
            'user' => $this->currentMember(),
        ]);
    }

    public function letterAppointment(Request $request): View
    {
        $executiveId = (int) $request->query('executive_id', 0);
        $executive = $this->printService->executiveAppointment($this->currentMember(), $executiveId);
        abort_if($executive === null, 404);

        return $this->renderPrint('print_letter_appointment', '임명장', 'print_letter_appointment', [
            'executive' => $executive,
            'user' => $this->currentMember(),
            'roleLabels' => MemberExecutive::roleLabels(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $with
     */
    private function renderPrint(string $view, string $gName, string $slug, array $with = []): View
    {
        return view('mypage.'.$view, array_merge([
            'page_type' => 'professional',
            'gNum' => 'print',
            'sNum' => '00',
            'gName' => $gName,
            'sName' => $gName,
            'gSlug' => $slug,
        ], $with));
    }
}

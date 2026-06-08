<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\EduTraining;
use App\Models\EduTrainingAttachment;
use App\Services\Frontend\PublicAcademicEventService;
use App\Services\Frontend\PublicBoardService;
use App\Services\Frontend\PublicTrainingCourseService;
use App\Support\BackofficeFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcademicEventController extends Controller
{
    public function __construct(
        private readonly PublicAcademicEventService $academicEventService,
        private readonly PublicTrainingCourseService $trainingCourseService,
        private readonly PublicBoardService $publicBoardService,
    ) {}

	public function annualSchedule(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '01';
        $gName = '학술행사';
        $sName = 'KIFM 연간일정';
        $geName = 'Academic Event';
        $gSlug = 'annual_schedule';
        $annualSchedules = $this->academicEventService->annualCalendarSchedules();
        $initialYear = (int) now()->format('Y');
        $initialMonth = (int) now()->format('n');

        return view('academic_event.annual_schedule', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'annualSchedules',
            'initialYear',
            'initialMonth',
        ));
    }

	public function academicHistory(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '02';
        $gName = '학술행사';
        $sName = '학술대회 연혁';
        $geName = 'Academic Event';
        $gSlug = 'academic_history';
        $histories = $this->publicBoardService->listAcademicConferenceHistory($request);
        $filters = [
            'search_type' => (string) $request->query('search_type', 'all'),
            'keyword' => trim((string) $request->query('keyword', '')),
        ];

        return view('academic_event.academic_history', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'histories', 'filters'));
    }

    public function conference(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '03';
        $gName = '학술행사';
        $sName = '학술대회';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_conference';
        $featuredConference = $this->academicEventService->featuredConference();
        $conferences = $this->academicEventService->paginateConferences($request);
        $statusLabels = $this->academicEventService->statusLabels();
        $yearOptions = $this->academicEventService->yearOptions();
        $filters = $this->academicEventService->filters($request);

        return view('academic_event.conference', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'featuredConference',
            'conferences',
            'statusLabels',
            'yearOptions',
            'filters',
        ));
    }

    public function conferenceView(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '03';
        $gName = '학술행사';
        $sName = '학술대회';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_conference_view';

        return view('academic_event.conference_view', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function trainingCourse(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '04';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course';
        $featuredTraining = $this->trainingCourseService->featuredTraining();
        $trainings = $this->trainingCourseService->paginateVisible($request);
        $statusLabels = $this->trainingCourseService->statusLabels();
        $yearOptions = $this->trainingCourseService->yearOptions();
        $filters = $this->trainingCourseService->filters($request);

        return view('academic_event.training_course', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'featuredTraining',
            'trainings',
            'statusLabels',
            'yearOptions',
            'filters',
        ));
    }

    public function trainingCourseView(Request $request): View|RedirectResponse
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '04';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course_view';
        $training = $this->resolveTrainingFromRequest($request);
        if ($training instanceof RedirectResponse) {
            return $training;
        }
        $user = $this->frontendUser();
        $rounds = $this->trainingCourseService->publicRounds($training);
        $memberGrade = $this->trainingCourseService->memberGrade($user);

        return view('academic_event.training_course_view', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'training',
            'rounds',
            'user',
            'memberGrade',
        ));
    }

    public function trainingCoursePayment(Request $request): View|RedirectResponse
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '04';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course_payment';
        $training = $this->resolveTrainingFromRequest($request);
        if ($training instanceof RedirectResponse) {
            return $training;
        }
        $user = $this->frontendUser();
        $rounds = $this->trainingCourseService->publicRounds($training)
            ->filter(fn ($round): bool => $this->trainingCourseService->canApplyRound($round, $user))
            ->values();
        if ($rounds->isEmpty()) {
            return redirect()
                ->route('academic_event.training_course_view', ['training' => $training->id])
                ->with('alert', '현재 신청 가능한 결제 항목이 없습니다.');
        }
        $memberGrade = $this->trainingCourseService->memberGrade($user);

        return view('academic_event.training_course_payment', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'training',
            'rounds',
            'user',
            'memberGrade',
        ));
    }

    public function storeTrainingCoursePayment(Request $request): JsonResponse|RedirectResponse
    {
        $user = $this->frontendUser();
        if ($user) {
            $request->merge([
                'name' => (string) $user->name,
                'license_no' => $user->license_number,
                'phone' => $user->phone_number,
                'email' => $user->email,
            ]);
        }

        $validated = $request->validate([
            'training_id' => ['required', 'integer', 'exists:edu_trainings,id'],
            'round_ids' => ['required', 'array', 'min:1'],
            'round_ids.*' => ['integer', 'exists:edu_training_rounds,id'],
            'name' => ['required', 'string', 'max:100'],
            'license_no' => ['nullable', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:100'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer'])],
            'bank_depositor' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'bank_deposit_date' => ['required_if:payment_method,bank_transfer', 'nullable', 'date'],
            'receipt_issue' => ['nullable', Rule::in(['YES', 'NO'])],
            'receipt_type' => ['required_if:receipt_issue,YES', 'nullable', Rule::in(['PERSONAL', 'CARD'])],
            'receipt_number' => ['required_if:receipt_issue,YES', 'nullable', 'string', 'max:100'],
            'refund_bank' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'refund_account' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'refund_holder' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'terms_agree' => ['accepted'],
        ], [
            'round_ids.required' => '결제 항목을 선택해주세요.',
            'round_ids.min' => '결제 항목을 선택해주세요.',
            'name.required' => '이름을 입력해주세요.',
            'phone.required' => '휴대폰번호를 입력해주세요.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
            'payment_method.required' => '결제수단을 선택해주세요.',
            'payment_method.in' => '결제수단을 확인해주세요.',
            'bank_depositor.required_if' => '입금자명을 입력해주세요.',
            'bank_deposit_date.required_if' => '입금 예정일을 선택해주세요.',
            'bank_deposit_date.date' => '입금 예정일 형식을 확인해주세요.',
            'refund_bank.required_if' => '환불 은행을 선택해주세요.',
            'refund_account.required_if' => '환불 계좌번호를 입력해주세요.',
            'refund_holder.required_if' => '예금주명을 입력해주세요.',
            'receipt_type.required_if' => '현금영수증 발급 구분을 선택해주세요.',
            'receipt_number.required_if' => '현금영수증 번호를 입력해주세요.',
            'terms_agree.accepted' => '결제 이용 약관, 개인정보 처리 동의가 필요합니다.',
        ]);

        $training = $this->trainingCourseService->findVisible((int) $validated['training_id']);
        $validRoundIds = $this->trainingCourseService->publicRounds($training)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
        foreach ((array) $validated['round_ids'] as $roundId) {
            if (! in_array((int) $roundId, $validRoundIds, true)) {
                return back()->withInput()->withErrors(['round_ids' => '선택한 차수가 이 연수강좌에 속하지 않습니다.']);
            }
        }
        if (($validated['payment_method'] ?? '') === 'card' && ! $this->hasTossKeys()) {
            return response()->json([
                'success' => false,
                'message' => '토스페이먼츠 테스트 키가 설정되어 있지 않습니다.',
                'errors' => ['payment' => ['토스페이먼츠 테스트 키가 설정되어 있지 않습니다.']],
            ], 422);
        }

        try {
            $payment = $this->trainingCourseService->createPayment($training, $validated, $user);
        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['round_ids' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput()->withErrors(['round_ids' => $e->getMessage()]);
        }

        if (($validated['payment_method'] ?? '') === 'card' && (int) $payment->total_amount > 0) {
            return response()->json($this->trainingTossPaymentPayload($payment));
        }

        return redirect()->route('academic_event.training_course_end', ['payment' => $payment->id]);
    }

    public function confirmTrainingCourseTossPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paymentKey' => ['required', 'string'],
            'orderId' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $payment = $this->trainingCourseService->confirmTossPayment(
                $validated['orderId'],
                $validated['paymentKey'],
                (int) $validated['amount']
            );
        } catch (RuntimeException $e) {
            return redirect()->route('academic_event.training_course')
                ->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->route('academic_event.training_course_end', ['payment' => $payment->id])
            ->with('success', '토스페이먼츠 결제가 완료되었습니다.');
    }

    public function failTrainingCourseTossPayment(Request $request): RedirectResponse
    {
        $message = trim((string) $request->query('message'));

        return redirect()->route('academic_event.training_course')
            ->withErrors(['payment' => $message !== '' ? $message : '토스페이먼츠 결제가 취소되었거나 실패했습니다.']);
    }

    public function applyTrainingCourseCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'training_id' => ['required', 'integer', 'exists:edu_trainings,id'],
            'round_ids' => ['required', 'array', 'min:1'],
            'round_ids.*' => ['integer', 'exists:edu_training_rounds,id'],
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $training = $this->trainingCourseService->findVisible((int) $validated['training_id']);
        $summary = $this->trainingCourseService->selectedRoundSummary($training, array_map('intval', $validated['round_ids']), $this->frontendUser());
        $coupon = $this->trainingCourseService->resolveCoupon($validated['coupon_code'], (int) $summary['subtotal']);

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => '사용이 불가한 쿠폰입니다.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'discount' => (int) $coupon['discount'],
            'final_amount' => (int) $coupon['final_amount'],
        ]);
    }

    public function trainingCourseEnd(Request $request): View|RedirectResponse
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '04';
        $gName = '학술행사';
        $sName = '연수강좌';
        $geName = 'Academic Event';
        $gSlug = 'academic_event_training_course_end';
        $paymentId = (int) $request->query('payment', 0);
        if ($paymentId <= 0) {
            return redirect()->route('academic_event.training_course');
        }
        $payment = $this->trainingCourseService->findPayment($paymentId);
        $summary = $this->trainingCourseService->paymentSummary($payment);

        return view('academic_event.training_course_end', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'payment',
            'summary',
        ));
    }

    public function downloadTrainingTextbook(EduTraining $training): StreamedResponse
    {
        if (
            $training->status !== 'PUBLIC'
            || ! $training->textbook_file_path
            || ! $this->trainingCourseService->canDownloadTextbook($training, $this->frontendUser())
            || ! Storage::disk('public')->exists($training->textbook_file_path)
        ) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $training->textbook_file_path,
            BackofficeFile::displayName($training->textbook_file_path)
        );
    }

    public function downloadTrainingAttachment(EduTrainingAttachment $attachment): StreamedResponse
    {
        $attachment->loadMissing('training');
        if (
            $attachment->training?->status !== 'PUBLIC'
            || ! $this->trainingCourseService->canDownloadAttachment($attachment->training)
            || ! Storage::disk('public')->exists($attachment->file_path)
        ) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name ?: BackofficeFile::displayName($attachment->file_path)
        );
    }

    private function resolveTrainingFromRequest(Request $request): \App\Models\EduTraining|RedirectResponse
    {
        $trainingId = (int) $request->query('training', 0);
        if ($trainingId > 0) {
            return $this->trainingCourseService->findVisible($trainingId);
        }

        $training = $this->trainingCourseService->featuredTraining();
        if (! $training) {
            return redirect()->route('academic_event.training_course');
        }

        return redirect()->to($request->url() . '?training=' . $training->id);
    }

    private function frontendUser(): ?\App\Models\User
    {
        $user = Auth::user();

        return $user && $user->role === 'user' ? $user : null;
    }

    private function hasTossKeys(): bool
    {
        return (string) config('services.toss.client_key') !== ''
            && (string) config('services.toss.secret_key') !== '';
    }

    private function trainingTossPaymentPayload(\App\Models\EduTrainingPayment $payment): array
    {
        $payment->loadMissing(['items', 'member']);
        $itemNames = $payment->items->pluck('item_name')->filter()->implode(', ');

        return [
            'success' => true,
            'clientKey' => (string) config('services.toss.client_key'),
            'orderId' => $payment->order_no,
            'orderName' => mb_substr($itemNames ?: '연수강좌 결제', 0, 100),
            'amount' => (int) $payment->total_amount,
            'customerName' => $payment->name,
            'customerEmail' => $payment->email,
            'customerMobilePhone' => preg_replace('/\D+/', '', (string) $payment->phone),
            'customerKey' => $payment->member_id
                ? 'kifm-training-member-' . $payment->member_id
                : 'kifm-training-guest-' . $payment->id,
            'successUrl' => route('academic_event.training_course_payment.toss_success'),
            'failUrl' => route('academic_event.training_course_payment.toss_fail'),
        ];
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PublicAcademicEventService;
use App\Services\Frontend\PublicTrainingCourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicEventController extends Controller
{
    public function __construct(
        private readonly PublicAcademicEventService $academicEventService,
        private readonly PublicTrainingCourseService $trainingCourseService,
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
        return view('academic_event.annual_schedule', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
	
	public function academicHistory(): View
    {
        $page_type = 'professional';
        $gNum = '02';
        $sNum = '02';
        $gName = '학술행사';
        $sName = '학술대회 연혁';
        $geName = 'Academic Event';
        $gSlug = 'academic_history';
        return view('academic_event.academic_history', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
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
        $rounds = $this->trainingCourseService->publicRounds($training);
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

    public function storeTrainingCoursePayment(Request $request): RedirectResponse
    {
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
            'refund_bank' => ['nullable', 'string', 'max:100'],
            'refund_account' => ['nullable', 'string', 'max:100'],
            'refund_holder' => ['nullable', 'string', 'max:100'],
            'terms_agree' => ['accepted'],
        ], [
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

        try {
            $payment = $this->trainingCourseService->createPayment($training, $validated, $this->frontendUser());
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['round_ids' => $e->getMessage()]);
        }

        return redirect()->route('academic_event.training_course_end', ['payment' => $payment->id]);
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
                'message' => '사용 가능한 쿠폰이 아닙니다.',
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
}

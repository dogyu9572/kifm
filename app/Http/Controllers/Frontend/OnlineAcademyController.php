<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\EduCourse;
use App\Models\User;
use App\Services\Frontend\PublicOnlineAcademyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class OnlineAcademyController extends Controller
{
    public function __construct(
        private readonly PublicOnlineAcademyService $onlineAcademyService,
    ) {}

    public function index(Request $request): View
    {
        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '00';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $geName = 'Online Academy';
        $gSlug = 'online_academy';
        $courses = $this->onlineAcademyService->paginateVisible($request);
        $featuredCourses = $this->onlineAcademyService->featuredCourses();
        $courseTypeLabels = $this->onlineAcademyService->courseTypeLabels();
        $searchFieldLabels = $this->onlineAcademyService->searchFieldLabels();
        $yearOptions = $this->onlineAcademyService->yearOptions();
        $keywordOptions = $this->onlineAcademyService->keywordOptions();
        $filters = $this->onlineAcademyService->filters($request);

        return view('online_academy.index', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'courses',
            'featuredCourses',
            'courseTypeLabels',
            'searchFieldLabels',
            'yearOptions',
            'keywordOptions',
            'filters',
        ));
    }

    public function view(): RedirectResponse
    {
        $course = $this->onlineAcademyService->firstVisible();
        if ($course === null) {
            return redirect()->route('online_academy.index');
        }

        return redirect()->route('online_academy.show', $course);
    }

    public function show(EduCourse $course): View|RedirectResponse
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);
        $user = $this->frontendUser();

        if (! $this->onlineAcademyService->canViewCourse($course, $user)) {
            if ($user !== null && $this->onlineAcademyService->activeEnrollment($course, $user)) {
                return redirect()->route('online_academy.index', ['course_type' => $course->course_type])
                    ->with('alert', '이미 신청 접수된 강좌입니다. 결제 완료 후 수강이 가능합니다.');
            }

            return redirect()->route('online_academy.payment', ['course' => $course->id]);
        }

        $enrollment = $this->onlineAcademyService->completedEnrollment($course, $user);

        return $this->renderInner('view', 'online_academy_view', [
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }

    public function test(): View
    {
        return $this->renderInner('test', 'online_academy_test');
    }

    public function exam(Request $request, EduCourse $course): View|RedirectResponse
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);
        $user = $this->frontendUser();
        if (! $this->onlineAcademyService->canViewCourse($course, $user)) {
            if ($user !== null && $this->onlineAcademyService->activeEnrollment($course, $user)) {
                return redirect()->route('online_academy.index', ['course_type' => $course->course_type])
                    ->with('alert', '이미 신청 접수된 강좌입니다. 결제 완료 후 시험을 볼 수 있습니다.');
            }

            return redirect()->route('online_academy.payment', ['course' => $course->id]);
        }

        $enrollment = $this->onlineAcademyService->completedEnrollment($course, $user);
        if (($enrollment?->progress_rate ?? 0) < 100) {
            return redirect()->route('online_academy.show', $course)
                ->withErrors(['progress' => '수강률 100% 달성 후 시험을 볼 수 있습니다.']);
        }

        $examPage = $this->onlineAcademyService->examPageData($course, (int) $request->query('step', 1));

        return $this->renderInner('exam', 'online_academy_exam', array_merge([
            'course' => $course,
        ], $examPage));
    }

    public function submitExam(Request $request, EduCourse $course): RedirectResponse
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);
        $user = $this->frontendUser();
        if (! $this->onlineAcademyService->canViewCourse($course, $user)) {
            return redirect()->route('online_academy.index', ['course_type' => $course->course_type])
                ->with('alert', '결제 완료 후 시험을 볼 수 있습니다.');
        }

        $step = max(1, (int) $request->input('step', 1));
        $total = $course->examQuestions->count();
        $request->validate([
            'answer' => ['required', 'integer', 'min:0'],
        ], [
            'answer.required' => '정답을 선택해주세요.',
        ]);

        $sessionKey = 'online_academy_exam_answers.' . $course->id;
        $answers = $request->session()->get($sessionKey, []);
        if (! is_array($answers)) {
            $answers = [];
        }
        $answers[$step] = (int) $request->input('answer');
        $request->session()->put($sessionKey, $answers);

        if ($step < $total) {
            return redirect()->route('online_academy.exam', [
                'course' => $course->id,
                'step' => $step + 1,
            ]);
        }

        $result = $this->onlineAcademyService->gradeExam($course, $user, $answers);
        $request->session()->forget($sessionKey);
        $request->session()->put('online_academy_exam_result.' . $course->id, $result);

        return redirect()->route('online_academy.end', ['course' => $course->id]);
    }

    public function storeProgress(Request $request, EduCourse $course): JsonResponse
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);
        $user = $this->frontendUser();

        if ($user === null || ! $this->onlineAcademyService->canViewCourse($course, $user)) {
            return response()->json([
                'success' => false,
                'message' => '수강 권한이 없습니다.',
            ], 403);
        }

        $validated = $request->validate([
            'current_time' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'numeric', 'min:0'],
            'ended' => ['nullable', 'boolean'],
        ]);

        try {
            $progress = $this->onlineAcademyService->updateProgress($course, $user, $validated);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json(array_merge(['success' => true], $progress));
    }

    public function end(Request $request): View|RedirectResponse
    {
        $courseId = (int) $request->query('course', 0);
        if ($courseId <= 0) {
            return redirect()->route('online_academy.index');
        }

        $course = $this->onlineAcademyService->findVisible($courseId);
        $user = $this->frontendUser();
        if (! $this->onlineAcademyService->canViewCourse($course, $user)) {
            return redirect()->route('online_academy.index', ['course_type' => $course->course_type])
                ->with('alert', '결제 완료 후 수강 완료 처리가 가능합니다.');
        }

        $enrollment = $this->onlineAcademyService->completedEnrollment($course, $user);
        $resultKey = 'online_academy_exam_result.' . $course->id;
        $result = $request->session()->get($resultKey);
        if (! is_array($result)) {
            $result = [
                'score' => (int) ($enrollment?->exam_score ?? 0),
                'correct' => 0,
                'total' => $course->examQuestions->count(),
                'passed' => ($enrollment?->exam_status ?? '') === 'passed',
            ];
        }

        return $this->renderInner('end', 'online_academy_end', [
            'course' => $course,
            'enrollment' => $enrollment,
            'result' => $result,
        ]);
    }

    private function renderInner(string $view, string $slug, array $data = []): View
    {
        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '01';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $gSlug = $slug;

        return view('online_academy.' . $view, array_merge(
            compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'),
            $data,
        ));
    }
    public function payment(Request $request): View|RedirectResponse
    {
        $courseId = (int) $request->query('course', 0);
        if ($courseId <= 0) {
            return redirect()->route('online_academy.index');
        }

        $course = $this->onlineAcademyService->findVisible($courseId);
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login', [
                'intended' => route('online_academy.payment', ['course' => $course->id], false),
            ]);
        }

        if ($this->onlineAcademyService->canViewCourse($course, $user)) {
            return redirect()->route('online_academy.show', $course);
        }

        if ($this->onlineAcademyService->activeEnrollment($course, $user)) {
            return redirect()->route('online_academy.index', ['course_type' => $course->course_type])
                ->with('alert', '이미 신청 접수된 강좌입니다. 입금 확인 후 수강이 가능합니다.');
        }

        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '00';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $gSlug = 'online_academy';
        $pricing = $this->onlineAcademyService->priceForUser($course, $user);
        $enrollment = $this->onlineAcademyService->activeEnrollment($course, $user);

        return view('online_academy.payment', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'gSlug',
            'course',
            'pricing',
            'enrollment',
        ));
    }

    public function storePayment(Request $request): RedirectResponse
    {
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login', [
                'intended' => route('online_academy.payment', ['course' => $request->input('course_id')], false),
            ]);
        }

        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:edu_courses,id'],
            'terms_agree' => ['accepted'],
        ], [
            'terms_agree.accepted' => '결제 이용 약관, 개인정보 처리 동의가 필요합니다.',
        ], [
            'terms_agree' => '결제 이용 약관, 개인정보 처리 동의',
        ]);

        $course = $this->onlineAcademyService->findVisible((int) $validated['course_id']);
        $pricing = $this->onlineAcademyService->priceForUser($course, $user);
        if (! $pricing['eligible']) {
            return back()->withInput()->withErrors(['course_id' => $pricing['message']]);
        }

        if ((int) ($pricing['price'] ?? 0) <= 0) {
            try {
                $enrollment = $this->onlineAcademyService->createOrRefreshEnrollmentWithPayment($course, $user, [
                    'payment_method' => 'card',
                ]);
            } catch (\RuntimeException $e) {
                return back()->withInput()->withErrors(['course_id' => $e->getMessage()]);
            }

            return redirect()->route('online_academy.payment.end', ['enrollment' => $enrollment->id]);
        }

        return redirect()->route('online_academy.payment.checkout', ['course' => $course->id]);
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $courseId = (int) $request->query('course', 0);
        if ($courseId <= 0) {
            return redirect()->route('online_academy.index');
        }

        $course = $this->onlineAcademyService->findVisible($courseId);
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login', [
                'intended' => route('online_academy.payment.checkout', ['course' => $course->id], false),
            ]);
        }

        if ($this->onlineAcademyService->canViewCourse($course, $user)) {
            return redirect()->route('online_academy.show', $course);
        }

        $pricing = $this->onlineAcademyService->priceForUser($course, $user);
        if (! $pricing['eligible']) {
            return redirect()->route('online_academy.payment', ['course' => $course->id])
                ->withErrors(['course_id' => $pricing['message']]);
        }

        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '00';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $gSlug = 'online_academy';

        return view('online_academy.checkout', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'gSlug',
            'course',
            'pricing',
            'user',
        ));
    }

    public function csrfToken(Request $request): JsonResponse
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }

    public function completePayment(Request $request): JsonResponse|RedirectResponse
    {
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login');
        }

        $this->normalizePaymentMethod($request);

        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:edu_courses,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method_display' => ['nullable', 'in:card,bank'],
            'payment_method' => ['required', 'in:card,bank_transfer'],
            'bank_depositor' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'bank_deposit_date' => ['required_if:payment_method,bank_transfer', 'nullable', 'date'],
            'bank_account_text' => ['nullable', 'string', 'max:200'],
            'receipt_issue' => ['nullable', 'in:YES,NO'],
            'receipt_type' => ['required_if:receipt_issue,YES', 'nullable', 'in:PERSONAL,CARD'],
            'receipt_number' => ['required_if:receipt_issue,YES', 'nullable', 'string', 'max:100'],
            'terms_agree' => ['accepted'],
        ], [
            'terms_agree.accepted' => '결제 이용 약관, 개인정보 처리 동의가 필요합니다.',
        ]);

        $course = $this->onlineAcademyService->findVisible((int) $validated['course_id']);
        if (($validated['payment_method'] ?? '') === 'card' && ! $this->hasTossKeys()) {
            return response()->json([
                'success' => false,
                'message' => '토스페이먼츠 테스트 키가 설정되어 있지 않습니다.',
                'errors' => ['payment' => ['토스페이먼츠 테스트 키가 설정되어 있지 않습니다.']],
            ], 422);
        }

        try {
            $enrollment = $this->onlineAcademyService->createOrRefreshEnrollmentWithPayment($course, $user, $validated);
        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['course_id' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput()->withErrors(['course_id' => $e->getMessage()]);
        }

        if (($validated['payment_method'] ?? '') === 'card' && (int) $enrollment->payment_amount > 0) {
            return response()->json($this->tossPaymentPayload($enrollment));
        }

        return redirect()->route('online_academy.payment.end', ['enrollment' => $enrollment->id]);
    }

    public function confirmTossPayment(Request $request): RedirectResponse
    {
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login');
        }

        $validated = $request->validate([
            'paymentKey' => ['required', 'string'],
            'orderId' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $enrollment = $this->onlineAcademyService->confirmTossPayment(
                $validated['orderId'],
                $validated['paymentKey'],
                (int) $validated['amount'],
                $user
            );
        } catch (RuntimeException $e) {
            return redirect()->route('online_academy.index')
                ->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->route('online_academy.payment.end', ['enrollment' => $enrollment->id])
            ->with('success', '토스페이먼츠 결제가 완료되었습니다.');
    }

    public function failTossPayment(Request $request): RedirectResponse
    {
        $message = trim((string) $request->query('message'));

        return redirect()->route('online_academy.index')
            ->withErrors(['payment' => $message !== '' ? $message : '토스페이먼츠 결제가 취소되었거나 실패했습니다.']);
    }

    public function paymentEnd(Request $request): View|RedirectResponse
    {
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login');
        }

        $enrollmentId = (int) $request->query('enrollment', 0);
        $enrollment = \App\Models\EduCourseEnrollment::query()
            ->with('course')
            ->where('member_id', $user->id)
            ->whereKey($enrollmentId)
            ->firstOrFail();
        $course = $enrollment->course;
        abort_if($course === null, 404);

        $page_type = 'online_academy';
        $gNum = 'online_academy';
        $sNum = '00';
        $gName = '온라인 아카데미';
        $sName = '온라인 아카데미';
        $gSlug = 'online_academy';
        $summary = $this->onlineAcademyService->paymentSummary($course, $user, $enrollment);

        return view('online_academy.payment_end', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'gSlug',
            'course',
            'enrollment',
            'summary',
            'user',
        ));
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:edu_courses,id'],
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $user = $this->frontendUser();
        if ($user === null) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        $course = $this->onlineAcademyService->findVisible((int) $validated['course_id']);
        $pricing = $this->onlineAcademyService->priceForUser($course, $user);
        $coupon = $this->onlineAcademyService->resolveCoupon($validated['coupon_code'], (int) $pricing['price']);

        if (! $coupon) {
            return response()->json(['success' => false, 'message' => '사용 가능한 쿠폰이 아닙니다.'], 422);
        }

        return response()->json([
            'success' => true,
            'discount' => $coupon['discount'],
            'final_amount' => $coupon['final_amount'],
        ]);
    }

    private function frontendUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User && $user->role === 'user' ? $user : null;
    }

    private function hasTossKeys(): bool
    {
        return (string) config('services.toss.client_key') !== ''
            && (string) config('services.toss.secret_key') !== '';
    }

    private function normalizePaymentMethod(Request $request): void
    {
        $displayMethod = (string) $request->input('payment_method_display', '');
        if ($displayMethod === 'card') {
            $request->merge(['payment_method' => 'card']);
            return;
        }

        if ($displayMethod === 'bank') {
            $request->merge(['payment_method' => 'bank_transfer']);
        }
    }

    private function tossPaymentPayload(\App\Models\EduCourseEnrollment $enrollment): array
    {
        $enrollment->loadMissing('member');

        return [
            'success' => true,
            'clientKey' => (string) config('services.toss.client_key'),
            'orderId' => $enrollment->payment_no,
            'orderName' => mb_substr((string) $enrollment->payment_item_name, 0, 100),
            'amount' => (int) $enrollment->payment_amount,
            'customerName' => $enrollment->member_name,
            'customerEmail' => $enrollment->member?->email,
            'customerMobilePhone' => preg_replace('/\D+/', '', (string) $enrollment->member?->phone_number),
            'customerKey' => 'kifm-online-member-' . $enrollment->member_id,
            'successUrl' => route('online_academy.payment.toss_success'),
            'failUrl' => route('online_academy.payment.toss_fail'),
        ];
    }
}

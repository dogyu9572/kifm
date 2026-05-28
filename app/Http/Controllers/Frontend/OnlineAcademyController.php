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
            return redirect()->route('online_academy.payment', ['course' => $course->id]);
        }

        return $this->renderInner('view', 'online_academy_view', [
            'course' => $course,
        ]);
    }

    public function test(): View
    {
        return $this->renderInner('test', 'online_academy_test');
    }

    public function exam(EduCourse $course): View|RedirectResponse
    {
        $course = $this->onlineAcademyService->findVisible((int) $course->id);
        $user = $this->frontendUser();
        if (! $this->onlineAcademyService->canViewCourse($course, $user)) {
            return redirect()->route('online_academy.payment', ['course' => $course->id]);
        }

        $examPage = $this->onlineAcademyService->examPageData($course);

        return $this->renderInner('exam', 'online_academy_exam', array_merge([
            'course' => $course,
        ], $examPage));
    }

    public function end(): View
    {
        return $this->renderInner('end', 'online_academy_end');
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
        if (! $this->onlineAcademyService->priceForUser($course, $user)['eligible']) {
            return back()->withInput()->withErrors(['course_id' => '현재 회원 등급으로 신청할 수 없는 강좌입니다.']);
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

    public function completePayment(Request $request): RedirectResponse
    {
        $user = $this->frontendUser();
        if ($user === null) {
            return redirect()->route('member.login');
        }

        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:edu_courses,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
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

        try {
            $enrollment = $this->onlineAcademyService->createOrRefreshEnrollmentWithPayment($course, $user, $validated);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['course_id' => $e->getMessage()]);
        }

        return redirect()->route('online_academy.payment.end', ['enrollment' => $enrollment->id]);
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
}

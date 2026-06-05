<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\AcademicConferenceAbstractRequest;
use App\Http\Requests\Frontend\AcademicConferenceAbstractUpdateRequest;
use App\Http\Requests\Frontend\AcademicConferenceNonMemberAbstractLookupRequest;
use App\Http\Requests\Frontend\AcademicConferenceNonMemberAbstractRequest;
use App\Http\Requests\Frontend\AcademicConferenceNonMemberRegistrationRequest;
use App\Http\Requests\Frontend\AcademicConferenceRegistrationRequest;
use App\Models\AcademicEvent;
use App\Models\AcademicEventAbstract;
use App\Models\AcademicEventRegistration;
use App\Services\Frontend\PublicAcademicConferenceAbstractService;
use App\Services\Frontend\PublicAcademicConferenceService;
use App\Services\Frontend\PublicAcademicConferenceRegistrationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcademicConferenceSiteController extends Controller
{
    public function __construct(
        private readonly PublicAcademicConferenceService $conferenceService,
        private readonly PublicAcademicConferenceRegistrationService $registrationService,
        private readonly PublicAcademicConferenceAbstractService $abstractService,
    ) {}

    public function show(Request $request, string $folderName, ?string $pagePath = null): View|RedirectResponse
    {
        try {
            $event = $this->conferenceService->findPublicEventByFolder($folderName);
            $page = $this->conferenceService->pageData($event, $pagePath);
        } catch (ModelNotFoundException) {
            return redirect()->to($this->blockedRedirectUrl($request))
                ->with('alert', '접근할 수 없는 학술대회입니다.');
        } catch (NotFoundHttpException) {
            return redirect()->to($this->blockedRedirectUrl($request))
                ->with('alert', '접근할 수 없는 학술대회입니다.');
        }

        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);
        $normalizedPagePath = trim((string) $pagePath, '/');

        if ($this->isFrontendMemberLoggedIn() && $normalizedPagePath === 'registration/reg') {
            return redirect()->to($conferenceBaseUrl . '/registration/form');
        }
        if ($this->isFrontendMemberLoggedIn() && $normalizedPagePath === 'registration/check_member') {
            return redirect()->to($conferenceBaseUrl . '/registration/result');
        }
        if (! $this->isFrontendMemberLoggedIn() && $normalizedPagePath === 'registration/form') {
            return redirect()->route('member.login', ['intended' => $conferenceBaseUrl . '/registration/form']);
        }
        if ($this->isFrontendMemberLoggedIn() && $normalizedPagePath === 'abstract/submission') {
            return redirect()->to($conferenceBaseUrl . '/abstract/form_member');
        }
        if ($this->isFrontendMemberLoggedIn() && $normalizedPagePath === 'abstract/check_member') {
            return redirect()->to($conferenceBaseUrl . '/abstract/result');
        }
        if ($normalizedPagePath === 'abstract/check') {
            return redirect()->to($conferenceBaseUrl . '/abstract/check_member');
        }
        if ($normalizedPagePath === 'abstract/modify') {
            return redirect()->to($conferenceBaseUrl . '/abstract/result');
        }
        if (! $this->isFrontendMemberLoggedIn() && $normalizedPagePath === 'abstract/form_member') {
            return redirect()->route('member.login', ['intended' => $conferenceBaseUrl . '/abstract/form_member']);
        }

        $view = $page['view'];
        $page_type = $page['page_type'];
        $gNum = $page['gNum'];
        $sNum = $page['sNum'] ?? null;
        $gName = $page['gName'];
        $sName = $page['sName'];
        $gSlug = $page['gSlug'];
        $currentMember = $this->isFrontendMemberLoggedIn() ? auth()->user() : null;
        if ($currentMember && in_array($normalizedPagePath, ['registration/reg', 'registration/form'], true) && $this->registrationService->hasActiveMemberRegistration($event, $currentMember)) {
            return redirect()->to($conferenceBaseUrl . '/registration/result')
                ->with('alert', '이미 사전등록 신청 내역이 있습니다.');
        }
        $membershipPlans = $currentMember && $normalizedPagePath === 'registration/form'
            ? $this->registrationService->membershipPlansForUser($currentMember)
            : collect();
        $paymentPlans = match (true) {
            $currentMember && $normalizedPagePath === 'registration/form' => $this->registrationService->candidateConferencePlans($currentMember),
            $normalizedPagePath === 'registration/form_non_member' => $this->registrationService->nonMemberConferencePlans(),
            default => collect(),
        };
        $showMembershipFeeNotice = $currentMember && $normalizedPagePath === 'registration/form'
            ? $this->registrationService->shouldShowMembershipFeeNotice($currentMember)
            : false;
        $registration = null;
        $registrationSummary = null;
        if ($normalizedPagePath === 'registration/end') {
            $registration = $currentMember
                ? $this->registrationService->findMemberRegistration(
                    $event,
                    $currentMember,
                    (int) session('academic_conference_registration_id')
                )
                : $this->registrationService->findNonMemberRegistrationById(
                    $event,
                    (int) session('academic_conference_registration_id')
                );
            $registrationSummary = $registration
                ? $this->registrationService->registrationSummary($registration)
                : null;
        }
        if ($normalizedPagePath === 'registration/result') {
            $lookupRegistrationId = $currentMember
                ? null
                : (int) session('academic_conference_registration_lookup_id');
            $registration = $this->registrationService->findRegistrationForLookup(
                $event,
                $currentMember,
                $lookupRegistrationId
            );
            $registrationSummary = $registration
                ? $this->registrationService->registrationSummary($registration)
                : null;
        }
        $notices = null;
        $notice = null;
        $noticeAttachments = [];
        $prevNotice = null;
        $nextNotice = null;
        $sponsorGroups = null;
        $abstractPresentationTypes = $this->abstractService->activePresentationTypeLabels($event);
        $abstractFields = $event->fields;
        $abstractBookUrl = $event->abstract_book_path ? $this->conferenceService->optionalImageUrl($event->abstract_book_path) : null;
        $abstractSubmission = null;
        $abstractSummary = null;
        $memberAbstracts = collect();
        $hasMemberAbstractSubmission = false;
        if ($normalizedPagePath === 'notice') {
            $notices = $this->conferenceService->notices($event, $request);
        }
        if ($normalizedPagePath === 'notice/view') {
            $noticeId = $request->integer('id') ?: null;
            $notice = $this->conferenceService->notice($event, $noticeId);
            abort_if($noticeId !== null && $notice === null, 404);

            if ($notice !== null) {
                $noticeAttachments = $this->conferenceService->noticeAttachments($notice->attachments ?? null);
                ['prev' => $prevNotice, 'next' => $nextNotice] = $this->conferenceService->noticePrevNext($event, (int) $notice->id);
            }
        }
        if ($normalizedPagePath === 'sponsors') {
            $sponsorGroups = $this->conferenceService->sponsorGroups($event);
        }
        if (in_array($normalizedPagePath, ['abstract/complete', 'abstract/result'], true)) {
            $lookupAbstractId = $currentMember
                ? null
                : (int) session('academic_conference_abstract_lookup_id');
            $abstractSubmission = $this->abstractService->findForLookup($event, $currentMember, $lookupAbstractId);
            $abstractSummary = $this->abstractService->abstractSummary($abstractSubmission);
            $memberAbstracts = $currentMember
                ? $this->abstractService->memberAbstracts($event, $currentMember)
                : collect();
            $hasMemberAbstractSubmission = $memberAbstracts->isNotEmpty();
        }

        return view($view, compact(
            'event',
            'currentMember',
            'paymentPlans',
            'membershipPlans',
            'showMembershipFeeNotice',
            'registration',
            'registrationSummary',
            'notices',
            'notice',
            'noticeAttachments',
            'prevNotice',
            'nextNotice',
            'sponsorGroups',
            'abstractPresentationTypes',
            'abstractFields',
            'abstractBookUrl',
            'abstractSubmission',
            'abstractSummary',
            'memberAbstracts',
            'hasMemberAbstractSubmission',
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'gSlug',
            'conferenceBaseUrl',
        ));
    }

    public function storeMemberAbstract(AcademicConferenceAbstractRequest $request, string $folderName): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);
        $user = auth()->user();
        abort_unless($user?->role === 'user', 403);

        if (! $this->abstractService->canSubmit($event)) {
            return back()
                ->withInput()
                ->withErrors(['submission' => '초록 제출 기간이 종료되었습니다.']);
        }

        if (! $this->fieldBelongsToEvent($event, $request->input('academic_event_field_id'))) {
            return back()
                ->withInput()
                ->withErrors(['academic_event_field_id' => '현재 행사에 등록된 발표 분야를 선택해주세요.']);
        }

        $abstract = $this->abstractService->createForMember(
            $event,
            $user,
            $request->validated(),
            (array) $request->file('attachments', [])
        );

        return redirect()->to($conferenceBaseUrl . '/abstract/complete')
            ->with('academic_conference_abstract_id', $abstract->id)
            ->with('success', '초록 제출이 완료되었습니다.');
    }

    public function storeNonMemberAbstract(AcademicConferenceNonMemberAbstractRequest $request, string $folderName): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);

        if (! $this->abstractService->canSubmit($event)) {
            return back()
                ->withInput()
                ->withErrors(['submission' => '초록 제출 기간이 종료되었습니다.']);
        }

        if (! $this->fieldBelongsToEvent($event, $request->input('academic_event_field_id'))) {
            return back()
                ->withInput()
                ->withErrors(['academic_event_field_id' => '현재 행사에 등록된 발표 분야를 선택해주세요.']);
        }

        $abstract = $this->abstractService->createForNonMember(
            $event,
            $request->validated(),
            (array) $request->file('attachments', [])
        );

        return redirect()->to($conferenceBaseUrl . '/abstract/complete')
            ->with('academic_conference_abstract_id', $abstract->id)
            ->with('academic_conference_abstract_lookup_id', $abstract->id)
            ->with('success', '초록 제출이 완료되었습니다.');
    }

    public function checkNonMemberAbstract(AcademicConferenceNonMemberAbstractLookupRequest $request, string $folderName): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);
        $validated = $request->validated();

        $abstract = $this->abstractService->findNonMemberAbstract(
            $event,
            $validated['name'],
            $validated['email'],
            $validated['phone'],
            $validated['lookup_password']
        );

        if (! $abstract) {
            return back()
                ->withInput()
                ->withErrors(['lookup' => '입력하신 정보와 일치하는 초록 접수 내역이 없습니다.']);
        }

        session(['academic_conference_abstract_lookup_id' => $abstract->id]);

        return redirect()->to($conferenceBaseUrl . '/abstract/result');
    }

    public function editAbstract(Request $request, string $folderName, AcademicEventAbstract $abstract): View
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        abort_unless($this->abstractService->canAccess(
            $abstract,
            $event,
            auth()->user(),
            (int) session('academic_conference_abstract_lookup_id')
        ), 403);

        $page = $this->conferenceService->pageData($event, 'abstract/modify');
        $abstract->loadMissing(['files', 'field', 'member']);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);
        $currentMember = $this->isFrontendMemberLoggedIn() ? auth()->user() : null;
        $abstractPresentationTypes = $this->abstractService->activePresentationTypeLabels($event);
        $abstractFields = $event->fields;
        $abstractBookUrl = $event->abstract_book_path ? $this->conferenceService->optionalImageUrl($event->abstract_book_path) : null;
        $canModifyAbstract = $this->abstractService->canModify($event);

        return view($page['view'], [
            'event' => $event,
            'currentMember' => $currentMember,
            'abstract' => $abstract,
            'abstractPresentationTypes' => $abstractPresentationTypes,
            'abstractFields' => $abstractFields,
            'abstractBookUrl' => $abstractBookUrl,
            'canModifyAbstract' => $canModifyAbstract,
            'page_type' => $page['page_type'],
            'gNum' => $page['gNum'],
            'sNum' => $page['sNum'] ?? null,
            'gName' => $page['gName'],
            'sName' => $page['sName'],
            'gSlug' => $page['gSlug'],
            'conferenceBaseUrl' => $conferenceBaseUrl,
        ]);
    }

    public function updateAbstract(AcademicConferenceAbstractUpdateRequest $request, string $folderName, AcademicEventAbstract $abstract): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);
        abort_unless($this->abstractService->canAccess(
            $abstract,
            $event,
            auth()->user(),
            (int) session('academic_conference_abstract_lookup_id')
        ), 403);

        if (! $this->abstractService->canModify($event)) {
            return back()
                ->withErrors(['submission' => '초록 수정 기간이 종료되었습니다.']);
        }

        if (! $this->fieldBelongsToEvent($event, $request->input('academic_event_field_id'))) {
            return back()
                ->withInput()
                ->withErrors(['academic_event_field_id' => '현재 행사에 등록된 발표 분야를 선택해주세요.']);
        }

        $updated = $this->abstractService->updateAbstract(
            $abstract,
            $request->validated(),
            (array) $request->file('attachments', []),
            (array) $request->validated('remove_file_ids', [])
        );

        session(['academic_conference_abstract_lookup_id' => $updated->id]);

        return redirect()->to($conferenceBaseUrl . '/abstract/result')
            ->with('success', '초록이 수정되었습니다.');
    }

    public function storeRegistration(AcademicConferenceRegistrationRequest $request, string $folderName): JsonResponse|RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);
        $user = auth()->user();
        abort_unless($user?->role === 'user', 403);

        if (! $this->registrationService->canPreRegister($event)) {
            return $this->registrationFormError($request, 'registration', '사전등록 기간이 종료되었습니다.');
        }
        if ($this->registrationService->hasActiveMemberRegistration($event, $user)) {
            return $this->registrationFormError($request, 'registration', '이미 사전등록 신청 내역이 있습니다.');
        }

        $membershipPlan = $this->registrationService->selectedMembershipPlanForUser($user, $request->validated('membership_plan_id'));
        $requiresMembershipPlan = $this->registrationService->shouldShowMembershipFeeNotice($user)
            && $this->registrationService->membershipPlansForUser($user)->isNotEmpty();
        if ($requiresMembershipPlan && ! $membershipPlan) {
            return $this->registrationFormError($request, 'membership_plan_id', '납부 가능한 연회비 항목을 선택해주세요.');
        }
        if ($request->filled('membership_plan_id') && ! $membershipPlan) {
            return $this->registrationFormError($request, 'membership_plan_id', '납부 가능한 연회비 항목을 선택해주세요.');
        }

        $plans = $this->registrationService->selectedPlansForUser($user, $request->validated('payment_plan_ids'), $membershipPlan);
        if ($plans->isEmpty()) {
            return $this->registrationFormError($request, 'payment_plan_ids', '선택한 회비 기준으로 결제 가능한 학술대회 등록비를 선택해주세요.');
        }
        if ($request->filled('coupon_code') && ! $this->registrationService->resolveCoupon($request->validated('coupon_code'), $plans)) {
            return $this->registrationFormError($request, 'coupon_code', '사용 가능한 쿠폰이 아닙니다.');
        }

        if ($request->validated('payment_method') === 'card') {
            if (! $this->hasTossKeys()) {
                return response()->json([
                    'success' => false,
                    'message' => '토스페이먼츠 테스트 키가 설정되어 있지 않습니다.',
                    'errors' => ['payment' => ['토스페이먼츠 테스트 키가 설정되어 있지 않습니다.']],
                ], 422);
            }

            $registration = $this->registrationService->createCardPendingRegistration($event, $user, $plans, $request->validated(), $membershipPlan);

            return response()->json($this->tossPaymentPayload($event, $registration));
        }

        $registration = $this->registrationService->createBankTransferRegistration($event, $user, $plans, $request->validated(), $membershipPlan);

        return redirect()->to($conferenceBaseUrl . '/registration/end')
            ->with('academic_conference_registration_id', $registration->id)
            ->with('success', '사전등록 신청이 접수되었습니다. 입금 확인 후 승인 처리됩니다.');
    }

    public function storeNonMemberRegistration(AcademicConferenceNonMemberRegistrationRequest $request, string $folderName): JsonResponse|RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);

        if (! $this->registrationService->canPreRegister($event)) {
            return $this->registrationFormError($request, 'registration', '사전등록 기간이 종료되었습니다.');
        }
        if ($this->registrationService->hasActiveNonMemberRegistration($event, $request->validated('email'), $request->validated('phone'))) {
            return $this->registrationFormError($request, 'registration', '이미 사전등록 신청 내역이 있습니다.');
        }

        $plans = $this->registrationService->selectedPlansForNonMember($request->validated('payment_plan_ids'));
        if ($plans->isEmpty()) {
            return $this->registrationFormError($request, 'payment_plan_ids', '비회원 결제 항목을 선택해주세요.');
        }
        if ($request->filled('coupon_code') && ! $this->registrationService->resolveCoupon($request->validated('coupon_code'), $plans)) {
            return $this->registrationFormError($request, 'coupon_code', '사용 가능한 쿠폰이 아닙니다.');
        }

        if ($request->validated('payment_method') === 'card') {
            if (! $this->hasTossKeys()) {
                return response()->json([
                    'success' => false,
                    'message' => '토스페이먼츠 테스트 키가 설정되어 있지 않습니다.',
                    'errors' => ['payment' => ['토스페이먼츠 테스트 키가 설정되어 있지 않습니다.']],
                ], 422);
            }

            $registration = $this->registrationService->createCardPendingNonMemberRegistration($event, $plans, $request->validated());

            return response()->json($this->tossPaymentPayload($event, $registration));
        }

        $registration = $this->registrationService->createBankTransferNonMemberRegistration($event, $plans, $request->validated());

        return redirect()->to($conferenceBaseUrl . '/registration/end')
            ->with('academic_conference_registration_id', $registration->id)
            ->with('academic_conference_registration_lookup_id', $registration->id)
            ->with('success', '사전등록 신청이 접수되었습니다. 입금 확인 후 승인 처리됩니다.');
    }

    public function confirmTossRegistrationPayment(Request $request, string $folderName): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);

        $validated = $request->validate([
            'paymentKey' => ['required', 'string'],
            'orderId' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $registration = $this->registrationService->confirmTossPayment(
                $event,
                $validated['orderId'],
                $validated['paymentKey'],
                (int) $validated['amount']
            );
        } catch (RuntimeException $e) {
            $formPath = $this->isFrontendMemberLoggedIn() ? '/registration/form' : '/registration/form_non_member';

            return redirect()->to($conferenceBaseUrl . $formPath)
                ->withErrors(['payment' => $e->getMessage()]);
        }

        session(['academic_conference_registration_id' => $registration->id]);
        if (! $registration->member_id) {
            session(['academic_conference_registration_lookup_id' => $registration->id]);
        }

        return redirect()->to($conferenceBaseUrl . '/registration/end')
            ->with('success', '토스페이먼츠 결제가 완료되었습니다.');
    }

    public function failTossRegistrationPayment(Request $request, string $folderName): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $message = trim((string) $request->query('message'));
        $formPath = $this->isFrontendMemberLoggedIn() ? '/registration/form' : '/registration/form_non_member';

        return redirect()->to($this->conferenceService->baseUrl($event) . $formPath)
            ->withErrors(['payment' => $message !== '' ? $message : '토스페이먼츠 결제가 취소되었거나 실패했습니다.']);
    }

    public function applyRegistrationCoupon(Request $request, string $folderName): JsonResponse
    {
        $this->conferenceService->findPublicEventByFolder($folderName);
        $user = auth()->user();

        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
            'payment_plan_ids' => ['required', 'array', 'min:1'],
            'payment_plan_ids.*' => ['integer', 'exists:payment_plans,id'],
        ]);

        $plans = $user?->role === 'user'
            ? $this->registrationService->selectedPlansForUser($user, $validated['payment_plan_ids'])
            : $this->registrationService->selectedPlansForNonMember($validated['payment_plan_ids']);
        $coupon = $this->registrationService->resolveCoupon($validated['coupon_code'], $plans);
        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => '사용 가능한 쿠폰이 아닙니다.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => '쿠폰이 적용되었습니다.',
            'discount' => $coupon['discount'],
            'final_amount' => $coupon['final_amount'],
        ]);
    }

    public function checkNonMemberRegistration(Request $request, string $folderName): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $conferenceBaseUrl = $this->conferenceService->baseUrl($event);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
        ], [], [
            'name' => '이름',
            'email' => '이메일',
            'phone' => '휴대폰 번호',
        ]);

        $registration = $this->registrationService->findNonMemberRegistration(
            $event,
            $validated['name'],
            $validated['email'],
            $validated['phone']
        );

        if (! $registration) {
            return back()
                ->withInput()
                ->with('alert', '조회된 내역이 없습니다.');
        }

        session(['academic_conference_registration_lookup_id' => $registration->id]);

        return redirect()->to($conferenceBaseUrl . '/registration/result');
    }

    public function cancelRegistration(Request $request, string $folderName, AcademicEventRegistration $registration): RedirectResponse
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        abort_unless($this->registrationService->canAccessRegistration(
            $registration,
            $event,
            auth()->user(),
            (int) session('academic_conference_registration_lookup_id')
        ), 403);

        try {
            $message = $this->registrationService->cancelRegistration($registration);
        } catch (RuntimeException $e) {
            return redirect()->to($this->conferenceService->baseUrl($event) . '/registration/result')
                ->with('alert', $e->getMessage());
        }

        return redirect()->to($this->conferenceService->baseUrl($event))
            ->with('alert', $message === '취소되었습니다.' ? '취소가 완료되었습니다.' : $message);
    }

    public function printParticipation(string $folderName, AcademicEventRegistration $registration): View
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $registration->loadMissing(['event', 'items', 'member']);
        abort_unless($this->registrationService->canAccessRegistration(
            $registration,
            $event,
            auth()->user(),
            (int) session('academic_conference_registration_lookup_id')
        ), 403);
        abort_unless($registration->payment_status === 'completed' && $registration->cancelled_at === null, 404);

        return $this->renderRegistrationPrint('print_participation', '참가증명서', [
            'registration' => $registration,
            'user' => $registration->member,
        ]);
    }

    public function printReceipt(string $folderName, AcademicEventRegistration $registration): View
    {
        $event = $this->conferenceService->findPublicEventByFolder($folderName);
        $registration->loadMissing(['event', 'items', 'member']);
        abort_unless($this->registrationService->canAccessRegistration(
            $registration,
            $event,
            auth()->user(),
            (int) session('academic_conference_registration_lookup_id')
        ), 403);
        abort_unless($registration->payment_status === 'completed', 404);

        return $this->renderRegistrationPrint('print_receipt_save', '영수증', [
            'registration' => $registration,
            'methodLabels' => [
                'card' => '신용카드',
                'bank_transfer' => '무통장 입금',
                'onsite' => '현장결제',
            ],
        ]);
    }

    private function isFrontendMemberLoggedIn(): bool
    {
        return auth()->check() && auth()->user()?->role === 'user';
    }

    private function blockedRedirectUrl(Request $request): string
    {
        $fallback = route('academic_event.conference');
        $previous = url()->previous();

        return $previous !== $request->fullUrl() ? $previous : $fallback;
    }

    private function fieldBelongsToEvent(AcademicEvent $event, mixed $fieldId): bool
    {
        if ($fieldId === null || $fieldId === '') {
            return true;
        }

        return $event->fields->contains('id', (int) $fieldId);
    }

    private function renderRegistrationPrint(string $view, string $title, array $with): View
    {
        return view('mypage.' . $view, array_merge([
            'page_type' => 'professional',
            'gNum' => 'print',
            'sNum' => '00',
            'gName' => $title,
            'sName' => $title,
            'gSlug' => 'academic_conference_registration_print',
        ], $with));
    }

    private function hasTossKeys(): bool
    {
        return (string) config('services.toss.client_key') !== ''
            && (string) config('services.toss.secret_key') !== '';
    }

    private function registrationFormError(Request $request, string $field, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => [$field => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors([$field => $message]);
    }

    private function tossPaymentPayload(AcademicEvent $event, AcademicEventRegistration $registration): array
    {
        $summary = $this->registrationService->registrationSummary($registration->loadMissing('items'));
        $baseUrl = $this->conferenceService->baseUrl($event);

        return [
            'success' => true,
            'clientKey' => (string) config('services.toss.client_key'),
            'orderId' => $registration->registration_no,
            'orderName' => mb_substr($summary['item_names'] ?: $event->title, 0, 100),
            'amount' => (int) $registration->total_amount,
            'customerName' => $registration->name,
            'customerEmail' => $registration->email,
            'customerMobilePhone' => $registration->phone,
            'customerKey' => $registration->member_id
                ? 'kifm-member-' . $registration->member_id
                : 'kifm-guest-' . $registration->id,
            'successUrl' => $baseUrl . '/registration/toss/success',
            'failUrl' => $baseUrl . '/registration/toss/fail',
        ];
    }
}

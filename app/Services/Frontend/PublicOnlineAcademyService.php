<?php

namespace App\Services\Frontend;

use App\Models\EduCourse;
use App\Models\EduCourseEnrollment;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PublicOnlineAcademyService
{
    public const FALLBACK_HEAD_IMAGE = 'images/img_sample_online_academy_head.jpg';
    public const FALLBACK_LIST_IMAGE = 'images/img_sample_online_academy_list.jpg';
    public const FALLBACK_VIEW_IMAGE = 'images/img_sample_online_academy_view.jpg';
    public const PAYMENT_COMPLETED_STATUSES = ['completed', 'paid'];

    public function __construct(
        private readonly MailformNotificationService $mailNotifier,
    ) {}

    /** @return array<string, string> */
    public function courseTypeLabels(): array
    {
        return [
            'required' => '필수 과정',
            'conference' => '학술대회 연계 과정',
            'training' => '연수강좌 연계 과정',
            'regular' => '수시 과정',
            'online_advanced' => '온라인 심화과정',
        ];
    }

    /** @return array<string, string> */
    public function searchFieldLabels(): array
    {
        return [
            'all' => '전체',
            'title' => '강의명',
            'topic_detail' => '강의주제',
            'professor_name' => '교수명',
        ];
    }

    public function paginateVisible(Request $request, int $perPage = 8): LengthAwarePaginator
    {
        $query = EduCourse::query()->with('professorMember');
        $this->applyVisibleScope($query);
        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('open_year')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return Collection<int, EduCourse> */
    public function featuredCourses(int $limit = 5): Collection
    {
        $query = EduCourse::query()->with('professorMember');
        $this->applyVisibleScope($query);

        return $query
            ->orderByDesc('open_year')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function findVisible(int $id): EduCourse
    {
        $query = EduCourse::query()->with(['professorMember', 'examQuestions', 'gradePrices']);
        $this->applyVisibleScope($query);

        return $query->findOrFail($id);
    }

    public function firstVisible(): ?EduCourse
    {
        $query = EduCourse::query();
        $this->applyVisibleScope($query);

        return $query->orderByDesc('open_year')->orderByDesc('id')->first();
    }

    /** @return array<string, mixed> */
    public function examPageData(EduCourse $course, int $step = 1): array
    {
        $total = $course->examQuestions->count();
        $currentStep = $total > 0 ? min(max(1, $step), $total) : 0;
        $question = $currentStep > 0 ? $course->examQuestions->values()->get($currentStep - 1) : null;
        $choices = is_array($question?->choices_json)
            ? array_values(array_filter(array_map('strval', $question->choices_json)))
            : [];
        $choiceItems = array_map(
            static fn (string $choice, int $index): array => [
                'id' => 'test_select' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'number' => $index + 1,
                'text' => $choice,
                'value' => $index,
            ],
            $choices,
            array_keys($choices),
        );

        return [
            'question' => $question,
            'choices' => $choiceItems,
            'currentStep' => $currentStep,
            'totalSteps' => $total,
            'stepText' => $question ? str_pad((string) $currentStep, 2, '0', STR_PAD_LEFT) . '/' . str_pad((string) $total, 2, '0', STR_PAD_LEFT) : '00/00',
            'previousStepUrl' => $currentStep > 1 ? route('online_academy.exam', ['course' => $course->id, 'step' => $currentStep - 1]) : route('online_academy.show', $course),
            'nextStepUrl' => $currentStep < $total ? route('online_academy.exam', ['course' => $course->id, 'step' => $currentStep + 1]) : route('online_academy.end', ['course' => $course->id]),
            'isLastStep' => $currentStep >= $total,
        ];
    }

    /**
     * @param array<int, int|string> $answers
     *
     * @return array{score: int, correct: int, total: int, passed: bool}
     */
    public function gradeExam(EduCourse $course, User $user, array $answers): array
    {
        $questions = $course->examQuestions->values();
        $total = $questions->count();
        $correct = 0;

        foreach ($questions as $index => $question) {
            if ((int) ($answers[$index + 1] ?? -1) === (int) $question->answer_index) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) floor(($correct / $total) * 100) : 0;
        $passingScore = (int) ($course->completion_score ?: 100);
        $passed = $score >= $passingScore;

        $enrollment = $this->completedEnrollment($course, $user);
        if ($enrollment) {
            $enrollment->fill([
                'exam_status' => $passed ? 'passed' : 'failed',
                'exam_score' => $score,
                'enrollment_status' => $passed ? 'completed' : $enrollment->enrollment_status,
                'completed_at' => $passed ? ($enrollment->completed_at ?: now()) : $enrollment->completed_at,
            ]);
            $enrollment->save();
        }

        return [
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
            'passed' => $passed,
        ];
    }

    /** @return list<int> */
    public function yearOptions(): array
    {
        $query = EduCourse::query();
        $this->applyVisibleScope($query);

        return $query
            ->select('open_year')
            ->distinct()
            ->orderByDesc('open_year')
            ->pluck('open_year')
            ->map(static fn ($year): int => (int) $year)
            ->all();
    }

    /** @return list<string> */
    public function keywordOptions(): array
    {
        $query = EduCourse::query();
        $this->applyVisibleScope($query);

        return $query
            ->whereNotNull('keywords')
            ->pluck('keywords')
            ->flatMap(fn (?string $keywords) => $this->splitCsv($keywords))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    public function filters(Request $request): array
    {
        $courseTypes = array_keys($this->courseTypeLabels());
        $keywordFilters = $request->input('keywords', []);
        if (! is_array($keywordFilters)) {
            $keywordFilters = [$keywordFilters];
        }
        $courseType = (string) $request->input('course_type', $courseTypes[0] ?? '');
        if ($courseType !== '' && ! array_key_exists($courseType, $this->courseTypeLabels())) {
            $courseType = $courseTypes[0] ?? '';
        }

        return [
            'course_type' => $courseType,
            'open_year' => (string) $request->input('open_year', ''),
            'keywords' => array_values(array_filter(array_map('strval', $keywordFilters))),
            'search_field' => (string) $request->input('search_field', 'all'),
            'search_keyword' => trim((string) $request->input('search_keyword', '')),
        ];
    }

    public function imageUrl(?string $path, string $fallback): string
    {
        if ($path === null || $path === '') {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function lectureFileUrl(EduCourse $course): ?string
    {
        if (! $course->lecture_file_path) {
            return null;
        }

        return Storage::disk('public')->url($course->lecture_file_path);
    }

    public function entryUrl(EduCourse $course, ?User $user): string
    {
        if ($user !== null && $this->hasActiveEnrollment($course, $user)) {
            return route('online_academy.show', $course);
        }

        return route('online_academy.payment', ['course' => $course->id]);
    }

    public function canViewCourse(EduCourse $course, ?User $user): bool
    {
        return $user !== null && $this->hasCompletedEnrollment($course, $user);
    }

    public function hasActiveEnrollment(EduCourse $course, ?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $this->activeEnrollment($course, $user) !== null;
    }

    public function hasCompletedEnrollment(EduCourse $course, ?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $this->completedEnrollment($course, $user) !== null;
    }

    public function activeEnrollment(EduCourse $course, User $user): ?EduCourseEnrollment
    {
        if (! $this->isWithinCoursePeriod($course)) {
            return null;
        }

        return EduCourseEnrollment::query()
            ->where('edu_course_id', $course->id)
            ->where('member_id', $user->id)
            ->whereNotIn('payment_status', ['cancel_requested', 'cancelled'])
            ->where(function ($query): void {
                $query->whereNull('expire_at')
                    ->orWhere('expire_at', '>=', now());
            })
            ->orderByDesc('id')
            ->first();
    }

    public function completedEnrollment(EduCourse $course, User $user): ?EduCourseEnrollment
    {
        if (! $this->isWithinCoursePeriod($course)) {
            return null;
        }

        return EduCourseEnrollment::query()
            ->where('edu_course_id', $course->id)
            ->where('member_id', $user->id)
            ->whereIn('payment_status', self::PAYMENT_COMPLETED_STATUSES)
            ->where(function ($query): void {
                $query->whereNull('expire_at')
                    ->orWhere('expire_at', '>=', now());
            })
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return array{progress_rate: int, watched_min: int, last_position_sec: int, video_duration_sec: int, is_completed: bool}
     */
    public function progressData(?EduCourseEnrollment $enrollment): array
    {
        return [
            'progress_rate' => (int) ($enrollment?->progress_rate ?? 0),
            'watched_min' => (int) ($enrollment?->total_study_min ?? 0),
            'last_position_sec' => (int) ($enrollment?->last_position_sec ?? 0),
            'video_duration_sec' => (int) ($enrollment?->video_duration_sec ?? 0),
            'is_completed' => (int) ($enrollment?->progress_rate ?? 0) >= 100,
        ];
    }

    /**
     * @param array{current_time: mixed, duration: mixed, ended?: mixed} $data
     *
     * @return array{progress_rate: int, watched_min: int, last_position_sec: int, video_duration_sec: int, is_completed: bool}
     */
    public function updateProgress(EduCourse $course, User $user, array $data): array
    {
        $enrollment = $this->completedEnrollment($course, $user);
        if (! $enrollment) {
            throw new RuntimeException('수강 신청 내역을 찾을 수 없습니다.');
        }

        $currentTime = max(0, (int) floor((float) $data['current_time']));
        $duration = max(0, (int) floor((float) $data['duration']));
        $ended = filter_var($data['ended'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $previousDuration = (int) ($enrollment->video_duration_sec ?? 0);
        $duration = max($duration, $previousDuration, $this->courseDurationSeconds($course));
        $progressRate = $duration > 0 ? (int) floor(($currentTime / $duration) * 100) : 0;
        if ($ended || ($duration > 0 && $currentTime >= $duration - 2)) {
            $progressRate = 100;
        }
        $progressRate = max((int) ($enrollment->progress_rate ?? 0), min(100, $progressRate));
        $lastPosition = $ended ? $duration : $currentTime;

        $enrollment->fill([
            'progress_rate' => $progressRate,
            'total_study_min' => max((int) ($enrollment->total_study_min ?? 0), (int) ceil($lastPosition / 60)),
            'last_position_sec' => max(0, $lastPosition),
            'video_duration_sec' => max(0, $duration),
            'last_studied_at' => now(),
            'completed_at' => $progressRate >= 100 ? ($enrollment->completed_at ?: now()) : $enrollment->completed_at,
            'enrollment_status' => $progressRate >= 100 ? 'completed' : ($enrollment->enrollment_status ?: 'in_progress'),
        ]);
        $enrollment->save();

        return $this->progressData($enrollment->refresh());
    }

    /**
     * @return array{eligible: bool, price: int, grade_label: string, message: string}
     */
    public function priceForUser(EduCourse $course, User $user): array
    {
        $grade = trim((string) $user->member_level);
        $gradeLabels = \App\Services\Backoffice\MemberService::memberLevelLabels();
        $gradeLabel = $gradeLabels[$grade] ?? ($grade !== '' ? $grade : '회원');

        if (! $this->isWithinCoursePeriod($course)) {
            return [
                'eligible' => false,
                'price' => 0,
                'grade_label' => $gradeLabel,
                'message' => '수강 기간이 아닙니다.',
            ];
        }

        if ($this->isFreeCourse($course)) {
            return [
                'eligible' => true,
                'price' => 0,
                'grade_label' => $gradeLabel,
                'message' => '',
            ];
        }

        $course->loadMissing('gradePrices');
        $gradePrice = $course->gradePrices->firstWhere('grade_code', $grade);
        if (! $gradePrice || ! $gradePrice->is_enabled) {
            return [
                'eligible' => false,
                'price' => 0,
                'grade_label' => $gradeLabel,
                'message' => '현재 회원 등급으로 신청할 수 없는 강좌입니다.',
            ];
        }

        return [
            'eligible' => true,
            'price' => (int) ($gradePrice->price ?? 0),
            'grade_label' => $gradeLabel,
            'message' => '',
        ];
    }

    public function courseGradeLabelText(EduCourse $course): string
    {
        if ($this->isFreeCourse($course)) {
            return '전체 회원';
        }

        $course->loadMissing('gradePrices');
        $labels = \App\Services\Backoffice\MemberService::memberLevelLabels();
        $enabled = $course->gradePrices
            ->filter(static fn ($price): bool => (bool) $price->is_enabled)
            ->map(static fn ($price): string => $labels[$price->grade_code] ?? (string) $price->grade_code)
            ->filter()
            ->values()
            ->all();

        return $enabled !== [] ? implode(', ', $enabled) : '신청 가능 회원 없음';
    }

    public function createOrRefreshEnrollment(EduCourse $course, User $user): EduCourseEnrollment
    {
        return $this->createOrRefreshEnrollmentWithPayment($course, $user, []);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createOrRefreshEnrollmentWithPayment(EduCourse $course, User $user, array $data): EduCourseEnrollment
    {
        $pricing = $this->priceForUser($course, $user);
        if (! $pricing['eligible']) {
            throw new \RuntimeException($pricing['message']);
        }

        $subtotal = (int) $pricing['price'];
        $couponResult = $this->resolveCoupon($data['coupon_code'] ?? null, $subtotal);
        $discount = (int) ($couponResult['discount'] ?? 0);
        $coupon = $couponResult['coupon'] ?? null;
        $amount = max(0, $subtotal - $discount);
        $paymentMethod = (string) ($data['payment_method'] ?? 'bank_transfer');
        $isFree = $amount <= 0;
        $isCompletedPayment = $isFree;
        $isPendingCardPayment = ! $isFree && $paymentMethod === 'card';
        $shouldSendMail = false;

        $enrollment = DB::transaction(function () use ($course, $user, $data, $subtotal, $discount, $coupon, $amount, $paymentMethod, $isFree, $isCompletedPayment, $isPendingCardPayment, &$shouldSendMail) {
            $enrollment = $this->activeEnrollment($course, $user) ?: new EduCourseEnrollment([
                'edu_course_id' => $course->id,
                'member_id' => $user->id,
            ]);

            if ($enrollment->exists && $this->canViewCourse($course, $user)) {
                return $enrollment;
            }

            $enrollment->fill([
                'member_name' => (string) ($user->name ?? ''),
                'member_grade_at' => (string) ($user->member_level ?? ''),
                'enrollment_status' => $isCompletedPayment ? 'in_progress' : 'payment_pending',
                'progress_rate' => $enrollment->progress_rate ?? 0,
                'exam_status' => $enrollment->exam_status ?? 'not_attempted',
                'total_study_min' => $enrollment->total_study_min ?? 0,
                'certificate_status' => $enrollment->certificate_status ?? 'not_issued',
                'payment_no' => $enrollment->payment_no ?: $this->generatePaymentNo(),
                'payment_status' => $isCompletedPayment ? 'completed' : ($isPendingCardPayment ? 'pending_payment' : 'pending'),
                'payment_method' => $isFree ? null : $paymentMethod,
                'payment_item_name' => $course->title . ' 수강료',
                'payment_amount' => $amount,
                'paid_at' => $isCompletedPayment ? now() : null,
                'bank_depositor' => $paymentMethod === 'bank_transfer' ? ($data['bank_depositor'] ?? null) : null,
                'bank_deposit_date' => $paymentMethod === 'bank_transfer' ? ($data['bank_deposit_date'] ?? null) : null,
                'refund_bank' => $paymentMethod === 'bank_transfer' ? ($data['refund_bank'] ?? null) : null,
                'refund_account' => $paymentMethod === 'bank_transfer' ? ($data['refund_account'] ?? null) : null,
                'refund_holder' => $paymentMethod === 'bank_transfer' ? ($data['refund_holder'] ?? null) : null,
                'receipt_issue' => (string) ($data['receipt_issue'] ?? 'NO'),
                'receipt_type' => ($data['receipt_issue'] ?? 'NO') === 'YES' ? ($data['receipt_type'] ?? null) : null,
                'receipt_number' => ($data['receipt_issue'] ?? 'NO') === 'YES' ? ($data['receipt_number'] ?? null) : null,
                'admin_memo' => $this->paymentMemo($subtotal, $discount, $coupon?->coupon_code),
                'applied_at' => $enrollment->applied_at ?: now(),
                'expire_at' => $this->resolveExpireAt($course),
            ]);
            $enrollment->save();

            if ($coupon && ! $isPendingCardPayment) {
                $coupon->increment('usage_count');
            }

            $shouldSendMail = $paymentMethod === 'bank_transfer' || $isCompletedPayment;

            return $enrollment->fresh(['course']);
        });

        if ($shouldSendMail) {
            $this->mailNotifier->sendOnlineAcademyRegistrationComplete($enrollment);
        }

        return $enrollment;
    }

    public function confirmTossPayment(string $orderId, string $paymentKey, int $amount, User $user): EduCourseEnrollment
    {
        $enrollment = EduCourseEnrollment::query()
            ->with('course')
            ->where('member_id', $user->id)
            ->where('payment_no', $orderId)
            ->where('payment_method', 'card')
            ->first();

        if (! $enrollment) {
            throw new RuntimeException('확인할 온라인 아카데미 카드 결제 신청 내역이 없습니다.');
        }
        if ((int) $enrollment->payment_amount !== $amount) {
            throw new RuntimeException('결제 금액이 신청 금액과 일치하지 않습니다.');
        }
        if ($enrollment->payment_status === 'completed') {
            return $enrollment;
        }

        $payload = $this->requestTossConfirm($paymentKey, $orderId, $amount);
        $isCompleted = ($payload['status'] ?? null) === 'DONE';
        $memo = (string) ($enrollment->admin_memo ?? '');
        if ($isCompleted && preg_match('/coupon=([A-Z0-9_-]+)/', $memo, $matches) && ! str_contains($memo, 'coupon_counted=1')) {
            Coupon::query()->where('coupon_code', $matches[1])->increment('usage_count');
            $memo = trim($memo . ';coupon_counted=1', ';');
        }

        $enrollment->update([
            'enrollment_status' => $isCompleted ? 'in_progress' : 'payment_pending',
            'payment_status' => $isCompleted ? 'completed' : 'pending_payment',
            'paid_at' => $isCompleted ? now() : null,
            'admin_memo' => trim($memo . ';toss_payment_key=' . $paymentKey, ';'),
        ]);

        $enrollment = $enrollment->refresh()->loadMissing(['course', 'member']);
        if ($isCompleted) {
            $this->mailNotifier->sendOnlineAcademyRegistrationComplete($enrollment);
        }

        return $enrollment;
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

    /**
     * @return array{subtotal: int, discount: int, final_amount: int, coupon_code: string}
     */
    public function paymentSummary(EduCourse $course, User $user, ?EduCourseEnrollment $enrollment = null): array
    {
        $pricing = $this->priceForUser($course, $user);
        $subtotal = (int) ($pricing['price'] ?? 0);
        $final = $enrollment ? (int) $enrollment->payment_amount : $subtotal;
        $discount = max(0, $subtotal - $final);
        $couponCode = '';

        if ($enrollment && preg_match('/coupon=([A-Z0-9_-]+)/', (string) $enrollment->admin_memo, $matches)) {
            $couponCode = $matches[1];
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'final_amount' => $final,
            'coupon_code' => $couponCode,
        ];
    }

    public function periodText(EduCourse $course): string
    {
        if ($course->period_type === 'range' && $course->period_start && $course->period_end) {
            return $course->period_start->format('Y.m.d') . ' ~ ' . $course->period_end->format('Y.m.d');
        }

        return ((int) ($course->duration_days ?: 0)) . '일 수강';
    }

    public function compactPeriodText(EduCourse $course): string
    {
        if ($course->period_type === 'range' && $course->period_start && $course->period_end) {
            $start = $course->period_start;
            $end = $course->period_end;

            if ($start->isSameDay($end)) {
                return $start->format('y.m.d');
            }
            if ($start->isSameMonth($end)) {
                return $start->format('y.m.d') . '~' . $end->format('d');
            }
            if ($start->isSameYear($end)) {
                return $start->format('y.m.d') . '~' . $end->format('m.d');
            }

            return $start->format('y.m.d') . '~' . $end->format('y.m.d');
        }

        return $this->periodText($course);
    }

    public function isFreeCourse(EduCourse $course): bool
    {
        if ($course->free_yn !== 'Y') {
            return false;
        }

        $today = now()->toDateString();
        if ($course->free_start_date && $course->free_start_date->toDateString() > $today) {
            return false;
        }
        if ($course->free_end_date && $course->free_end_date->toDateString() < $today) {
            return false;
        }

        return true;
    }

    public function professorText(EduCourse $course): string
    {
        $name = $course->professorMember->name ?? $course->professor_name ?? '';
        $org = $course->professorMember->workplace_name ?? $course->professor_org ?? '';

        if ($name === '' && $org === '') {
            return '';
        }
        if ($org === '') {
            return $name . ' 교수';
        }

        return $name . ' 교수 (' . $org . ')';
    }

    /** @return list<string> */
    public function topicList(?string $topics): array
    {
        return $this->splitCsv($topics);
    }

    /** @return list<string> */
    public function keywordList(?string $keywords): array
    {
        return $this->splitCsv($keywords);
    }

    public function summaryText(EduCourse $course, int $limit = 120): string
    {
        $text = trim(strip_tags((string) ($course->topic_detail ?: $course->content ?: '')));

        return $text !== '' ? Str::limit($text, $limit) : $course->title;
    }

    public function courseDurationSeconds(EduCourse $course): int
    {
        return max(0, (int) ($course->duration_sec ?? 0) ?: ((int) $course->duration_min * 60));
    }

    public function durationText(EduCourse $course): string
    {
        $seconds = $this->courseDurationSeconds($course);
        $minutes = intdiv($seconds, 60);
        $remainSeconds = $seconds % 60;

        if ($remainSeconds <= 0) {
            return $minutes . '분';
        }

        return $minutes . '분 ' . $remainSeconds . '초';
    }

    public function videoEmbedUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]+)~', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }
        if (str_contains($url, 'player.vimeo.com/video/')) {
            return $url;
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return null;
    }

    private function applyVisibleScope(Builder $query): void
    {
        $query->where('use_yn', 'Y')->where('expose_yn', 'Y');
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $filters = $this->filters($request);

        if ($filters['course_type'] !== '') {
            $query->where('course_type', $filters['course_type']);
        }
        if ($filters['open_year'] !== '') {
            $query->where('open_year', (int) $filters['open_year']);
        }
        $keywords = array_values(array_filter(
            $filters['keywords'],
            static fn (string $keyword): bool => $keyword !== '' && $keyword !== '전체'
        ));
        if ($keywords !== []) {
            $query->where(function (Builder $q) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $q->orWhere('keywords', 'like', '%' . $keyword . '%');
                }
            });
        }
        if ($filters['search_keyword'] === '') {
            return;
        }

        $like = '%' . $filters['search_keyword'] . '%';
        $field = $filters['search_field'];

        $query->where(function (Builder $q) use ($field, $like): void {
            if ($field === 'title') {
                $q->where('title', 'like', $like);
                return;
            }
            if ($field === 'topic_detail') {
                $q->where('topic_detail', 'like', $like)
                    ->orWhere('topics', 'like', $like)
                    ->orWhere('content', 'like', $like);
                return;
            }
            if ($field === 'professor_name') {
                $q->where('professor_name', 'like', $like)
                    ->orWhereHas('professorMember', fn (Builder $memberQ) => $memberQ->where('name', 'like', $like));
                return;
            }

            $q->where('title', 'like', $like)
                ->orWhere('topic_detail', 'like', $like)
                ->orWhere('topics', 'like', $like)
                ->orWhere('content', 'like', $like)
                ->orWhere('keywords', 'like', $like)
                ->orWhere('professor_name', 'like', $like)
                ->orWhereHas('professorMember', fn (Builder $memberQ) => $memberQ->where('name', 'like', $like));
        });
    }

    /** @return list<string> */
    private function splitCsv(?string $value): array
    {
        $items = preg_split('/\s*,\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_map('trim', $items ?: [])));
    }

    private function resolveExpireAt(EduCourse $course): ?\Illuminate\Support\Carbon
    {
        if ($course->period_type === 'range') {
            return $course->period_end?->copy()->endOfDay();
        }

        $days = max(1, (int) ($course->duration_days ?: 30));

        return now()->addDays($days)->endOfDay();
    }

    private function isWithinCoursePeriod(EduCourse $course): bool
    {
        if ($course->period_type !== 'range') {
            return true;
        }

        $now = now();
        if ($course->period_start && $course->period_start->copy()->startOfDay()->gt($now)) {
            return false;
        }
        if ($course->period_end && $course->period_end->copy()->endOfDay()->lt($now)) {
            return false;
        }

        return true;
    }

    private function generatePaymentNo(): string
    {
        do {
            $no = 'OA-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
        } while (EduCourseEnrollment::query()->where('payment_no', $no)->exists());

        return $no;
    }

    private function paymentMemo(int $subtotal, int $discount, ?string $couponCode): ?string
    {
        if ($discount <= 0 && ! $couponCode) {
            return null;
        }

        return 'subtotal=' . $subtotal . ';discount=' . $discount . ';coupon=' . ($couponCode ?: '');
    }
}

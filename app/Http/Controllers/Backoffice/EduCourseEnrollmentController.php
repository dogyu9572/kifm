<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\EduCourse;
use App\Models\EduCourseEnrollment;
use App\Services\Backoffice\MemberService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EduCourseEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        $query = EduCourseEnrollment::query()
            ->with(['course', 'member'])
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('course', fn ($courseQ) => $courseQ->where('course_type', $request->string('category')->toString()));
            })
            ->when($request->filled('open_year'), function ($q) use ($request) {
                $q->whereHas('course', fn ($courseQ) => $courseQ->where('open_year', (int) $request->input('open_year')));
            })
            ->when($request->filled('edu_course_id'), fn ($q) => $q->where('edu_course_id', (int) $request->input('edu_course_id')))
            ->when($request->filled('enrollment_status'), fn ($q) => $q->where('enrollment_status', $request->string('enrollment_status')->toString()))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('applied_at', '>=', $request->string('date_from')->toString()))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('applied_at', '<=', $request->string('date_to')->toString()))
            ->when(trim((string) $request->input('search_keyword')) !== '', function ($q) use ($request) {
                $keyword = trim((string) $request->input('search_keyword'));
                $field = (string) $request->input('search_field', 'all');
                $like = '%' . $keyword . '%';
                $q->where(function ($inner) use ($field, $like) {
                    if ($field === 'course') {
                        $inner->whereHas('course', fn ($courseQ) => $courseQ->where('title', 'like', $like));
                        return;
                    }
                    if ($field === 'name') {
                        $inner->where('member_name', 'like', $like);
                        return;
                    }
                    $inner->where('member_name', 'like', $like)
                        ->orWhereHas('course', fn ($courseQ) => $courseQ->where('title', 'like', $like));
                });
            });

        $enrollments = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        $courses = EduCourse::query()->orderByDesc('open_year')->orderBy('title')->get(['id', 'title', 'open_year', 'course_type']);

        return view('backoffice.edu-course-enrollments.index', [
            'enrollments' => $enrollments,
            'courses' => $courses,
            'perPage' => $perPage,
            'categoryLabels' => $this->categoryLabels(),
            'statusLabels' => $this->statusLabels(),
            'certificateLabels' => $this->certificateLabels(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function show(Request $request, EduCourseEnrollment $eduCourseEnrollment)
    {
        $eduCourseEnrollment->load(['course', 'member']);

        return view('backoffice.edu-course-enrollments.show', [
            'enrollment' => $eduCourseEnrollment,
            'returnUrl' => $this->returnUrl($request),
            'categoryLabels' => $this->categoryLabels(),
            'statusLabels' => $this->statusLabels(),
            'paymentStatusLabels' => $this->paymentStatusLabels(),
            'paymentMethodLabels' => $this->paymentMethodLabels(),
            'examStatusLabels' => $this->examStatusLabels(),
            'memberLevelLabels' => MemberService::memberLevelLabels(),
        ]);
    }

    public function certificate(EduCourseEnrollment $eduCourseEnrollment): View
    {
        abort_unless($eduCourseEnrollment->certificate_status === 'issued', 404);

        $eduCourseEnrollment->load(['course', 'member']);

        return view('mypage.print_completion', [
            'gName' => '이수증',
            'enrollment' => $eduCourseEnrollment,
            'user' => $eduCourseEnrollment->member,
        ]);
    }

    public function update(Request $request, EduCourseEnrollment $eduCourseEnrollment): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'string'],
            'payment_method' => ['nullable', 'string'],
            'bank_depositor' => ['nullable', 'string', 'max:100'],
            'bank_deposit_date' => ['nullable', 'date'],
            'admin_memo' => ['nullable', 'string'],
            'receipt_issue' => ['nullable', 'in:YES,NO'],
            'receipt_type' => ['nullable', 'string', 'max:30'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'refund_bank' => ['nullable', 'string', 'max:100'],
            'refund_account' => ['nullable', 'string', 'max:100'],
            'refund_holder' => ['nullable', 'string', 'max:100'],
        ]);

        if (in_array($validated['payment_status'], ['paid', 'completed'], true)
            && $eduCourseEnrollment->enrollment_status === 'payment_pending') {
            $validated['enrollment_status'] = 'in_progress';
            $validated['paid_at'] = $eduCourseEnrollment->paid_at ?: now();
        }

        $eduCourseEnrollment->fill($validated);
        $eduCourseEnrollment->save();

        return redirect()->route('backoffice.edu-course-enrollments.show', [
            'edu_course_enrollment' => $eduCourseEnrollment,
            'return_url' => $request->input('return_url'),
        ])->with('success', '수강 신청 내역이 수정되었습니다.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enrollment_ids' => ['required', 'array'],
            'enrollment_ids.*' => ['integer', 'exists:edu_course_enrollments,id'],
        ]);

        $deleted = EduCourseEnrollment::destroy($validated['enrollment_ids']);

        return response()->json([
            'success' => true,
            'message' => $deleted . '건의 수강 신청 내역이 삭제되었습니다.',
            'deleted_count' => $deleted,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'edu-course-enrollments-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $rows = EduCourseEnrollment::query()->with('course')->orderByDesc('id')->limit(1000)->get();

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['번호', '분류', '개설연도', '강의명', '수강자명', '신청일', '수강만료일', '수강률', '상태']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $this->categoryLabels()[$row->course->course_type ?? ''] ?? '-',
                    $row->course->open_year ?? '-',
                    $row->course->title ?? '-',
                    $row->member_name,
                    optional($row->applied_at)->format('Y.m.d'),
                    optional($row->expire_at)->format('Y.m.d') ?? '-',
                    $row->progress_rate . '%',
                    $this->statusLabels()[$row->enrollment_status] ?? $row->enrollment_status,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    protected function yearOptions(): array
    {
        $base = (int) now()->format('Y');
        $years = [];
        for ($y = $base; $y >= 2010; $y--) {
            $years[] = $y;
        }

        return $years;
    }

    protected function returnUrl(Request $request): string
    {
        $fallback = route('backoffice.edu-course-enrollments.index');
        $raw = $request->query('return_url');
        if (! is_string($raw) || $raw === '') {
            return $fallback;
        }

        $decoded = urldecode($raw);
        if (str_starts_with($decoded, '/backoffice/') && ! str_starts_with($decoded, '//')) {
            return $decoded;
        }

        return $fallback;
    }

    protected function categoryLabels(): array
    {
        return [
            'conference' => '학술대회',
            'training' => '연수강좌',
            'regular' => '일반교육',
            'required' => '필수 과정',
            'online_advanced' => '정기강좌',
        ];
    }

    protected function statusLabels(): array
    {
        return [
            'in_progress' => '수강 중',
            'completed' => '수강 완료',
            'payment_pending' => '결제 대기',
            'expired' => '수강기간 만료',
        ];
    }

    protected function paymentStatusLabels(): array
    {
        return [
            'pending' => '입금 대기',
            'paid' => '결제 완료',
            'cancelled' => '결제 취소',
            'expired' => '입금 기한 만료',
            'failed' => '결제 실패',
        ];
    }

    protected function paymentMethodLabels(): array
    {
        return [
            'card' => '신용카드',
            'bank_transfer' => '무통장 입금',
        ];
    }

    protected function examStatusLabels(): array
    {
        return [
            'not_attempted' => '미응시',
            'attempted' => '응시',
            'passed' => '합격',
            'failed' => '불합격',
        ];
    }

    protected function certificateLabels(): array
    {
        return [
            'issued' => '발급완료',
            'not_issued' => '미발급',
        ];
    }
}

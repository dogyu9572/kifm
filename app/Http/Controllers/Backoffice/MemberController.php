<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackofficeMemberRequest;
use App\Models\CommunityCommittee;
use App\Models\User;
use App\Services\Backoffice\MemberHistoryService;
use App\Services\Backoffice\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService,
        private readonly MemberHistoryService $memberHistoryService,
    ) {
    }

    public function index(Request $request)
    {
        $grades = $request->get('grades', []);
        if (! is_array($grades)) {
            $grades = [];
        }

        $filters = [
            'join_type' => $request->get('join_type', '전체'),
            'join_date_start' => $request->get('join_date_start'),
            'join_date_end' => $request->get('join_date_end'),
            'date_start' => $request->filled('date_start') ? $request->get('date_start') : $request->get('join_date_start'),
            'date_end' => $request->filled('date_end') ? $request->get('date_end') : $request->get('join_date_end'),
            'search_condition' => $request->get('search_condition', 'joinDate'),
            'marketing_consent' => $request->get('marketing_consent', []),
            'search_type' => $request->get('search_type', '전체'),
            'search_term' => $request->get('search_term', ''),
            'sort_order' => $request->get('sort_order', 'joinDate'),
            'grades' => $grades,
            'is_certified' => $request->get('is_certified', 'all'),
            'inactive_only' => $request->boolean('inactive_only'),
            'due_mode' => $request->get('due_mode', 'all'),
            'due_date' => $request->get('due_date'),
            'annual_fee' => $request->get('annual_fee', 'all'),
            'executive_status' => $request->get('executive_status', 'all'),
            'search_field' => $request->get('search_field', ''),
            'search_keyword' => $request->get('search_keyword', ''),
        ];

        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;
        $members = $this->memberService->getMembers($filters, $perPage);

        $memberLevelLabels = MemberService::memberLevelLabels();
        $jobTypeLabels = MemberService::jobTypeLabels();
        $committeeNamesById = CommunityCommittee::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->mapWithKeys(static fn (mixed $name, int|string $id): array => [(string) $id => $name])
            ->all();

        return view('backoffice.members.index', compact(
            'members',
            'filters',
            'perPage',
            'memberLevelLabels',
            'jobTypeLabels',
            'committeeNamesById'
        ));
    }

    public function create()
    {
        return view('backoffice.members.create', $this->memberFormViewData(
            new User(),
            false
        ));
    }

    public function store(BackofficeMemberRequest $request)
    {
        $this->memberService->createMember($request->validated());

        return redirect()->route('backoffice.members.index')
            ->with('success', '회원이 등록되었습니다.');
    }

    public function show(User $user)
    {
        return redirect()->route('backoffice.members.edit', $user);
    }

    public function edit(User $user)
    {
        $member = $this->memberService->getMember($user->id);

        $viewData = $this->memberFormViewData($member, true);
        $viewData['histories'] = $this->memberHistoryService->forMember($member);
        $viewData['historyLabels'] = MemberHistoryService::labels();

        return view('backoffice.members.edit', $viewData);
    }

    /**
     * 회원 등록/수정 폼에 필요한 뷰 데이터(프로토타입 정합 폼용)
     *
     * @return array<string, mixed>
     */
    private function memberFormViewData(User $member, bool $isEdit): array
    {
        $committeeFromMember = $member->committee_codes ?? [];
        if (! is_array($committeeFromMember)) {
            $committeeFromMember = [];
        }
        $selectedCommitteeCodes = old('committee_codes', $committeeFromMember);
        if (! is_array($selectedCommitteeCodes)) {
            $selectedCommitteeCodes = [];
        }
        $selectedCommitteeCodes = array_values(array_filter(
            array_map(static fn ($code): string => is_string($code) || is_int($code) ? (string) $code : '', $selectedCommitteeCodes),
            static fn (string $code): bool => $code !== ''
        ));

        $medicalDepartmentValue = (string) old('medical_department', $member->medical_department ?? '');

        $committeesForForm = CommunityCommittee::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'member' => $member,
            'isEdit' => $isEdit,
            'committeesForForm' => $committeesForForm,
            'memberLevelLabels' => MemberService::memberLevelLabels(),
            'jobTypeLabels' => MemberService::jobTypeLabels(),
            'medicalDepartmentOptions' => config('medical_departments', []),
            'medicalDepartmentValue' => $medicalDepartmentValue,
            'selectedCommitteeCodes' => $selectedCommitteeCodes,
        ];
    }

    public function update(BackofficeMemberRequest $request, User $user)
    {
        $beforeLevel = (string) ($user->member_level ?? '');
        $member = $this->memberService->updateMember($user->id, $request->validated());

        if ($beforeLevel === 'pending' && $member->member_level !== 'pending') {
            $this->sendWelcomeApprovedMail($member);
        }

        return redirect()->route('backoffice.members.index')
            ->with('success', '회원 정보가 수정되었습니다.');
    }

    private function sendWelcomeApprovedMail(User $member): void
    {
        if (blank($member->email)) {
            return;
        }

        try {
            $memberLevelLabels = MemberService::memberLevelLabels();
            Mail::send('mailform.mail_welcome_approved', [
                'member' => $member,
                'approvedAt' => now(),
                'memberLevelLabel' => $memberLevelLabels[$member->member_level] ?? (string) $member->member_level,
            ], function ($message) use ($member): void {
                $message
                    ->to($member->email, $member->name)
                    ->subject('[대한기능의학회] 회원 가입 승인 및 환영 안내');
            });
        } catch (Throwable $e) {
            Log::warning('회원 가입 승인 메일 발송 실패', [
                'member_id' => $member->id,
                'email' => $member->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(User $user)
    {
        $this->memberService->deleteMember($user->id);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('backoffice.members.index')
            ->with('success', '회원이 탈퇴 처리되었습니다.');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => '선택된 회원이 없습니다.'], 400);
        }

        $this->memberService->deleteMembers($ids);
        return response()->json(['success' => true, 'message' => '선택된 회원이 탈퇴 처리되었습니다.']);
    }

    public function checkDuplicateEmail(Request $request)
    {
        $email = (string) $request->input('email');
        $excludeId = $request->input('exclude_id');
        if ($email === '') {
            return response()->json(['available' => false, 'message' => '이메일을 입력해주세요.'], 400);
        }

        $exists = $this->memberService->checkDuplicateEmail($email, $excludeId ? (int) $excludeId : null);
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? '이미 사용 중인 이메일입니다.' : '사용 가능한 이메일입니다.',
        ]);
    }

    public function checkDuplicatePhone(Request $request)
    {
        $phone = (string) $request->input('phone');
        $excludeId = $request->input('exclude_id');
        if ($phone === '') {
            return response()->json(['available' => false, 'message' => '휴대폰번호를 입력해주세요.'], 400);
        }

        $exists = $this->memberService->checkDuplicatePhone($phone, $excludeId ? (int) $excludeId : null);
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? '이미 사용 중인 휴대폰번호입니다.' : '사용 가능한 휴대폰번호입니다.',
        ]);
    }

    public function searchSchool(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        if ($keyword === '') {
            return response()->json([
                'schools' => [],
                'message' => '검색어를 입력해주세요.',
            ]);
        }

        $schools = User::query()
            ->select('school_name')
            ->whereNotNull('school_name')
            ->where('school_name', '<>', '')
            ->where('school_name', 'like', '%' . $keyword . '%')
            ->groupBy('school_name')
            ->orderBy('school_name')
            ->limit(20)
            ->pluck('school_name');

        return response()->json([
            'schools' => $schools,
            'message' => $schools->isEmpty() ? '검색 결과가 없습니다.' : null,
        ]);
    }

    public function export(Request $request)
    {
        $grades = $request->get('grades', []);
        if (! is_array($grades)) {
            $grades = [];
        }

        $filters = [
            'join_type' => $request->get('join_type', '전체'),
            'join_date_start' => $request->get('join_date_start'),
            'join_date_end' => $request->get('join_date_end'),
            'date_start' => $request->filled('date_start') ? $request->get('date_start') : $request->get('join_date_start'),
            'date_end' => $request->filled('date_end') ? $request->get('date_end') : $request->get('join_date_end'),
            'search_condition' => $request->get('search_condition', 'joinDate'),
            'marketing_consent' => $request->get('marketing_consent', []),
            'search_type' => $request->get('search_type', '전체'),
            'search_term' => $request->get('search_term', ''),
            'sort_order' => $request->get('sort_order', 'joinDate'),
            'grades' => $grades,
            'is_certified' => $request->get('is_certified', 'all'),
            'inactive_only' => $request->boolean('inactive_only'),
            'due_mode' => $request->get('due_mode', 'all'),
            'due_date' => $request->get('due_date'),
            'annual_fee' => $request->get('annual_fee', 'all'),
            'executive_status' => $request->get('executive_status', 'all'),
            'search_field' => $request->get('search_field', ''),
            'search_keyword' => $request->get('search_keyword', ''),
        ];

        $members = $this->memberService->exportMembersToCsv($filters);
        $filename = 'members_' . date('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $levelLabels = MemberService::memberLevelLabels();
        $jobLabels = MemberService::jobTypeLabels();

        $callback = static function () use ($members, $levelLabels, $jobLabels) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, [
                'No',
                '가입구분',
                '회원등급',
                '구분',
                '인정의',
                'ID',
                '이름',
                '영문명',
                '학교명',
                '휴대폰',
                '이메일',
                '직장명',
                '가입일',
                '최종로그인',
            ]);

            foreach ($members as $index => $member) {
                $joinTypeLabel = $member->join_type === 'email' ? '이메일' : ($member->join_type === 'kakao' ? '카카오' : '네이버');
                $level = $member->member_level ? ($levelLabels[$member->member_level] ?? $member->member_level) : '';
                $job = $member->job_type ? ($jobLabels[$member->job_type] ?? $member->job_type) : '';
                fputcsv($file, [
                    $index + 1,
                    $joinTypeLabel,
                    $level,
                    $job,
                    $member->certified_instructor ? '인정의' : '-',
                    $member->login_id ?? '',
                    $member->name,
                    $member->name_en ?? '',
                    $member->school_name ?? '',
                    $member->phone_number ?? '',
                    $member->email ?? '',
                    $member->workplace_name ?? '',
                    optional($member->created_at)->format('Y.m.d H:i'),
                    optional($member->last_login_at)->format('Y.m.d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function withdrawn(Request $request)
    {
        $filters = [
            'join_type' => $request->get('join_type', '전체'),
            'withdrawal_date_start' => $request->get('withdrawal_date_start'),
            'withdrawal_date_end' => $request->get('withdrawal_date_end'),
            'search_type' => $request->get('search_type', '전체'),
            'search_term' => $request->get('search_term', ''),
        ];

        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;
        $members = $this->memberService->getWithdrawnMembers($filters, $perPage);
        $joinTypes = ['전체' => '전체', 'email' => '이메일', 'kakao' => '카카오', 'naver' => '네이버'];

        return view('backoffice.members.withdrawn', compact('members', 'filters', 'joinTypes', 'perPage'));
    }

    public function restore(Request $request, int $id)
    {
        $this->memberService->restoreMember($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '회원이 복원되었습니다.',
                'redirect' => route('backoffice.members.index'),
            ]);
        }

        return redirect()->route('backoffice.members.index')->with('success', '회원이 복원되었습니다.');
    }

    public function forceDelete(int $id)
    {
        $this->memberService->forceDeleteMember($id);
        return redirect()->route('backoffice.withdrawn')->with('success', '회원이 영구 삭제되었습니다.');
    }

    public function forceDeleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => '선택된 회원이 없습니다.'], 400);
        }

        $this->memberService->forceDeleteMembers($ids);
        return response()->json(['success' => true, 'message' => '선택된 회원이 영구 삭제되었습니다.']);
    }
}

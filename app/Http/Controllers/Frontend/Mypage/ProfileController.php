<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Http\Requests\FrontendMypageLocalDoctorUpdateRequest;
use App\Http\Requests\FrontendMypageProfileUpdateRequest;
use App\Http\Requests\FrontendMypageSecessionRequest;
use App\Models\CommunityCommittee;
use App\Models\CommunityCommitteeApplication;
use App\Models\CommunityCommitteeMember;
use App\Models\MemberExecutive;
use App\Models\User;
use App\Services\Backoffice\MemberService;
use App\Services\Frontend\MypageAnnualFeeCardService;
use App\Services\Frontend\MypageCertificationSummaryService;
use App\Services\Frontend\MypageLocalDoctorService;
use App\Services\Frontend\MypageProfileUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use RendersMypageViews;

    public function __construct(
        private readonly MypageAnnualFeeCardService $annualFeeCardService,
        private readonly MypageCertificationSummaryService $certificationSummaryService,
        private readonly MypageProfileUpdateService $profileUpdateService,
        private readonly MypageLocalDoctorService $mypageLocalDoctorService,
    ) {}

    public function edit(): View
    {
        $user = $this->currentMember();

        return $this->renderMypage('profile_edit', '01', '개인정보 관리', 'profile_edit', [
            'user' => $user,
            'annualFeeCard' => $this->annualFeeCardService->resolve($user),
            'certification' => $this->certificationSummaryService->summarize($user),
            'memberLevelLabel' => MemberService::memberLevelLabels()[$user->member_level] ?? $user->member_level,
            'jobTypeLabels' => MemberService::jobTypeLabels(),
            'phoneDisplay' => $this->formatPhoneDisplay($user->phone_number),
        ]);
    }

    public function update(FrontendMypageProfileUpdateRequest $request): RedirectResponse
    {
        $this->profileUpdateService->update($this->currentMember(), $request->validated());

        return redirect()
            ->route('mypage.profile_edit')
            ->with('success', '회원정보가 수정되었습니다.');
    }

    public function checkEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $email = (string) $request->input('email');
        $exists = User::query()
            ->where('email', $email)
            ->where('id', '!=', $this->currentMember()->id)
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 사용 중인 이메일입니다.' : '사용 가능한 이메일입니다.',
        ]);
    }

    public function checkPhone(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['phone_number' => ['required', 'string']]);
        $phone = User::normalizePhone((string) $request->input('phone_number', ''));
        if ($phone === '' || ! preg_match('/^01[016789]\d{7,8}$/', $phone)) {
            return response()->json(['message' => '휴대폰 번호 형식을 확인해주세요.'], 422);
        }
        $exists = User::query()
            ->where('phone_number', $phone)
            ->where('id', '!=', $this->currentMember()->id)
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 사용 중인 휴대폰번호입니다.' : '사용 가능한 휴대폰번호입니다.',
        ]);
    }

    public function checkLicense(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['license_number' => ['required', 'string']]);
        $license = (string) $request->input('license_number');
        $exists = User::query()
            ->where('role', 'user')
            ->whereNull('withdrawn_at')
            ->where('license_number', $license)
            ->where('id', '!=', $this->currentMember()->id)
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 등록된 의사면허번호입니다.' : '사용 가능한 의사면허번호입니다.',
        ]);
    }

    public function secession(): View
    {
        return $this->renderMypage('secession', '01', '개인정보 관리', 'secession');
    }

    public function secessionStore(FrontendMypageSecessionRequest $request): RedirectResponse
    {
        $user = $this->currentMember();
        $legacy = is_array($user->legacy_import_json) ? $user->legacy_import_json : [];
        $legacy['withdraw_reason'] = (string) $request->validated('withdrawal_reason');

        $user->forceFill([
            'withdrawn_at' => now(),
            'is_active' => false,
            'legacy_import_json' => $legacy,
        ])->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('alert', "회원 탈퇴가 완료되었습니다.\n이용해 주셔서 감사합니다.");
    }

    public function hospitalInformation(): View
    {
        $user = $this->currentMember();
        $doctor = $this->mypageLocalDoctorService->findForMember($user);
        $canEdit = $this->mypageLocalDoctorService->canMemberEdit($doctor);

        return $this->renderMypage('hospital_information', '07', '병원 정보 관리하기', 'hospital_information', array_merge(
            [
                'user' => $user,
                'doctor' => $doctor,
                'canEdit' => $canEdit,
                'memberLevelLabel' => MemberService::memberLevelLabels()[$user->member_level] ?? $user->member_level,
            ],
            $this->mypageLocalDoctorService->formContext($doctor),
        ));
    }

    public function updateHospitalInformation(FrontendMypageLocalDoctorUpdateRequest $request): RedirectResponse
    {
        $this->mypageLocalDoctorService->updateForMember(
            $this->currentMember(),
            $request,
            $request->validated(),
        );

        return redirect()
            ->route('mypage.hospital_information')
            ->with('success', '병원 정보가 저장되었습니다.');
    }

    public function executiveActivities(Request $request): View
    {
        $user = $this->currentMember();
        $keyword = trim((string) $request->get('keyword', ''));
        $query = MemberExecutive::query()
            ->where('member_id', $user->id)
            ->orderByDesc('term_start_date');

        if ($keyword !== '') {
            $labels = MemberExecutive::roleLabels();
            $matchingRoles = array_keys(array_filter(
                $labels,
                static fn (string $label): bool => str_contains($label, $keyword)
            ));
            if ($matchingRoles !== []) {
                $query->whereIn('executive_role', $matchingRoles);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $this->renderMypage('executive_activities', '08', '회원 활동(임원)', 'executive_activities', [
            'executives' => $query->get(),
            'roleLabels' => MemberExecutive::roleLabels(),
        ]);
    }

    public function committeeParticipation(): View
    {
        return $this->renderMypage('committee_participation', '09', '위원회 참여 현황', 'committee_participation', [
            'committees' => $this->resolveCommitteesForMember(false),
        ]);
    }

    public function committeeParticipationAdmin(Request $request): View
    {
        abort_unless($this->currentMember()->isAdmin(), 403);

        $applications = $this->committeeApplicationsForAdmin($request);

        return $this->renderMypage('committee_participation_admin', '10', '위원회 참여 현황', 'committee_participation_admin', [
            'applications' => $applications,
            'committeeStats' => $this->committeeApplicationStats(),
            'filterStatus' => $request->get('status', 'all'),
            'filterKeyword' => $request->get('keyword'),
            'statusLabels' => [
                'PENDING' => '승인 대기',
                'APPROVED' => '참여 중',
                'REJECTED' => '반려',
            ],
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Models\CommunityCommittee> */
    private function resolveCommitteesForMember(bool $adminView): \Illuminate\Support\Collection
    {
        $user = $this->currentMember();
        if ($adminView && $user->isAdmin()) {
            return CommunityCommittee::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        $codes = is_array($user->committee_codes) ? $user->committee_codes : [];
        if ($codes === []) {
            return collect();
        }

        return CommunityCommittee::query()
            ->whereIn('id', array_map('intval', $codes))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, CommunityCommitteeApplication> */
    private function committeeApplicationsForAdmin(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $status = strtoupper(trim((string) $request->get('status', '')));
        $keyword = trim((string) $request->get('keyword', ''));

        return CommunityCommitteeApplication::query()
            ->with('committee:id,name')
            ->when(in_array($status, ['PENDING', 'APPROVED', 'REJECTED'], true), fn ($query) => $query->where('status', $status))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $like = '%'.$keyword.'%';
                $query->where(function ($q) use ($like) {
                    $q->where('applicant_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderByDesc('applied_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /** @return array{committee_capacity:int, member_count:int, total:int, pending:int, approved:int, rejected:int} */
    private function committeeApplicationStats(): array
    {
        $capacity = (int) CommunityCommittee::query()->sum('member_limit');
        $memberCount = CommunityCommitteeMember::query()->count();
        $statusCounts = CommunityCommitteeApplication::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'committee_capacity' => $capacity,
            'member_count' => $memberCount,
            'total' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['PENDING'] ?? 0),
            'approved' => (int) ($statusCounts['APPROVED'] ?? 0),
            'rejected' => (int) ($statusCounts['REJECTED'] ?? 0),
        ];
    }

    private function formatPhoneDisplay(?string $phone): string
    {
        $digits = User::normalizePhone($phone);
        if (strlen($digits) === 11) {
            return substr($digits, 0, 3).'-'.substr($digits, 3, 4).'-'.substr($digits, 7);
        }
        if (strlen($digits) === 10) {
            return substr($digits, 0, 3).'-'.substr($digits, 3, 3).'-'.substr($digits, 6);
        }

        return (string) $phone;
    }
}

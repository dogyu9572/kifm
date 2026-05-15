<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Mypage\Concerns\RendersMypageViews;
use App\Http\Requests\FrontendMypageLocalDoctorUpdateRequest;
use App\Http\Requests\FrontendMypageProfileUpdateRequest;
use App\Models\MemberExecutive;
use App\Models\User;
use App\Services\Backoffice\MemberService;
use App\Services\Frontend\MypageAnnualFeeCardService;
use App\Services\Frontend\MypageCertificationSummaryService;
use App\Services\Frontend\MypageLocalDoctorService;
use App\Services\Frontend\MypageProfileUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function committeeParticipationAdmin(): View
    {
        return $this->renderMypage('committee_participation_admin', '10', '위원회 참여 현황', 'committee_participation_admin', [
            'committees' => $this->resolveCommitteesForMember(true),
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Models\CommunityCommittee> */
    private function resolveCommitteesForMember(bool $adminView): \Illuminate\Support\Collection
    {
        $user = $this->currentMember();
        if ($adminView && $user->isAdmin()) {
            return \App\Models\CommunityCommittee::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        $codes = is_array($user->committee_codes) ? $user->committee_codes : [];
        if ($codes === []) {
            return collect();
        }

        return \App\Models\CommunityCommittee::query()
            ->whereIn('id', array_map('intval', $codes))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
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

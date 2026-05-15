<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontendMemberLoginRequest;
use App\Http\Requests\FrontendMemberRegisterRequest;
use App\Models\CommunityCommittee;
use App\Models\User;
use App\Services\Backoffice\MemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService,
    ) {}

    public function login(): View
    {
        $loginPopup = Session::pull('member_login_popup');

        return $this->renderMember('login', '01', '로그인', 'login', compact('loginPopup'));
    }

    public function loginStore(FrontendMemberLoginRequest $request): RedirectResponse
    {
        $credentials = [
            'login_id' => $request->validated('login_id'),
            'password' => $request->validated('password'),
            'role' => 'user',
        ];

        if (! Auth::attempt($credentials, false)) {
            return back()
                ->withInput($request->only('login_id'))
                ->withErrors(['login_id' => '아이디 또는 비밀번호가 올바르지 않습니다.']);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->withdrawn_at !== null) {
            Auth::logout();

            return back()
                ->withInput($request->only('login_id'))
                ->withErrors(['login_id' => '탈퇴 처리된 계정입니다.']);
        }

        if ($user->member_level === 'pending') {
            Auth::logout();
            $request->session()->regenerate();

            return redirect()
                ->route('member.login')
                ->with('member_login_popup', 'pending');
        }

        if ($user->isDormantMember()) {
            Auth::logout();
            $request->session()->regenerate();

            return redirect()
                ->route('member.login')
                ->with('member_login_popup', 'dormant');
        }

        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function dormantAuth(): View
    {
        return $this->renderMember('dormant_auth', '01', '휴면 계정 해제', 'dormant_auth');
    }

    public function passwordReset(): View
    {
        return $this->renderMember('password_reset', '01', '비밀번호 재설정', 'password_reset');
    }

    public function findId(): View
    {
        return $this->renderMember('find_id', '02', '아이디 찾기', 'find_id');
    }

    public function findIdResult(): View
    {
        return $this->renderMember('find_id_result', '02', '아이디 찾기 완료', 'find_id_result');
    }

    public function findPw(): View
    {
        return $this->renderMember('find_pw', '03', '비밀번호 찾기', 'find_pw');
    }

    public function newPassword(): View
    {
        return $this->renderMember('new_password', '03', '새 비밀번호 입력', 'new_password');
    }

    public function register(): View
    {
        $committeesForRegister = CommunityCommittee::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->renderMember('register', '04', '회원가입', 'register', compact('committeesForRegister'));
    }

    public function registerStore(FrontendMemberRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['privacy_agreed'], $validated['terms_agreed'], $validated['password_confirmation']);
        $validated['join_type'] = 'email';
        $validated['member_level'] = 'pending';

        $this->memberService->createMember($validated);

        return redirect()->route('member.register_success');
    }

    public function registerCheckEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
        ]);

        $email = (string) $request->input('email');
        $exists = $this->memberService->checkDuplicateEmail($email, null);

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 사용 중인 이메일입니다.' : '사용 가능한 이메일입니다.',
        ]);
    }

    public function registerCheckPhone(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => 'required|string',
        ], [
            'phone_number.required' => '휴대폰 번호를 입력해주세요.',
        ]);

        $phone = User::normalizePhone((string) $request->input('phone_number', ''));
        if ($phone === '' || ! preg_match('/^01[016789]\d{7,8}$/', $phone)) {
            return response()->json([
                'message' => '휴대폰 번호 형식을 확인해주세요. (숫자만 입력)',
            ], 422);
        }
        $exists = $this->memberService->checkDuplicatePhone($phone, null);

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 사용 중인 휴대폰번호입니다.' : '사용 가능한 휴대폰번호입니다.',
        ]);
    }

    public function registerCheckLicense(Request $request): JsonResponse
    {
        $request->validate([
            'license_number' => 'required|string',
        ], [
            'license_number.required' => '의사면허번호를 입력해주세요.',
        ]);

        $license = (string) $request->input('license_number');
        $exists = $this->memberService->checkDuplicateLicenseNumber($license, null);

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 등록된 의사면허번호입니다.' : '사용 가능한 의사면허번호입니다.',
        ]);
    }

    public function registerCheckLoginId(Request $request): JsonResponse
    {
        $request->validate([
            'login_id' => 'required|string|max:80',
        ], [
            'login_id.required' => '아이디를 입력해주세요.',
        ]);

        $loginId = (string) $request->input('login_id');
        $exists = User::query()->where('login_id', $loginId)->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? '이미 사용 중인 아이디입니다.' : '사용 가능한 아이디입니다.',
        ]);
    }

    public function registerSuccess(): View
    {
        return $this->renderMember('register_success', '04', '회원가입 완료', 'register_success');
    }

    /**
     * @param  array<string, mixed>  $with
     */
    private function renderMember(string $view, string $sNum, string $sName, string $slug, array $with = []): View
    {
        $page_type = 'professional';
        $gNum = '00';
        $gName = '회원서비스';
        $geName = 'Member';
        $gSlug = $slug;

        return view('member.'.$view, array_merge(
            compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'),
            $with
        ));
    }
}

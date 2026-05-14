<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function login(): View
    {
        return $this->renderMember('login', '01', '로그인', 'login');
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
        return $this->renderMember('register', '04', '회원가입', 'register');
    }

    public function registerSuccess(): View
    {
        return $this->renderMember('register_success', '04', '회원가입 완료', 'register_success');
    }

    private function renderMember(string $view, string $sNum, string $sName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = '00';
        $gName = '회원서비스';
        $geName = 'Member';
        $gSlug = $slug;

        return view('member.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}

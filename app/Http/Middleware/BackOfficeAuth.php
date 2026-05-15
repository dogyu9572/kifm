<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BackOfficeAuth
{
    /**
     * 백오피스 인증 미들웨어: 로그인 + 관리자 권한(isAdmin) 필수.
     * 일반 회원(web) 세션만 있는 경우 백오피스 로그인 화면으로 보낸다. (프론트 세션은 유지)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect('/backoffice/login');
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active || ! $user->isAdmin()) {
            return redirect('/backoffice/login')
                ->withErrors([
                    'login_id' => '백오피스 접근 권한이 없습니다.',
                ]);
        }

        return $next($request);
    }
}

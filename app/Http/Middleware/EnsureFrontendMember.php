<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 프론트 마이페이지 등 일반 회원 전용 구간: web 가드 로그인 + role === user 만 통과.
 */
class EnsureFrontendMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->role !== 'user') {
            return redirect()
                ->route('home')
                ->with('error', '마이페이지는 일반 회원 로그인 후 이용할 수 있습니다.');
        }

        return $next($request);
    }
}

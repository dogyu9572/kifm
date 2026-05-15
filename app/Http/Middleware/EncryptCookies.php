<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * JS(document.cookie)로 설정하는 popup_hide_* 쿠키는 암호화 대상에서 제외한다.
 * (암호화 미들웨어가 복호화 실패 시 값을 null로 만들어 "오늘 하루 보지 않기"가 무시되는 문제 방지)
 */
class EncryptCookies extends Middleware
{
    public function isDisabled($name): bool
    {
        if (str_starts_with((string) $name, 'popup_hide_')) {
            return true;
        }

        return parent::isDisabled($name);
    }
}

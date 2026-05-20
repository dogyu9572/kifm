<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function discussion(): Response
    {
        $code = $this->makeCode();
        Session::put('captcha.discussion', strtolower($code));

        $svg = $this->makeSvg($code);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    private function makeCode(): string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < 6; $i++) {
            $code .= $pool[random_int(0, strlen($pool) - 1)];
        }

        return $code;
    }

    private function makeSvg(string $code): string
    {
        $characters = str_split($code);
        $text = '';

        foreach ($characters as $index => $character) {
            $x = 20 + ($index * 28);
            $y = random_int(35, 48);
            $rotate = random_int(-18, 18);
            $text .= sprintf(
                '<text x="%d" y="%d" transform="rotate(%d %d %d)">%s</text>',
                $x,
                $y,
                $rotate,
                $x,
                $y,
                e($character)
            );
        }

        $noiseLines = '';
        for ($i = 0; $i < 10; $i++) {
            $noiseLines .= sprintf(
                '<line x1="%d" y1="%d" x2="%d" y2="%d" />',
                random_int(0, 210),
                random_int(0, 60),
                random_int(0, 210),
                random_int(0, 60)
            );
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="210" height="60" viewBox="0 0 210 60" role="img" aria-label="자동입력방지 코드">
    <rect width="210" height="60" fill="#f7f7f7"/>
    <g stroke="#303030" stroke-width="2" stroke-linecap="round" opacity=".75">{$noiseLines}</g>
    <g fill="#2f2f2f" font-family="Arial, Helvetica, sans-serif" font-size="32" font-weight="700">{$text}</g>
    <path d="M8 43 C40 12, 68 58, 105 28 S164 9, 202 35" fill="none" stroke="#555" stroke-width="3" opacity=".85"/>
</svg>
SVG;
    }
}

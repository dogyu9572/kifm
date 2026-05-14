<?php

namespace App\Services\Backoffice;

/**
 * CSV·입력 지역 문자열을 config(local_doctor_regions) canonical 시도로 정규화.
 */
class LocalDoctorRegionNormalizer
{
    /**
     * @return array{sido: string, warning: string|null}
     */
    public static function normalizeSido(string $raw): array
    {
        $t = trim(preg_replace('/\s+/u', ' ', $raw) ?? '');
        if ($t === '') {
            return ['sido' => '', 'warning' => null];
        }

        $aliases = config('local_doctor_regions.sido_aliases', []);
        if (isset($aliases[$t])) {
            return ['sido' => $aliases[$t], 'warning' => null];
        }

        $sidos = config('local_doctor_regions.sidos', []);
        foreach ($sidos as $canonical) {
            if ($t === $canonical) {
                return ['sido' => $canonical, 'warning' => null];
            }
        }

        return [
            'sido' => $t,
            'warning' => '시/도 매핑 실패(원문 저장): ' . $t,
        ];
    }

    /**
     * 주소 문자열에서 시군구 후보 추출(휴리스틱). 실패 시 빈 문자열.
     */
    public static function guessSigunguFromAddress(string $address): string
    {
        $a = trim($address);
        if ($a === '') {
            return '';
        }

        if (preg_match('/([가-힣]+(?:시|군|구))(?:\s|$|,)/u', $a, $m) === 1) {
            return $m[1];
        }

        return '';
    }
}

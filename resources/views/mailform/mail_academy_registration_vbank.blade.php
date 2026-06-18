<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>온라인아카데미교육 신청 완료 안내</title>
</head>
<style>
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-Regular.woff2') format('woff2');font-weight: 400;font-display: swap;}
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-SemiBold.woff2') format('woff2');font-weight: 600;font-display: swap;}
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-Bold.woff2') format('woff2');font-weight: 700;font-display: swap;}
html,body {margin:0; padding:0;}
</style>
<body>
@php
    $enrollmentModel = $enrollment ?? null;
    $course = $enrollmentModel?->course;
    $member = $enrollmentModel?->member;
    $memberName = $member?->name ?: '-';
    $academyName = $course?->title ?: '-';
    $registrationNo = $enrollmentModel?->payment_no ?: '-';
    $coursePeriodText = '-';
    if ($course?->period_start && $course?->period_end) {
        $coursePeriodText = $course->period_start->format('Y년 n월 j일').' - '.$course->period_end->format('Y년 n월 j일');
    } elseif ($enrollmentModel?->expire_at) {
        $coursePeriodText = '입금 확인일로부터 '.$enrollmentModel->expire_at->format('Y년 n월 j일').'까지';
    }
    $planName = $enrollmentModel?->payment_item_name ?: ($academyName !== '-' ? $academyName.' 수강료' : '-');
    $requestedAt = $enrollmentModel?->applied_at?->format('Y년 n월 j일 H:i:s') ?: '-';
    $depositExpectedDateText = $enrollmentModel?->bank_deposit_date?->format('Y년 n월 j일') ?: '-';
    $amountText = $enrollmentModel ? number_format((int) $enrollmentModel->payment_amount).'원' : '-';
    $bankText = trim((string) config('mypage.membership_bank_display_name').' '.(string) config('mypage.membership_bank_account_no'));
    $bankHolder = (string) config('mypage.membership_bank_holder');
    $depositorName = $enrollmentModel?->bank_depositor ?: $memberName;
@endphp

<!-- form -->
<table style="table-layout:fixed; border-collapse:collapse; border-spacing:0; width:640px; margin:0 auto; font-family:'Pretendard';">
    <tbody>
        <tr>
            <td style="padding:40px 40px 48px;"><img src="{{ url('/images/logo.png') }}" alt="" style="width:120px;"></td>
        </tr>
        <tr>
            <td style="padding:0 40px 48px;">
                <div style="font-size:24px; color:#222; font-weight:700; line-height:1.4; letter-spacing:-.02em;">온라인아카데미교육 완료 안내</div>
                <p style="font-size:16px; color:#222; line-height:1.5; letter-spacing:-.02em; margin:16px 0 40px;">
                    안녕하세요, 대한기능의학회입니다.<br>
                    {{ $academyName }} 온라인아카데미 무통장 입금 신청이 접수되었습니다.<br>
                    입금 확인 후 수강이 가능합니다.<br>
                    등록해 주신 내역을 아래와 같이 안내해 드리오니, 내용을 확인해 주시기 바랍니다.<br>
                    수강 내역 확인 시 본 안내 메일을 참고해 주세요.
                </p>
                <div style="background:#f8f8f8; border-radius:8px; padding:20px 24px;">
                    <table style="table-layout:fixed; width:100%; border-collapse:collapse; border-spacing:0;">
                        <tbody>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">등록자명</th>
                                <td style="font-size:14px; color:#0088B8; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $memberName }} 선생님</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">등록 번호</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $registrationNo }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">수강 기간</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $coursePeriodText }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">결제 항목</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $planName }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">결제 금액</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $amountText }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">결제 신청 일시</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $requestedAt }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">입금하실 계좌번호</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $bankText }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">예금주</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $bankHolder }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">입금자명</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $depositorName }}</td>
                            </tr>
                            <tr>
                                <th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:115px; text-align:left;">입금 예정일</th>
                                <td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $depositExpectedDateText }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="text-align:center; padding:54px 0 14px;">
                    <a href="{{ url('/mypage/online_training') }}" target="_blank" style="font-size:14px; color:#fff; font-weight:600; line-height:1; padding:14px 45px; background:#0088B8; border-radius:8px; text-decoration:none;">수강 내역 확인하기</a>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding:32px 40px; background:#F8F8F8; font-size:12px; color:#999; font-weight:400; line-height:14px; letter-spacing:-.02em;">
                <p>본 메일은 발신 전용이며 회신되지 않습니다.</p>
                <table style="table-layout:auto; border-collapse:collapse; border-spacing:0; margin:8px 0;">
                    <tr>
                        <td>대한기능의학회 사무국 <i style="display:inline-block; width:1px; height:8px; background:#d9d9d9; margin:0 9px;"></i></td>
                        <td>T. 010.8441.4484 <i style="display:inline-block; width:1px; height:8px; background:#d9d9d9; margin:0 9px;"></i></td>
                        <td>E. 0182253645@naver.com <i style="display:inline-block; width:1px; height:8px; background:#d9d9d9; margin:0 9px;"></i></td>
                        <td>www.kfm.or.kr</td>
                    </tr>
                </table>
                <p>© 대한기능의학회. All rights reserved.</p>
            </td>
        </tr>
    </tbody>
</table>
<!-- //form -->

</body>
</html>

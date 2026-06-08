<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="EditPlus®">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>사전등록 완료 안내</title>
</head>
<style>
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-Regular.woff2') format('woff2');font-weight: 400;font-display: swap;}
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-SemiBold.woff2') format('woff2');font-weight: 600;font-display: swap;}
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-Bold.woff2') format('woff2');font-weight: 700;font-display: swap;}
html,body {margin:0; padding:0;}
</style>
<body>
@php
	$registrationModel = $registration ?? null;
	$event = $registrationModel?->event;
	$registrantName = $registrationModel?->name ?: ($registrationModel?->member?->name ?: '-');
	$registrationNo = $registrationModel?->registration_no ?: '-';
	$paymentMethodLabels = [
		'card' => '신용카드',
		'bank_transfer' => '무통장입금',
		'bank' => '무통장입금',
	];
	$paymentStatusLabels = [
		'completed' => '결제 완료',
		'pending_payment' => '결제 대기',
		'cancel_requested' => '취소 요청',
		'cancelled' => '취소 완료',
	];
	$paymentMethodText = $paymentMethodLabels[$registrationModel?->payment_method ?? ''] ?? ($registrationModel?->payment_method ?: '-');
	$paymentStatusText = $paymentStatusLabels[$registrationModel?->payment_status ?? ''] ?? ($registrationModel?->payment_status ?: '-');
	$amountText = $registrationModel
		? number_format((int) $registrationModel->total_amount).'원 / '.$paymentStatusText.' ('.$paymentMethodText.')'
		: '-';
	$eventDateText = '-';
	if ($event?->start_at && $event?->end_at) {
		$eventDateText = $event->start_at->format('Y년 n월 j일 H:i').' - '.$event->end_at->format('H:i');
	} elseif ($event?->start_at) {
		$eventDateText = $event->start_at->format('Y년 n월 j일 H:i');
	}
	$venueText = $event?->venue ?: '-';
	$eventTitle = $event?->title ?: '학술대회';
@endphp

<!-- form -->
<table style="table-layout:fixed; border-collapse:collapse; border-spacing:0; width:640px; margin:0 auto; font-family:'Pretendard';">
	<tbody>
		<tr>
			<td style="padding:40px 40px 48px;"><img src="{{ url('/images/logo.png') }}" alt="" style="width:120px;"></td>
		</tr>
		<tr>
			<td style="padding:0 40px 48px;">
				<div style="font-size:24px; color:#222; font-weight:700; line-height:1.4; letter-spacing:-.02em;">사전등록 완료 안내</div>
				<p style="font-size:16px; color:#222; line-height:1.5; letter-spacing:-.02em; margin:16px 0 40px;">
					안녕하세요, 대한기능의학회입니다.<br>
					{{ $eventTitle }} 사전등록이 성공적으로 접수되었습니다.<br>
					<br>
					등록해 주신 내역을 아래와 같이 안내해 드리오니, 내용을 확인해 주시기 바랍니다.<br>
					행사 당일 원활한 입장을 위해 본 안내 메일을 보관해 주세요.
				</p>
				<div style="background:#f8f8f8; border-radius:8px; padding:20px 24px;">
					<table style="table-layout:fixed; width:100%; border-collapse:collapse; border-spacing:0;">
						<tbody>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">등록자명</th>
								<td style="font-size:14px; color:#0088B8; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $registrantName }} 선생님</td>
							</tr>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">등록 번호</th>
								<td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $registrationNo }}</td>
							</tr>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">결제 금액</th>
								<td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $amountText }}</td>
							</tr>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">행사 일시</th>
								<td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $eventDateText }}</td>
							</tr>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">행사 장소</th>
								<td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $venueText }}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div style="text-align:center; padding:54px 0 14px;">
					<a href="{{ url('/mypage/participation_history') }}" target="_blank" style="font-size:14px; color:#fff; font-weight:600; line-height:1; padding:14px 45px; background:#0088B8; border-radius:8px; text-decoration:none;">사전등록 내역 확인하기</a>
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

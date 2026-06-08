<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="EditPlus®">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>위원회 신청 접수 완료 안내</title>
</head>
<style>
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-Regular.woff2') format('woff2');font-weight: 400;font-display: swap;}
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-SemiBold.woff2') format('woff2');font-weight: 600;font-display: swap;}
@font-face {font-family: 'Pretendard';src: url('//cdn.jsdelivr.net/gh/projectnoonnu/pretendard@1.0/Pretendard-Bold.woff2') format('woff2');font-weight: 700;font-display: swap;}
html,body {margin:0; padding:0;}
</style>
<body>
@php
	$applicationModel = $application ?? null;
	$member = $applicationModel?->member;
	$committeeName = $applicationModel?->committee?->name ?: '-';
	$appliedAt = $applicationModel?->applied_at?->format('Y.m.d H:i') ?: '-';
	$applicantName = $member?->name ?: ($applicationModel?->applicant_name ?: '-');
@endphp

<!-- form -->
<table style="table-layout:fixed; border-collapse:collapse; border-spacing:0; width:640px; margin:0 auto; font-family:'Pretendard';">
	<tbody>
		<tr>
			<td style="padding:40px 40px 48px;"><img src="{{ url('/images/logo.png') }}" alt="" style="width:120px;"></td>
		</tr>
		<tr>
			<td style="padding:0 40px 48px;">
				<div style="font-size:24px; color:#222; font-weight:700; line-height:1.4; letter-spacing:-.02em;">위원회 신청 접수 완료 안내</div>
				<p style="font-size:16px; color:#222; line-height:1.5; letter-spacing:-.02em; margin:16px 0 40px;">
					안녕하세요, 대한기능의학회입니다.<br>
					회원님께서 신청하신 위원회 활동 접수가 정상적으로 완료되었습니다.<br>
					내부 검토 후 결과를 다시 안내해 드리겠습니다.
				</p>
				<div style="background:#f8f8f8; border-radius:8px; padding:20px 24px;">
					<table style="table-layout:fixed; width:100%; border-collapse:collapse; border-spacing:0;">
						<tbody>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">신청 위원회</th>
								<td style="font-size:14px; color:#0088B8; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $committeeName }}</td>
							</tr>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">신청 일시</th>
								<td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $appliedAt }}</td>
							</tr>
							<tr>
								<th style="font-size:14px; color:#222; font-weight:400; line-height:1.5; letter-spacing:-.02em; padding:4px 0; width:76px; text-align:left;">이름</th>
								<td style="font-size:14px; color:#222; font-weight:700; line-height:1.5; letter-spacing:-.02em; padding:4px 0;">{{ $applicantName }}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div style="text-align:center; padding:54px 0 14px;">
					<a href="{{ url('/mypage/committee_participation') }}" target="_blank" style="font-size:14px; color:#fff; font-weight:600; line-height:1; padding:14px 45px; background:#0088B8; border-radius:8px; text-decoration:none;">나의 신청 내역 확인하기</a>
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

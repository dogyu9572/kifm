@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$course = $enrollment->course;
	$statusLabel = $statusLabels[$enrollment->enrollment_status] ?? $enrollment->enrollment_status;
	$paymentStatusLabel = $paymentStatusLabels[$enrollment->payment_status] ?? $enrollment->payment_status;
	$paymentMethodValue = strtolower((string) $enrollment->payment_method);
	$isBankTransfer = in_array($paymentMethodValue, ['bank', 'bank_transfer'], true);
	$methodLabel = $isBankTransfer ? '무통장입금' : ($paymentMethodLabels[$paymentMethodValue] ?? $enrollment->payment_method);
	$memberGradeLabel = $memberGradeLabels[$enrollment->member_grade_at] ?? ($enrollment->member_grade_at ?: '-');
	$examStatusLabel = $examStatusLabels[$enrollment->exam_status] ?? ($enrollment->exam_status ?: '-');
	$receiptIssueValue = strtoupper((string) $enrollment->receipt_issue);
	$isReceiptIssued = in_array($receiptIssueValue, ['YES', 'Y', '1', 'TRUE', 'ISSUED', '발행'], true);
	$receiptTypeValue = strtoupper((string) $enrollment->receipt_type);
	$receiptTypeLabel = $receiptTypeLabels[$receiptTypeValue] ?? ($enrollment->receipt_type ?: '-');
	$isPersonalReceipt = in_array($receiptTypeValue, ['PERSONAL', 'PHONE', '휴대폰'], true) || ($receiptTypeValue === '' && $isReceiptIssued);
	$isCardReceipt = in_array($receiptTypeValue, ['CARD', 'BUSINESS', '사업자증빙용'], true);
	$isCompleted = $enrollment->enrollment_status === 'completed';
	$isPaymentCompleted = in_array($enrollment->payment_status, ['completed', 'paid'], true);
	$periodEnd = $enrollment->expire_at ?: $course?->period_end;
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="online-training-view-heading">
	<div class="inner">
		<h1 class="sub_title" id="online-training-view-heading">{{ $sName }}</h1>

		<div class="gbox flex flex_colm participation_history_view_top">
			<h2>증명서 출력</h2>
			<p>증명서 출력에 문제가 있거나 기타 문의는 대한기능의학회로 연락바랍니다.</p>
			<ul class="tel_mail_infobox flex">
				<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
				<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
			</ul>
			<div class="btns_btm flex_colm">
				@if ($isCompleted)
				<a href="{{ route('mypage.print_completion', ['enrollment_id' => $enrollment->id]) }}" target="_blank" class="btn btn_print btn_wbb">이수증 출력</a>
				@endif
				@if ($isPaymentCompleted)
				<a href="{{ route('mypage.print_receipt_save', ['enrollment_id' => $enrollment->id]) }}" target="_blank" class="btn btn_print btn_kwg btn_kwg_line8">영수증 출력</a>
				@endif
				@if (! $isCompleted && ! $isPaymentCompleted)
				<span class="btn btn_print btn_kwg btn_kwg_line8">결제 완료 후 출력 가능합니다.</span>
				@endif
			</div>
		</div>

		<div class="num_tit"><span>1</span>신청 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">강의명</th>
						<td>{{ $course?->title ?? '-' }}</td>
						<th scope="row">평점</th>
						<td>-</td>
					</tr>
					<tr>
						<th scope="row">이름</th>
						<td>{{ $enrollment->member_name ?: '-' }}</td>
						<th scope="row">회원 등급</th>
						<td>{{ $memberGradeLabel }}</td>
					</tr>
					<tr>
						<th scope="row">수강 상태</th>
						<td>{{ $statusLabel }}</td>
						<th scope="row">수강률</th>
						<td>{{ (int) $enrollment->progress_rate }}%</td>
					</tr>
					<tr>
						<th scope="row">수강 기간</th>
						<td>{{ optional($enrollment->applied_at)->format('Y.m.d') ?: '-' }} ~ {{ optional($periodEnd)->format('Y.m.d') ?: '-' }}</td>
						<th scope="row">최근 학습일</th>
						<td>{{ optional($enrollment->last_studied_at)->format('Y.m.d H:i') ?: '-' }}</td>
					</tr>
					<tr>
						<th scope="row">시험 상태</th>
						<td>{{ $examStatusLabel }}</td>
						<th scope="row">시험 점수</th>
						<td>{{ $enrollment->exam_score !== null ? $enrollment->exam_score.'점' : '-' }}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="num_tit"><span>2</span>결제 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">결제 상태</th>
						<td>{{ $paymentStatusLabel }} @if ($enrollment->paid_at) ({{ $enrollment->paid_at->format('Y.m.d') }}) @endif</td>
						<th scope="row">결제 항목</th>
						<td>{{ $enrollment->payment_item_name ?: ($course?->title ?? '-') }}</td>
					</tr>
					<tr>
						<th scope="row">쿠폰 할인</th>
						<td>-</td>
						<th scope="row">결제 금액</th>
						<td>{{ number_format((int) $enrollment->payment_amount) }}원</td>
					</tr>
					<tr>
						<th scope="row">결제 수단</th>
						<td>{{ $methodLabel ?: '-' }}</td>
						@if ($isBankTransfer)
						<th scope="row">입금자명</th>
						<td>{{ $enrollment->bank_depositor ?: '-' }}</td>
						@else
						<th scope="row">결제 번호</th>
						<td>{{ $enrollment->payment_no ?: '-' }}</td>
						@endif
					</tr>
					@if ($isBankTransfer)
					<tr>
						<th scope="row">입금 예정일</th>
						<td>{{ optional($enrollment->bank_deposit_date)->format('Y.m.d') ?: '-' }}</td>
						<th scope="row">결제 번호</th>
						<td>{{ $enrollment->payment_no ?: '-' }}</td>
					</tr>
					@endif
				</tbody>
			</table>
		</div>

		<div class="num_tit"><span>3</span>현금 영수증 발급</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">현금 영수증</th>
						<td>{{ $isReceiptIssued ? '발행 신청' : '미신청' }}</td>
						<th scope="row">발급 구분</th>
						<td>{{ $isReceiptIssued ? $receiptTypeLabel : '-' }}</td>
					</tr>
					<tr>
						<th scope="row">휴대폰 번호</th>
						<td>{{ $isReceiptIssued && $isPersonalReceipt ? ($enrollment->receipt_number ?: '-') : '-' }}</td>
						<th scope="row">현금영수증 <br>카드 번호</th>
						<td>{{ $isReceiptIssued && $isCardReceipt ? ($enrollment->receipt_number ?: '-') : '-' }}</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>
</section>

</main>

@endsection

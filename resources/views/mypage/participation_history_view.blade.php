@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$isTrainingCourse = ($participationType ?? 'academic_event') === 'training_course';
	$member = $registration->member;
	$eventTitle = $isTrainingCourse ? ($registration->training?->title ?? '-') : ($registration->event?->title ?? '-');
	$itemNames = $registration->items?->pluck('item_name')->filter()->implode(', ') ?: $eventTitle;
	$statusLabel = $paymentStatusLabels[$registration->payment_status] ?? $registration->payment_status;
	$methodLabel = $paymentMethodLabels[$registration->payment_method] ?? $registration->payment_method;
	$isCompleted = $registration->payment_status === 'completed';
	$paidAt = $registration->paid_at ?: $registration->registered_at;
	$address = trim(implode(' ', array_filter([
		$member?->workplace_zipcode ? '('.$member->workplace_zipcode.')' : null,
		$member?->workplace_address,
		$member?->workplace_address_detail,
	])));
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="participation-history-view-heading">
	<div class="inner">
		<h1 class="sub_title" id="participation-history-view-heading">{{ $sName }}</h1>

		<div class="gbox flex flex_colm participation_history_view_top">
			<h2>증명서 출력</h2>
			<p>증명서 출력에 문제가 있거나 기타 문의는 대한기능의학회로 연락바랍니다.</p>
			<ul class="tel_mail_infobox flex">
				<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
				<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
			</ul>
			<div class="btns_btm flex_colm">
				@if ($isCompleted && ! $isTrainingCourse)
				<a href="{{ route('mypage.print_participation', ['registration_id' => $registration->id]) }}" target="_blank" class="btn btn_print btn_wbb">참가증명서 출력</a>
				<a href="{{ route('mypage.print_receipt_save', ['registration_id' => $registration->id]) }}" target="_blank" class="btn btn_print btn_kwg btn_kwg_line8">영수증 출력</a>
				@elseif ($isCompleted && $isTrainingCourse)
				<a href="{{ route('mypage.print_receipt_save', ['training_payment_id' => $registration->id]) }}" target="_blank" class="btn btn_print btn_kwg btn_kwg_line8">영수증 출력</a>
				@else
				<span class="btn btn_print btn_kwg btn_kwg_line8">결제 완료 후 출력 가능합니다.</span>
				@endif
			</div>
		</div>

		<div class="num_tit"><span>1</span>신청 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">행사명</th>
						<td>{{ $eventTitle }}</td>
						<th scope="row">평점</th>
						<td>-</td>
					</tr>
					<tr>
						<th scope="row">한글 이름</th>
						<td>{{ $registration->name ?: ($member?->name ?? '-') }}</td>
						<th scope="row">영문 이름</th>
						<td>{{ $member?->name_en ?: '-' }}</td>
					</tr>
					<tr>
						<th scope="row">휴대폰번호</th>
						<td>{{ $registration->phone ?: ($member?->phone_number ?? '-') }}</td>
						<th scope="row">이메일</th>
						<td>{{ $registration->email ?: ($member?->email ?? '-') }}</td>
					</tr>
					<tr>
						<th scope="row">의사면허번호</th>
						<td>{{ $registration->license_no ?: ($member?->license_number ?? '-') }}</td>
<!-- 						<th scope="row">전공과목</th>
						<td>{{ $member?->specialty ?: '-' }}</td> -->
					</tr>
					<tr>
						<th scope="row">직장명</th>
						<td>{{ $member?->workplace_name ?: '-' }}</td>
						<th scope="row">직장 전화</th>
						<td>{{ $member?->workplace_phone ?: '-' }}</td>
					</tr>
					<tr>
						<th scope="row">주소</th>
						<td colspan="3">{{ $address !== '' ? $address : '-' }}</td>
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
						<td>
							@if ($registration->payment_status === 'pending' || $registration->payment_status === 'pending_payment')
							<span class="before_deposit">{{ $statusLabel }}</span>
							@else
							{{ $statusLabel }} @if ($paidAt) ({{ $paidAt->format('Y.m.d') }}) @endif
							@endif
						</td>
						<th scope="row">결제 항목</th>
						<td>{{ $itemNames }}</td>
					</tr>
					<tr>
						<th scope="row">쿠폰 할인</th>
						<td>-</td>
						<th scope="row">결제 금액</th>
						<td>{{ number_format((int) $registration->total_amount) }}원</td>
					</tr>
					<tr>
						<th scope="row">결제 수단</th>
						<td>{{ $methodLabel }}</td>
						@if ($registration->payment_method === 'bank_transfer')
						<th scope="row">입금 계좌</th>
						<td>
							@if (! $isTrainingCourse && ! empty($registration->bank_account_text))
								{{ $registration->bank_account_text }}
							@else
								{{ config('mypage.membership_bank_display_name').': '.config('mypage.membership_bank_account_no').' / 예금주: '.config('mypage.membership_bank_holder') }}
							@endif
						</td>
						@else
						<th scope="row">결제 번호</th>
						<td>{{ $isTrainingCourse ? ($registration->order_no ?: '-') : ($registration->registration_no ?: '-') }}</td>
						@endif
					</tr>
					@if ($registration->payment_method === 'bank_transfer')
					<tr>
						<th scope="row">입금자명</th>
						<td>{{ $registration->bank_depositor ?: '-' }}</td>
						<th scope="row">입금 예정일</th>
						<td>{{ optional($registration->bank_deposit_date)->format('Y.m.d') ?: '-' }}</td>
					</tr>
					@endif
				</tbody>
			</table>
		</div>

		@if($registration->receipt_issue === 'YES')
		<div class="num_tit"><span>3</span>현금 영수증 발급</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">현금 영수증</th>
						<td>발행 신청</td>
						<th scope="row">발급 구분</th>
						<td>{{ ['PERSONAL' => '개인소득공제용', 'BUSINESS' => '현금영수증 카드번호'][$registration->receipt_type] ?? ($registration->receipt_type ?: '-') }}</td>
					</tr>
					<tr>
						<th scope="row">휴대폰 번호</th>
						<td>{{ $registration->phone ?: ($member?->phone_number ?? '-') }}</td>
						<th scope="row">현금영수수증 <br>카드 번호</th>
						<td>{{ $registration->receipt_number ?: '-' }}</td>
					</tr>
				</tbody>
			</table>
		</div>

		@endif
		<div class="btns_btm">
			<a href="{{ route('mypage.participation_history') }}" class="btn btn_kwk">목록으로</a>
		</div>
	</div>
</section>

</main>

@endsection

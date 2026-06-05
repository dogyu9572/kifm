@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$summary = $registrationSummary ?? null;
	$member = $registration?->member ?? $currentMember ?? null;
	$source = $registration?->source_row_json ?? [];
	$phone = preg_replace('/\D/', '', (string) ($registration?->phone ?? $member?->phone_number ?? ''));
	$phoneDisplay = match (strlen($phone)) {
		11 => substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7),
		10 => substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6),
		default => $phone,
	};
	$address = trim(implode(' ', array_filter([
		! empty($source['address_postcode']) ? '(' . $source['address_postcode'] . ')' : ($member?->workplace_zipcode ? '(' . $member->workplace_zipcode . ')' : null),
		$source['address_base'] ?? $member?->workplace_address,
		$source['address_detail'] ?? $member?->workplace_address_detail,
	])));
	$paymentStatusLabel = [
		'pending_payment' => '결제 대기',
		'pending' => '입금 대기',
		'completed' => '결제 완료',
		'cancel_requested' => '취소 요청',
		'cancelled' => '취소 완료',
	][$registration?->payment_status] ?? ($registration?->payment_status ?: '-');
	$paymentMethodLabel = [
		'bank_transfer' => '무통장 입금',
		'card' => '신용카드',
		'onsite' => '현장결제',
	][$registration?->payment_method] ?? ($registration?->payment_method ?: '-');
	$isCompleted = $registration?->payment_status === 'completed';
	$isBankTransfer = $registration?->payment_method === 'bank_transfer';
	$isWaitingForDeposit = $isBankTransfer && $registration?->payment_status === 'pending';
	$isCancelable = $registration && ! in_array($registration->payment_status, ['cancel_requested', 'cancelled'], true);
@endphp
<main class="sub_area">

<section class="scon">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>
		
		<div class="member_inbox">
			@unless ($registration && $summary)
				<div class="gbox after_info">
					<h2 class="tt">등록 내역이 없습니다.</h2>
					<p>확인 가능한 사전등록 내역이 없습니다. 입력하신 정보를 다시 확인해 주세요.</p>
				</div>
				<div class="btns_btm flex_center">
<!-- 					<a href="{{ $conferenceBaseUrl }}/registration/check_member" class="btn btn_kwg">회원 등록 조회</a> -->
					<a href="{{ $conferenceBaseUrl }}/registration/form" class="btn btn_wbb">접수하기</a>
				</div>
			@else
			<div class="gbox after_info print_area">
				<h2 class="tt">증명서 출력</h2>
				<p>증명서 출력에 문제가 있거나 기타 문의는 대한기능의학회로 연락바랍니다.</p>
				<ul class="tel_mail_infobox flex_center">
					<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
					<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
				</ul>
				<div class="btns flex_center">
					@if ($isCompleted)
						<a href="{{ route('academic_conference.site.registration.print_participation', [$event->folder_name, $registration]) }}" class="btn btn_print" target="_blank">참가증명서 출력</a>
						<a href="{{ route('academic_conference.site.registration.print_receipt', [$event->folder_name, $registration]) }}" class="btn btn_print" target="_blank">영수증 출력</a>
					@else
						<button type="button" class="btn btn_print" data-print-disabled>참가증명서 출력</button>
						<button type="button" class="btn btn_print" data-print-disabled>영수증 출력</button>
					@endif
				</div>
			</div>
			
			<div class="registration_check_wrap">
				<div class="infobox" id="payer_Information">
					<h3 class="tit">결제자 정보</h3>
					<dl>
						<div>
							<dt>한글 이름</dt>
							<dd>{{ $registration->name }}</dd>
						</div>
						<div>
							<dt>휴대폰 번호</dt>
							<dd>{{ $phoneDisplay ?: '-' }}</dd>
						</div>
						<div>
							<dt>영문 이름</dt>
							<dd>{{ $member?->name_en ?: ($source['name_en'] ?? '-') }}</dd>
						</div>
						<div>
							<dt>의사면허번호</dt>
							<dd>{{ $registration->license_no ?: '-' }}</dd>
						</div>
<!-- 						<div>
							<dt>전공과목</dt>
							<dd>{{ $member?->medical_department ?: ($member?->specialty ?: ($source['major_subject'] ?? '-')) }}</dd>
						</div> -->
						<div>
							<dt>직장명</dt>
							<dd>{{ $member?->workplace_name ?: ($source['affiliated_hospital'] ?? '-') }}</dd>
						</div>
						<div>
							<dt>직장 주소</dt>
							<dd>{{ $address ?: '-' }}</dd>
						</div>
						<div>
							<dt>직장 전화</dt>
							<dd>{{ $source['workplace_phone'] ?? $member?->workplace_phone ?: '-' }}</dd>
						</div>
						<div>
							<dt>이메일</dt>
							<dd>{{ $registration->email ?: '-' }}</dd>
						</div>
					</dl>
				</div>
				
				<div class="infobox" id="payment_information">
					<h3 class="tit">결제 정보</h3>
					<dl>
						<div>
							<dt>결제 상태</dt>
							<dd class="flex">{{ $paymentStatusLabel }} @if($registration->paid_at)({{ $registration->paid_at->format('Y.m.d') }})@endif @if($isWaitingForDeposit)<strong class="c_red">입금 전</strong>@endif</dd>
						</div>
						<div>
							<dt>결제 항목</dt>
							<dd>
								@forelse ($registration->items as $item)
									{{ $item->item_name }} ({{ number_format((int) $item->price) }}원)@if(!$loop->last)<br/>@endif
								@empty
									-
								@endforelse
							</dd>
						</div>
						<div>
							<dt>쿠폰 할인</dt>
							<dd>{{ $summary['discount'] > 0 ? (($summary['coupon_code'] ?: '쿠폰') . ' (-' . number_format($summary['discount']) . '원)') : '0원' }}</dd>
						</div>
						<div>
							<dt>결제 금액</dt>
							<dd>{{ number_format($summary['total']) }}원</dd>
						</div>
						<div>
							<dt>결제 수단</dt>
							<dd>{{ $paymentMethodLabel }}</dd>
						</div>
						@if ($isBankTransfer)
							<div>
								<dt>입금 계좌</dt>
								<dd>{{ $registration->bank_account_text ?: '-' }}</dd>
							</div>
							<div>
								<dt>입금자명</dt>
								<dd>{{ $registration->bank_depositor ?: '-' }}</dd>
							</div>
							<div>
								<dt>입금 예정일</dt>
								<dd>{{ optional($registration->bank_deposit_date)->format('Y.m.d') ?: '-' }}</dd>
							</div>
						@endif
					</dl>
				</div>
			</div>
			<div class="btns_btm flex_center">
				<a href="{{ $conferenceBaseUrl }}" class="btn btn_kwg">메인 페이지로</a>
				@if ($isCancelable)
					<form method="POST" action="{{ route('academic_conference.site.registration.cancel', [$event->folder_name, $registration]) }}" class="btn">
						@csrf
						<button type="submit" class="btn btn_wbb">결제 취소</button>
					</form>
				@endif
			</div>
			@endunless
		</div>
		
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-registration-result.js') }}"></script>
@endpush

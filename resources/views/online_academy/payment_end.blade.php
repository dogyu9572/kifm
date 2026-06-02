@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$methodLabels = [
		'card' => '신용카드',
		'bank_transfer' => '무통장입금',
	];
	$methodLabel = $methodLabels[$enrollment->payment_method] ?? ($enrollment->payment_method ?: '무료');
	$isBankTransfer = $enrollment->payment_method === 'bank_transfer';
@endphp
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="online-academy-payment-end-heading">
    <div class="inner">
		<div class="inbox">
			<div class="title_area">
				@if ($isBankTransfer)
					<h1 id="online-academy-payment-end-heading" class="title">결제 신청이 <strong class="c_iden">완료</strong>되었습니다.</h1>
					<p>아래 계좌정보로 입금하시면 확인 후 수강이 가능합니다.</p>
				@else
					<h1 id="online-academy-payment-end-heading" class="title">결제가 <strong class="c_iden">완료</strong>되었습니다.</h1>
					<p>신청하신 내역을 확인해 주세요.</p>
				@endif
			</div>

			<div class="shadow_box">
				<h2 class="tit">결제 요약</h2>
				<dl>
					<div>
						<dt>결제 항목</dt>
						<dd>{{ $course->title }}</dd>
					</div>
					<div>
						<dt>결제 금액</dt>
						<dd>{{ number_format((int) $summary['subtotal']) }}원</dd>
					</div>
					<div>
						<dt>쿠폰 할인</dt>
						<dd>-{{ number_format((int) $summary['discount']) }}원</dd>
					</div>
					<div>
						<dt>최종 결제 금액</dt>
						<dd class="c_iden"><strong>{{ number_format((int) $summary['final_amount']) }}</strong>원</dd>
					</div>
				</dl>
			</div>

			@if ($isBankTransfer)
				<div class="shadow_box">
					<h2 class="tit">입금하실 계좌정보</h2>
					<dl>
						<div>
							<dt>계좌번호</dt>
							<dd>국민은행 287937-00-000083</dd>
						</div>
						<div>
							<dt>예금주</dt>
							<dd>대한기능의학회</dd>
						</div>
						<div>
							<dt>입금자명</dt>
							<dd>{{ $enrollment->bank_depositor ?: '-' }}</dd>
						</div>
						<div>
							<dt>입금 예정일</dt>
							<dd>{{ optional($enrollment->bank_deposit_date)->format('Y.m.d') ?: '-' }}</dd>
						</div>
					</dl>
				</div>
			@endif

			<div class="shadow_box">
				<h2 class="tit">상세 정보</h2>
				<dl>
					<div>
						<dt>결제 번호</dt>
						<dd><strong>{{ $enrollment->payment_no }}</strong></dd>
					</div>
					<div>
						<dt>결제 일시</dt>
						<dd>{{ optional($enrollment->paid_at ?: $enrollment->applied_at)->format('Y.m.d H:i:s') ?: '-' }}</dd>
					</div>
					<div>
						<dt>결제 수단</dt>
						<dd>{{ $methodLabel }}</dd>
					</div>
					<div>
						<dt>이름</dt>
						<dd>{{ $user->name }}</dd>
					</div>
					<div>
						<dt>이메일</dt>
						<dd>{{ $user->email }}</dd>
					</div>
					<div>
						<dt>휴대폰번호</dt>
						<dd>{{ $user->phone_number }}</dd>
					</div>
					<div>
						<dt>의사면허번호</dt>
						<dd>{{ $user->license_number ?: '-' }}</dd>
					</div>
				</dl>
			</div>

			<div class="btns_btm">
				<a href="{{ route('home') }}" class="btn btn_kwk">메인으로</a>
				<a href="{{ route('mypage.online_training') }}" class="btn btn_wkk">등록확인 페이지로</a>
			</div>
		</div>
	</div>
</section>
</main>

@endsection

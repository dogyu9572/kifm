@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@php
	$isBankTransfer = ($payment?->payment_method ?? '') === 'bank_transfer';
	$planName = $payment?->plan?->plan_name ?? '연회비';
	$amount = (int) ($payment?->amount ?? 0);
	$bankName = (string) config('mypage.membership_bank_display_name');
	$bankAccount = (string) config('mypage.membership_bank_account_no');
	$bankHolder = (string) config('mypage.membership_bank_holder');
	$depositExpectedDate = data_get($payment?->legacy_import_json, 'deposit_expected_date');
	$paymentMethodLabel = match ($payment?->payment_method) {
		'card' => '신용카드',
		'bank_transfer' => '무통장 입금',
		default => '-',
	};
@endphp
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="training-course-end-heading">
    <div class="inner">
		<div class="inbox">
		
			<div class="title_area">
				<h1 id="training-course-end-heading" class="title">결제{{ $isBankTransfer ? ' 신청이' : '가' }} <strong class="c_iden">완료</strong>되었습니다.</h1>
				<p>신청하신 내역을 확인해 주세요.</p>
			</div>

			<div class="shadow_box">
				<h2 class="tit">결제 요약</h2>
				<dl>
					<div>
						<dt>결제 항목</dt>
						<dd>{{ $planName }}</dd>
					</div>
					<div>
						<dt>결제 금액</dt>
						<dd>{{ number_format($amount) }}원</dd>
					</div>
					<div>
						<dt>최종 결제 금액</dt>
						<dd class="c_iden"><strong>{{ number_format($amount) }}</strong>원</dd>
					</div>
				</dl>
			</div>

			@if ($isBankTransfer)
			<div class="shadow_box">
				<h2 class="tit">입금하실 계좌정보</h2>
				<dl>
					<div>
						<dt>계좌번호</dt>
						<dd>{{ $bankName }} {{ $bankAccount }}</dd>
					</div>
					<div>
						<dt>예금주</dt>
						<dd>{{ $bankHolder }}</dd>
					</div>
					<div>
						<dt>입금자명</dt>
						<dd>{{ $payment?->depositor_name ?? '-' }}</dd>
					</div>
					<div>
						<dt>입금 예정일</dt>
						<dd>{{ $depositExpectedDate ?: '-' }}</dd>
					</div>
					<!-- <p class="excl">무통장 입금 완료 후 담당자 확인을 거쳐 익일 등록이 활성화됩니다.</p> -->
				</dl>
			</div>
			@endif

			<div class="shadow_box">
				<h2 class="tit">상세 정보</h2>
				<dl>
					<div>
						<dt>결제 번호</dt>
						<dd><strong>{{ $payment?->payment_no ?? '-' }}</strong></dd>
					</div>
					<div>
						<dt>{{ $isBankTransfer ? '신청 일시' : '결제 일시' }}</dt>
						<dd>{{ $payment?->requested_at?->format('Y-m-d H:i:s') ?? '-' }}</dd>
					</div>
					<div>
						<dt>결제 수단</dt>
						<dd>{{ $paymentMethodLabel }}</dd>
					</div>
					<div>
						<dt>이름</dt>
						<dd>{{ $user?->name ?? '-' }}</dd>
					</div>
					<div>
						<dt>이메일</dt>
						<dd>{{ $user?->email ?? '-' }}</dd>
					</div>
					<div>
						<dt>휴대폰번호</dt>
						<dd>{{ $user?->phone_number ?? '-' }}</dd>
					</div>
					<div>
						<dt>의사면허번호</dt>
						<dd>{{ $user?->license_number ?? '-' }}</dd>
					</div>
				</dl>
			</div>

			<div class="btns_btm">
				<a href="{{ route('home') }}" class="btn btn_kwk">메인으로</a>
				<a href="{{ route('mypage.profile_edit') }}" class="btn btn_wkk">마이페이지로</a>
			</div>
			
		</div>
	</div>
</section>
</main>

@endsection

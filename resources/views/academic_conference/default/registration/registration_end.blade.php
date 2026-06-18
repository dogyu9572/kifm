@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
    $summary = $registrationSummary ?? null;
    $member = $registration?->member ?? $currentMember ?? null;
    $source = $registration?->source_row_json ?? [];
    $checkUrl = $registration?->member_id ? $conferenceBaseUrl . '/registration/check_member' : $conferenceBaseUrl . '/registration/check_non_member';
    $phone = preg_replace('/\D/', '', (string) ($registration?->phone ?? $member?->phone_number ?? ''));
    $phoneDisplay = match (strlen($phone)) {
        11 => substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7),
        10 => substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6),
        default => $phone,
    };
    $paymentMethodLabel = [
        'bank_transfer' => '무통장입금',
        'card' => '신용카드',
        'onsite' => '현장결제',
    ][$registration?->payment_method] ?? ($registration?->payment_method ?: '-');
    $bankAccountText = $registration?->bank_account_text ?: '국민은행 287937-00-000083 / 예금주 대한기능의학회';
@endphp
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="registration-end-heading">
    <div class="inner">
		<div class="inbox">
		
			<div class="title_area">
			<h1 id="registration-end-heading" class="title">
				{{ $registration?->payment_method === 'bank_transfer' ? '결제 신청이 완료되었습니다.' : '결제가 완료되었습니다.' }}
			</h1>
				<p>신청하신 내역을 확인해 주세요.</p>
			</div>
            @unless ($registration && $summary)
                <div class="gbox">
                    <p class="c_red" role="alert">확인할 등록 신청 내역이 없습니다.</p>
                </div>
            @else
			
			<div class="shadow_box">
				<h2 class="tit">결제 요약</h2>
				<dl>
					<div>
						<dt>결제 항목</dt>
						<dd>{{ $summary['item_names'] ?: '-' }}</dd>
					</div>
					<div>
						<dt>결제 금액</dt>
						<dd>{{ number_format($summary['subtotal']) }}원</dd>
					</div>
					<div>
						<dt>쿠폰 할인</dt>
						<dd>{{ $summary['discount'] > 0 ? '-' . number_format($summary['discount']) . '원' : '0원' }}</dd>
					</div>
					<div>
						<dt>최종 결제 금액</dt>
						<dd class="c_iden"><strong>{{ number_format($summary['total']) }}</strong>원</dd>
					</div>
				</dl>
			</div>

				@if ($registration->payment_method === 'bank_transfer')
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
								<dd>{{ $registration->bank_depositor ?: '-' }}</dd>
							</div>
							<div>
								<dt>입금 예정일</dt>
								<dd>{{ optional($registration->bank_deposit_date)->format('Y.m.d') ?: '-' }}</dd>
							</div>
						</dl>
					</div>
				@endif
			
			<div class="shadow_box">
				<h2 class="tit">상세 정보</h2>
				<dl>
					<div>
						<dt>결제 번호</dt>
						<dd><strong>{{ $registration->registration_no }}</strong></dd>
					</div>
					<div>
						<dt>결제 일시</dt>
						<dd>{{ optional($registration->registered_at)->format('Y-m-d') }} &nbsp; &nbsp;{{ optional($registration->registered_at)->format('H:i:s') }}</dd>
					</div>
					<div>
						<dt>결제 수단</dt>
						<dd>{{ $paymentMethodLabel }}@if($registration->payment_method === 'bank_transfer' && $registration->bank_depositor) (입금자명: {{ $registration->bank_depositor }})@endif</dd>
					</div>
					@if ($registration->payment_method === 'bank_transfer')
						<div>
							<dt>입금 계좌</dt>
							<dd>{{ $bankAccountText }}</dd>
						</div>
						<div>
							<dt>입금 예정일</dt>
							<dd>{{ optional($registration->bank_deposit_date)->format('Y.m.d') ?: '-' }}</dd>
						</div>
					@endif
					<div>
						<dt>이름</dt>
						<dd>{{ $registration->name }}</dd>
					</div>
					<div>
						<dt>의사면허번호</dt>
						<dd>{{ $registration->license_no ?: '-' }}</dd>
					</div>					
					<div>
						<dt>직장명</dt>
						<dd>{{ $member?->workplace_name ?: ($source['affiliated_hospital'] ?? '-') }}</dd>
					</div>
					<div>
						<dt>휴대폰번호</dt>
						<dd>{{ $phoneDisplay ?: '-' }}</dd>
					</div>
				</dl>
			</div>
            @endunless
			
			<div class="btns_btm">
				<a href="{{ $conferenceBaseUrl }}" class="btn btn_kwg">메인으로</a>
				<a href="{{ $checkUrl }}" class="btn btn_wkk">등록확인 페이지로</a>
			</div>
			
		</div>
	</div>
</section>
</main>

@endsection

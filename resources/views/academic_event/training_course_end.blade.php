@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="training-course-end-heading">
    <div class="inner">
		<div class="inbox">
			<div class="title_area">
				<h1 id="training-course-end-heading" class="title">결제가 <strong class="c_iden">완료</strong>되었습니다.</h1>
				<p>신청하신 내역을 확인해 주세요.</p>
			</div>

			<div class="shadow_box">
				<h2 class="tit">결제 요약</h2>
				<dl>
					<div>
						<dt>결제 항목</dt>
						<dd>
							@foreach ($payment->items as $item)
								{{ $item->item_name }}@if (! $loop->last)<br>@endif
							@endforeach
						</dd>
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
						<dd class="c_iden"><strong>{{ number_format($summary['final_amount']) }}</strong>원</dd>
					</div>
				</dl>
			</div>

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
						<dd>{{ $payment->bank_depositor ?: '-' }}</dd>
					</div>
					<div>
						<dt>입금 예정일</dt>
						<dd>{{ optional($payment->bank_deposit_date)->format('Y-m-d') ?: '-' }}</dd>
					</div>
				</dl>
			</div>

			<div class="shadow_box">
				<h2 class="tit">상세 정보</h2>
				<dl>
					<div>
						<dt>결제 번호</dt>
						<dd><strong>{{ $payment->order_no }}</strong></dd>
					</div>
					<div>
						<dt>결제 일시</dt>
						<dd>{{ optional($payment->paid_at ?: $payment->applied_at)->format('Y-m-d H:i:s') ?: '-' }}</dd>
					</div>
					<div>
						<dt>결제 수단</dt>
						<dd>{{ $payment->payment_method === 'bank_transfer' ? '무통장입금' : '신용카드' }}</dd>
					</div>
					<div>
						<dt>이름</dt>
						<dd>{{ $payment->name }}</dd>
					</div>
					<div>
						<dt>이메일</dt>
						<dd>{{ $payment->email ?: '-' }}</dd>
					</div>
					<div>
						<dt>휴대폰번호</dt>
						<dd>{{ $payment->phone ?: '-' }}</dd>
					</div>
					<div>
						<dt>의사면허번호</dt>
						<dd>{{ $payment->license_no ?: '-' }}</dd>
					</div>
				</dl>
			</div>

			<div class="btns_btm">
				<a href="/home" class="btn btn_kwk">메인으로</a>
				<a href="/mypage/online_training" class="btn btn_wkk">등록확인 페이지로</a>
			</div>
		</div>
	</div>
</section>
</main>

@endsection

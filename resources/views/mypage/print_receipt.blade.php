@extends('layouts.frontend')
@section('title', $gName)
@section('content')
<main class="sub_area print_page" data-mypage-print>

<section class="scon receipt_wrap" aria-labelledby="receipt-heading">
	<div class="head">
		<img src="/images/logo_small.png" alt="">
		<h1 class="tit">영 수 증</h1>
	</div>
	<div class="body">
		<table>
			<tbody>
				<tr>
					<th>결제항목</th>
					<td>연회비({{ $payment->plan?->plan_name ?? '회비' }})</td>
				</tr>
				@if ($payment->payment_method === 'bank_transfer')
				<tr>
					<th>입금자명</th>
					<td>{{ $payment->depositor_name }}</td>
				</tr>
				<tr>
					<th>입금은행</th>
					<td>{{ config('mypage.membership_bank_display_name') }}</td>
				</tr>
				<tr>
					<th>계좌번호</th>
					<td>{{ config('mypage.membership_bank_account_no') }}</td>
				</tr>
				<tr>
					<th>예금주</th>
					<td>{{ config('mypage.membership_bank_holder') }}</td>
				</tr>
				<tr>
					<th>입금일시</th>
					<td>{{ optional($payment->paid_at)->format('Y.m.d H:i') }}</td>
				</tr>
				@else
				<tr>
					<th>결제수단</th>
					<td>{{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}</td>
				</tr>
				<tr>
					<th>결제일시</th>
					<td>{{ optional($payment->paid_at)->format('Y.m.d H:i') }}</td>
				</tr>
				@endif
				<tr>
					<th>입금금액</th>
					<td><strong class="c_iden">₩{{ number_format((int) $payment->amount) }}</strong></td>
				</tr>
			</tbody>
		</table>
		<p class="tac">위 현황은 사실과 같음을 증명합니다.</p>
	</div>
	<div class="foot">
		<div class="date">{{ now()->format('Y년 m월 d일') }}</div>
		<div class="copy">대한기능의학회(KIFM) <img src="/images/img_stamp.png" alt=""></div>
	</div>
</section>
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/mypage-print.js') }}"></script>
@endpush

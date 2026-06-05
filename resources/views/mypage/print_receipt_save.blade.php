@extends('layouts.frontend')
@section('title', $gName)
@section('content')
@php
	$isCourseReceipt = isset($enrollment);
	$isTrainingReceipt = isset($trainingPayment);
	if ($isTrainingReceipt) {
		$receiptNo = $trainingPayment->order_no ?: 'TRAIN-'.$trainingPayment->id;
		$itemNames = $trainingPayment->items?->pluck('item_name')->filter()->implode(', ') ?: ($trainingPayment->training?->title ?? '-');
		$paidAt = $trainingPayment->paid_at ?: $trainingPayment->registered_at;
		$paymentMethod = $trainingPayment->payment_method;
		$amount = $trainingPayment->total_amount;
		$bankDepositor = $trainingPayment->bank_depositor;
	} elseif ($isCourseReceipt) {
		$receiptNo = $enrollment->payment_no ?: 'ENR-'.$enrollment->id;
		$itemNames = $enrollment->payment_item_name ?: ($enrollment->course?->title ?? '-');
		$paidAt = $enrollment->paid_at;
		$paymentMethod = $enrollment->payment_method;
		$amount = $enrollment->payment_amount;
		$bankDepositor = $enrollment->bank_depositor;
	} else {
		$receiptNo = $registration->registration_no;
		$itemNames = $registration->items?->pluck('item_name')->filter()->implode(', ') ?: ($registration->event?->title ?? '-');
		$paidAt = $registration->paid_at ?: $registration->registered_at;
		$paymentMethod = $registration->payment_method;
		$amount = $registration->total_amount;
		$bankDepositor = $registration->bank_depositor;
	}
	$methodLabel = $methodLabels[$paymentMethod] ?? $paymentMethod;
	$isBankTransfer = in_array($paymentMethod, ['bank', 'bank_transfer'], true);
@endphp
<main class="sub_area print_page">

<section class="scon receipt_wrap pt_number" aria-labelledby="receipt-heading">
	<div class="certificate_number">
		<span>[{{ $receiptNo }}]</span>
		<div class="btns no-print">
			<button type="button" class="btn btn_kwg btn_down">PDF 다운</button>
			<button type="button" class="btn btn_kwg btn_print">인쇄하기</button>
		</div>
	</div>
	<div class="head">
		<img src="/images/logo_small.png" alt="">
		<h1 class="tit">영 수 증</h1>
	</div>
	<div class="body">
		<table>
			<tbody>
				<tr>
					<th>결제항목</th>
					<td>{{ $itemNames }}</td>
				</tr>
				<tr>
					<th>결제 수단</th>
					<td>{{ $methodLabel }}</td>
				</tr>
				@if ($isBankTransfer)
				<tr>
					<th>입금자명</th>
					<td>{{ $bankDepositor ?: '-' }}</td>
				</tr>
				@if (! $isCourseReceipt && ! $isTrainingReceipt)
				<tr>
					<th>입금계좌</th>
					<td>{{ $registration->bank_account_text ?: '-' }}</td>
				</tr>
				@endif
				@endif
				<tr>
					<th>결제일시</th>
					<td>{{ optional($paidAt)->format('Y.m.d H:i') ?: '-' }}</td>
				</tr>
				<tr>
					<th>입금금액</th>
					<td><strong class="c_iden">₩{{ number_format((int) $amount) }}</strong></td>
				</tr>
			</tbody>
		</table>
		<p class="tac">위 현황은 사실과 같음을 증명합니다.</p>
	</div>
	<div class="foot">
		<div class="date">{{ now()->format('Y년 m월 d일') }}</div>
		<div class="copy">대한기능의학회(KIFM)<!--  <img src="/images/img_stamp.png" alt=""> --></div>
	</div>
</section>
</main>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ asset('js/frontend/script_print_down.js') }}"></script>
@endpush

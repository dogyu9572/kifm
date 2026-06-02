@extends('layouts.frontend')
@section('title', $gName)
@section('content')
@php
	$itemNames = $registration->items?->pluck('item_name')->filter()->implode(', ') ?: ($registration->event?->title ?? '-');
	$paidAt = $registration->paid_at ?: $registration->registered_at;
@endphp
<main class="sub_area print_page">

<section class="scon receipt_wrap pt_number" aria-labelledby="receipt-heading">
	<div class="certificate_number">
		<span>[{{ $registration->registration_no }}]</span>
		<div class="btns no-print">
			<button type="button" class="btn btn_kwg btn_down">PDF 다운</button>
			<button type="button" class="btn btn_kwg btn_print">인쇄하기</button>
		</div>
	</div>
	<div class="head">
		<img src="/images/logo_small.png" alt="">
		<h1 class="tit">참 가 증 명 서</h1>
	</div>
	<div class="body">
		<table>
			<tbody>
				<tr>
					<th>결제항목</th>
					<td>{{ $itemNames }}</td>
				</tr>
				<tr>
					<th>이름</th>
					<td>{{ $registration->name }}</td>
				</tr>
				<tr>
					<th>면허번호</th>
					<td>{{ $registration->license_no ?: '-' }}</td>
				</tr>
				<tr>
					<th>행사명</th>
					<td>{{ $registration->event?->title ?? '-' }}</td>
				</tr>
				<tr>
					<th>입금일시</th>
					<td>{{ optional($paidAt)->format('Y.m.d H:i') ?: '-' }}</td>
				</tr>
				<tr>
					<th>입금금액</th>
					<td>{{ number_format((int) $registration->total_amount) }}원</td>
				</tr>
			</tbody>
		</table>
		<p class="tac">위 현황은 사실과 같음을 증명합니다.</p>
	</div>
	<div class="foot">
		<div class="date">{{ now()->format('Y년 m월 d일') }}</div>
		<div class="copy">대한기능의학회(KIFM) <!-- <img src="/images/img_stamp.png" alt=""> --></div>
	</div>
</section>
</main>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ asset('js/frontend/script_print_down.js') }}"></script>
@endpush

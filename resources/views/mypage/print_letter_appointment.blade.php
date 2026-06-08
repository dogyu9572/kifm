@extends('layouts.frontend')
@section('title', $gName)
@section('content')
@php
	$termStart = $executive->term_start_date?->format('Y년 n월 j일') ?: '-';
	if ($executive->is_indefinite) {
		$termEnd = '무기한';
	} elseif ($executive->term_end_date) {
		$termEnd = $executive->term_end_date->format('Y년 n월 j일');
	} else {
		$termEnd = '-';
	}
	$issuedAt = $executive->created_at ?: now();
	$certificateNo = 'KIFM'.$issuedAt->format('YmdHis').'-'.$executive->id;
	$organization = $user->workplace_name ?: ($user->school_name ?: '대한기능의학회');
@endphp
<main class="sub_area print_page">

<section class="scon receipt_wrap pt_number" aria-labelledby="receipt-heading">
	<div class="certificate_number">
		<span>[{{ $certificateNo }}]</span>
		<div class="btns no-print">
			<button type="button" class="btn btn_kwg btn_down">PDF 다운</button>
			<button type="button" class="btn btn_kwg btn_print">인쇄하기</button>
		</div>
	</div>
	<div class="head">
		<img src="/images/logo_small.png" alt="">
		<h1 class="tit">임 명 장</h1>
	</div>
	<div class="body">
		<table>
			<tbody>
				<tr>
					<th>직책명</th>
					<td>{{ $roleLabels[$executive->executive_role] ?? $executive->executive_role }}</td>
				</tr>
				<tr>
					<th>이름</th>
					<td>{{ $user->name ?: '-' }}</td>
				</tr>
				<tr>
					<th>소속</th>
					<td>{{ $organization }}</td>
				</tr>
				<tr>
					<th>임기</th>
					<td>{{ $termStart }} ~ {{ $termEnd }}</td>
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

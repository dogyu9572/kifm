@extends('layouts.frontend')
@section('title', $gName)
@section('content')
<main class="sub_area print_page">

<section class="scon receipt_wrap pt_number" aria-labelledby="receipt-heading">
	<div class="certificate_number">
		<span>[KIRM20260223182321]</span>
		<div class="btns no-print">
			<button type="button" class="btn btn_kwg btn_down">PDF 다운</button>
			<button type="button" class="btn btn_kwg btn_print">인쇄하기</button>
		</div>
	</div>
	<div class="head">
		<img src="/images/logo_small.png" alt="">
		<h1 class="tit">이 수 증</h1>
	</div>
	<div class="body">
		<table>
			<tbody>
				<tr>
					<th>강의명</th>
					<td>제12차 기능의학 영양 솔루션 과정</td>
				</tr>
				<tr>
					<th>이름</th>
					<td>홍길동</td>
				</tr>
				<tr>
					<th>면허번호</th>
					<td>12345</td>
				</tr>
				<tr>
					<th>평점</th>
					<td>20점</td>
				</tr>
			</tbody>
		</table>
		<p class="tac">위 현황은 사실과 같음을 증명합니다.</p>
	</div>
	<div class="foot">
		<div class="date">YYYY년 MM월 DD일</div>
		<div class="copy">대한기능의학회(KIFM) <img src="/images/img_stamp.png" alt=""></div>
	</div>
</section>
</main>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ asset('js/script_print_down.js') }}"></script>
@endpush
@extends('layouts.frontend')
@section('title', $gName)
@section('content')
<main class="sub_area print_page">

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
					<td>연회비(정회원)</td>
				</tr>
				<!-- 무통장 입금시 -->
				<tr>
					<th>입금자명</th>
					<td>홍길동</td>
				</tr>
				<tr>
					<th>입금은행</th>
					<td>국민은행</td>
				</tr>
				<tr>
					<th>계좌번호</th>
					<td>287937-00-000083</td>
				</tr>
				<tr>
					<th>예금주</th>
					<td>대한기능의학회</td>
				</tr>
				<tr>
					<th>입금일시</th>
					<td>2025.11.10 14:32</td>
				</tr>
				<!-- //무통장 입금시 -->
				<!-- 카드 결제시 -->
				<!-- <tr>
					<th>카드 종류</th>
					<td>KB카드</td>
				</tr>
				<tr>
					<th>카드번호</th>
					<td>123456 ***** 4000</td>
				</tr>
				<tr>
					<th>결제일시</th>
					<td>2025.11.10 14:32</td>
				</tr> -->
				<!-- //카드 결제시 -->
				<tr>
					<th>입금금액</th>
					<td><strong class="c_iden">₩200,000</strong></td>
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

<script>
$(document).ready(function() {
    window.print();
    $(window).on('afterprint', function() {
        window.close();
    });
});
</script>

@endsection
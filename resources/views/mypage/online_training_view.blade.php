@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="find-id-heading">
	<div class="inner">
		<h1 class="sub_title" id="find-id-heading">{{ $sName }}</h1>
		
		<div class="gbox flex flex_colm participation_history_view_top">
			<h2>증명서 출력</h2>
			<p>증명서 출력에 문제가 있거나 기타 문의는 대한기능의학회로 연락바랍니다.</p>
			<ul class="tel_mail_infobox flex">
				<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
				<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
			</ul>
			<div class="btns_btm flex_colm">
				<a href="/mypage/print_participation" target="_blank" class="btn btn_print btn_wbb">참가증명서 출력</a>
				<a href="/mypage/print_receipt_save" target="_blank" class="btn btn_print btn_kwg btn_kwg_line8">영수증 출력</a>
			</div>
		</div>
		
		<div class="num_tit"><span>1</span>신청 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">행사명</th>
						<td>2026년 대한기능의학회 추계학술대회</td>
						<th scope="row">평점</th>
						<td>20잠</td>
					</tr>
					<tr>
						<th scope="row">이름(한글)</th>
						<td>홍길동</td>
						<th scope="row">이름(영문)</th>
						<td>hong Gill-dong</td>
					</tr>
					<tr>
						<th scope="row">전화번호</th>
						<td>010-4564-7898</td>
						<th scope="row">휴대폰번호</th>
						<td>010-1234-5678</td>
					</tr>
					<tr>
						<th scope="row">이메일</th>
						<td>home@homepagekorea.com</td>
						<th scope="row">전공과목</th>
						<td>전공과목명</td>
					</tr>
					<tr>
						<th scope="row">면허번호</th>
						<td>11233</td>
						<th scope="row">소속병의원명</th>
						<td>정회원 연회비</td>
					</tr>
					<tr>
						<th scope="row">주소</th>
						<td colspan="3">(02637) 서울 동대문구 답십리로 288 (장안동, 씨젠 메디칼 타워) 5층 건강증진센터</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<!-- 무통장 결제 완료 -->
		<div class="num_tit"><span>2</span>결제 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">결제 상태</th>
						<td>결제 완료 (2026.02.02)</td>
						<th scope="row">결제 항목</th>
						<td>정회원 사전 등록비: 80,000원</td>
					</tr>
					<tr>
						<th scope="row">쿠폰 할인</th>
						<td>후원사 전용 10,000원 할인 쿠폰 (-10,000원)</td>
						<th scope="row">결제 금액</th>
						<td>200,000원</td>
					</tr>
					<tr>
						<th scope="row">결제 수단</th>
						<td>무통장 입금</td>
						<th scope="row">입금 계좌</th>
						<td>국민은행: 287937-00-000083 / 예금주: 대한기능의학회</td>
					</tr>
					<tr>
						<th scope="row">입금자명</th>
						<td>홍길동</td>
						<th scope="row">입금 예정일</th>
						<td>2026.02.04</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<!-- 무통장 입금 전 -->
		<div class="num_tit"><span>2</span>결제 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">결제 상태</th>
						<td><span class="before_deposit">입금 전</span></td>
						<th scope="row">결제 항목</th>
						<td>정회원 사전 등록비: 80,000원</td>
					</tr>
					<tr>
						<th scope="row">쿠폰 할인</th>
						<td>후원사 전용 10,000원 할인 쿠폰 (-10,000원)</td>
						<th scope="row">결제 금액</th>
						<td>200,000원</td>
					</tr>
					<tr>
						<th scope="row">결제 수단</th>
						<td>무통장 입금</td>
						<th scope="row">입금 계좌</th>
						<td>국민은행: 287937-00-000083 / 예금주: 대한기능의학회</td>
					</tr>
					<tr>
						<th scope="row">입금자명</th>
						<td>홍길동</td>
						<th scope="row">입금 예정일</th>
						<td>2026.02.04</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<!-- 카드 결제 -->
		<div class="num_tit"><span>2</span>결제 정보</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">결제 상태</th>
						<td>결제 완료 (2026.02.02)</td>
						<th scope="row">결제 항목</th>
						<td>정회원 사전 등록비: 80,000원</td>
					</tr>
					<tr>
						<th scope="row">쿠폰 할인</th>
						<td>후원사 전용 10,000원 할인 쿠폰 (-10,000원)</td>
						<th scope="row">결제 금액</th>
						<td>200,000원</td>
					</tr>
					<tr>
						<th scope="row">결제 수단</th>
						<td colspan="3">신용카드 (현대 1234******)</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="num_tit"><span>3</span>현금 영수증 발급</div>
		<div class="tbl row_th_bg">
			<table>
				<tbody>
					<tr>
						<th scope="row">현금 영수증</th>
						<td>발행 신청 (2026.02.02)</td>
						<th scope="row">발급 구분</th>
						<td>개인소득공제</td>
					</tr>
					<tr>
						<th scope="row">휴대폰 번호</th>
						<td>010-1234-5678</td>
						<th scope="row">현금영수수증 <br>카드 번호</th>
						<td>123456789</td>
					</tr>
				</tbody>
			</table>
		</div>
		
	</div>
</section>
	
</main>

@endsection
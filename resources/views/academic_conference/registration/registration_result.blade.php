@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>
		
		<div class="member_inbox">
			<div class="gbox after_info print_area">
				<h2 class="tt">증명서 출력</h2>
				<p>증명서 출력에 문제가 있거나 기타 문의는 대한기능의학회로 연락바랍니다.</p>
				<ul class="tel_mail_infobox flex_center">
					<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
					<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
				</ul>
				<div class="btns flex_center">
					<a href="" class="btn btn_print ">참가증명서 출력</a>
					<a href="" class="btn btn_print">영수증 출력</a>
				</div>
			</div>
			
			<div class="registration_check_wrap">
				<div class="infobox" id="payer_Information">
					<h3 class="tit">결제자 정보</h3>
					<dl>
						<div>
							<dt>이름(국문)</dt>
							<dd>홍길동</dd>
						</div>
						<div>
							<dt>휴대폰 번호</dt>
							<dd>010-2331-4187</dd>
						</div>
						<div>
							<dt>이름(영문)</dt>
							<dd>hong Gil-dong</dd>
						</div>
						<div>
							<dt>면허번호</dt>
							<dd>12155</dd>
						</div>
						<div>
							<dt>전공과목</dt>
							<dd>전공과목</dd>
						</div>
						<div>
							<dt>소속병의원명</dt>
							<dd>소속병의원명</dd>
						</div>
						<div>
							<dt>주소</dt>
							<dd>(02637) 서울 동대문구 답십리로 288 (장안동, 씨젠 메디칼 타워) 5층 건강증진센터</dd>
						</div>
						<div>
							<dt>전화번호</dt>
							<dd>010-1234-5678</dd>
						</div>
						<div>
							<dt>이메일</dt>
							<dd>home@homepagekorea.com</dd>
						</div>
					</dl>
				</div>
				
				<div class="infobox" id="payment_information">
					<h3 class="tit">결제 정보</h3>
					<dl>
						<div>
							<dt>결제 상태</dt>
							<dd class="flex">결제 완료 (2026.02.02) <strong class="c_red">입금 전</strong></dd>
						</div>
						<div>
							<dt>결제 항목</dt>
							<dd>정회원 사전 등록비 (100,000원)<br/>사전등록비 (150,000원)</dd>
						</div>
						<div>
							<dt>쿠폰 할인</dt>
							<dd>후원사 전용 10,000원 할인 쿠폰 (-10,000원)</dd>
						</div>
						<div>
							<dt>결제 금액</dt>
							<dd>200,000원</dd>
						</div>
						<div>
							<dt>결제 수단</dt>
							<dd>무통장 입금</dd>
						</div>
						<div>
							<dt>입금 계좌</dt>
							<dd>국민은행: 287937-00-000083, 예금주: 대한기능의학회</dd>
						</div>
						<div>
							<dt>입금자명</dt>
							<dd>홍길동</dd>
						</div>
						<div>
							<dt>입금 예정일</dt>
							<dd>2026.02.04</dd>
						</div>
					</dl>
				</div>
			</div>
			<div class="btns_btm flex_center">
				<a href="/academic_conference" class="btn btn_kwg">메인 페이지로</a>
				<button type="button" class="btn btn_wbb">결제 취소</button>
			</div>
		</div>
		
	</div>
</section>

</main>
@endsection
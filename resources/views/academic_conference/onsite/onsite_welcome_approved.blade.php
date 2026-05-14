@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="registration-end-heading">
    <div class="inner">
		<div class="inbox">
		
			<div class="title_area">
				<h1 id="registration-end-heading" class="title">결제가 완료되었습니다.</h1>
				<p>신청하신 내역을 확인해 주세요.</p>
			</div>
			
			<div class="gbox">
				<h2 class="tit">결제 요약</h2>
				<dl>
					<div>
						<dt>결제 항목</dt>
						<dd>정회원 연회비 사전등록비(정회원)</dd>
					</div>
					<div>
						<dt>결제 금액</dt>
						<dd>250,000원</dd>
					</div>
					<div>
						<dt>쿠폰 할인</dt>
						<dd>-10,000원</dd>
					</div>
					<div>
						<dt>최종 결제 금액</dt>
						<dd class="c_iden"><strong>200,000</strong>원</dd>
					</div>
				</dl>
			</div>
			
			<div class="gbox">
				<h2 class="tit">상세 정보</h2>
				<dl>
					<div>
						<dt>결제 번호</dt>
						<dd><strong>kirm20260223182321</strong></dd>
					</div>
					<div>
						<dt>결제 일시</dt>
						<dd>2026-02-23 &nbsp; &nbsp;18:23:21</dd>
					</div>
					<div>
						<dt>결제 수단</dt>
						<dd>신용카드 (현대 1234)</dd>
					</div>
					<div>
						<dt>이름</dt>
						<dd>홍길동</dd>
					</div>
					<div>
						<dt>소속</dt>
						<dd>대한기능의학회</dd>
					</div>
					<div>
						<dt>휴대폰번호</dt>
						<dd>010-12345-678</dd>
					</div>
					<div>
						<dt>면허번호</dt>
						<dd>513423</dd>
					</div>
				</dl>
			</div>
			
			<div class="btns_btm">
				<a href="/academic_conference" class="btn btn_kwk">메인으로</a>
			</div>
			
			<div class="go_main_timer"><strong class="c_red">20초</strong> 후 등록 페이지로 자동 이동합니다</div>
			
		</div>
	</div>
</section>
</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let timeLeft = 20; // 초기 시간 설정
    const $timerDisplay = $('.go_main_timer .c_red'); // 숫자가 표시될 요소 선택

    let countdown = setInterval(function() {
        timeLeft--;
        
        // 숫자 부분만 업데이트 (텍스트 뒤에 '초'를 붙여줌)
        $timerDisplay.text(timeLeft + '초');

        if (timeLeft <= 0) {
            clearInterval(countdown); // 타이머 중지
            window.location.href = '/academic_conference/onsite_info'; // 페이지 이동
        }
    }, 1000); // 1,000ms = 1초
});
</script>
@endpush
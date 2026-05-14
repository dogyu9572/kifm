@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="registration-end-heading">
    <div class="inner">
		<div class="inbox">
		
			<div class="title_area end_ico2">
				<h1 id="registration-end-heading" class="title">현장 등록 확인 완료</h1>
				<p>등록하신 정보를 확인했습니다.</p>
			</div>
			
			<div class="user_info">
				<h2 class="name">홍길동</h2>
				<span class="eng_name">hong gil dong</span>
				<p><strong>서울대병원 / 심장내과 전공</strong>면허번호: 513423</p>
			</div>
			
			<div class="gbox">
				<h2 class="tit">결제 요약</h2>
				<dl>
					<div>
						<dt>결제 번호</dt>
						<dd>kirm20260223182321</dd>
					</div>
					<div>
						<dt>결제 일시</dt>
						<dd>2026-02-23    18:23:21</dd>
					</div>
					<div>
						<dt>결제 수단</dt>
						<dd>신용카드 (현대 1234)</dd>
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
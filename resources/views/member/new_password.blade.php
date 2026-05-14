@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="new-password-heading">
	<div class="inner">
		<h1 class="sub_title" id="new-password-heading">{{ $sName }}</h1>
		
		<div class="member_inbox">
			<div class="tabs">
				<a href="/member/find_id">아이디 찾기</a>
				<a href="/member/find_pw" class="on">비밀번호 찾기</a>
			</div>
			<div class="inputs_li">
				<h2 class="sound_only">이메일 인증 정보 입력</h2>
				<ul>
					<li>
						<label for="dormant_pw" class="sound_only">새 비밀번호</label>
						<input type="password" class="text w100p" placeholder="새 비밀번호">
					</li>
					<li>
						<label for="dormant_pw_check" class="sound_only">새 비밀번호 확인</label>
						<input type="password" class="text w100p" placeholder="새 비밀번호 확인">
					</li>
				</ul>
				<p class="excl mt">* 영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요.</p>
			</div>
			<button type="submit" class="btn btn_wbb" onclick="layerShow('pop_password_end');">변경</button>
		</div>
		
	</div>
</section>
	
</main>

<div class="popup" id="pop_password_end">
	<div class="dm"></div>
	<div class="inbox">
		<div class="ptit">비밀번호 변경 완료</div>
		<div class="con gbox tac">비밀번호 변경이 완료되었습니다.<br/>새로운 비밀번호로 로그인해주세요.</div>
		<div class="btns flex_center">
			<button type="button" class="btn btn_wbb" onclick="location.href='/member/login'">로그인 하기</button>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
//팝업
var lastFocusedElement;
function layerShow(id) {
    lastFocusedElement = document.activeElement;
    $("#" + id).fadeIn(300, function() {
        $(this).find(".btn_wbb").focus();
    });
}
function layerHide(id) {
    $("#" + id).fadeOut(300, function() {
        if (lastFocusedElement) lastFocusedElement.focus();
    });
}
</script>
@endpush
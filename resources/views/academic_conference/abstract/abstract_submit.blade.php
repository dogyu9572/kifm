@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon">
	<div class="inner">
		<div class="sub_title mb0">{{ $sName }}</div>
		<p class="tb">회원으로 신청하신 경우, 로그인 후 확인하실 수 있습니다.</p>
		
		<div class="member_inbox">
			<form action="" method="POST" class="inputs gbox">
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
					<label for="user-id">아이디</label>
					<input type="text" id="user-id" class="text w100p" placeholder="아이디 입력" required>
				</div>
				<div class="input_row">
					<label for="user-pw">비밀번호</label>
					<input type="password" id="user-pw" class="text w100p" placeholder="비밀번호 입력" required>
				</div>
				<div class="id_set flex_between">
					<div class="checkbox"><input type="checkbox" name="save-id" id="save-id"><label for="save-id"><i aria-hidden="true"></i><span>아이디 저장</span></label></div>
					<ul class="btns">
						<li><a href="/member/find_id">아이디 찾기</a></li>
						<li><a href="/member/find_pw">비밀번호 찾기</a></li>
					</ul>
				</div>
				<button type="submit" class="btn btn_wkk" onclick="/academic_conference/abstract/form_member">로그인</button>
				<a href="/academic_conference/abstract/form_non_member" class="btn btn_kwg">비회원으로 접수하기</a>
			</form>
			
			<div class="glbox after_info">
				<h2 class="tt">대한기능의학회 사무국</h2>
				<p>정보조회가 어려우실 경우 대한기능의학회 사무국으로 <br class="pc_vw">문의해 주시기 바랍니다.</p>
				<ul class="tel_mail_infobox flex_center">
					<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
					<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
				</ul>
			</div>
		</div>
		
	</div>
</section>

</main>
@endsection
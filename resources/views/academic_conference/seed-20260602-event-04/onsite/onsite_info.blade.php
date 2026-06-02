@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@section('content')
<main class="onsite_wrap">

<section class="scon" aria-labelledby="registration-heading">
	<div class="inner">
		<h1 class="sub_title mb0" id="registration-heading">{{ $sName }}</h1>
		<p class="tb">등록 시 회원혜택을 받으시려면 회원 인증이 필요합니다.</p>
		
		<div class="member_inbox">
			<form action="" method="POST" class="inputs gbox">
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
                    <label for="user-id">아이디</label>
                    <input type="text" id="user-id" class="text w100p" placeholder="아이디" required>
                </div>
                <div class="input_row">
                    <label for="user-pw">비밀번호</label>
                    <input type="password" id="user-pw" class="text w100p" placeholder="비밀번호" required>
                </div>
				<div class="id_set">
					<ul class="btns">
						<li><a href="/member/find_id">아이디 찾기</a></li>
						<li><a href="/member/find_pw">비밀번호 찾기</a></li>
					</ul>
				</div>
				<button type="submit" class="btn btn_wkk" onclick="location.href='{{ $conferenceBaseUrl }}/onsite_member_registration'">로그인</button>
				<a href="{{ $conferenceBaseUrl }}/onsite_non_member_registration" class="btn btn_kwk">비회원으로 신청하기</a>
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

@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="registration-heading">
	<div class="inner">
		<h1 class="sub_title mb0" id="registration-heading">{{ $sName }}</h1>
		
		<div class="member_inbox">
			<ul class="tabs type_member">
				<li><a href="/academic_conference/onsite_check_registration">회원 등록</a></li>
				<li class="on"><a href="/academic_conference/onsite_check_non_registration">비회원 등록</a></li>
			</ul>
			
			<p class="tb tac">등록 시 입력한 정보를 입력해 주세요.</p>
			<form action="/academic_conference/registration/result" method="POST" class="inputs gbox">
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
                    <label for="user-name">이름</label>
                    <input type="text" id="user-name" class="text w100p" placeholder="이름을 입력해 주세요." required>
                </div>
                <div class="input_row">
                    <label for="user-email">이메일</label>
                    <input type="password" id="user-email" class="text w100p" placeholder="이메일 주소를 입력해주세요." required>
                </div>
                <div class="input_row">
                    <label for="user-phone">휴대폰 번호</label>
                    <input type="password" id="user-phone" class="text w100p" placeholder="휴대폰 번호를 입력해주세요." required>
                </div>
				<button type="submit" class="btn btn_wkk mt" onclick="location.href='/academic_conference/onsite_confirmation_complete'">사전등록 조회</button>
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
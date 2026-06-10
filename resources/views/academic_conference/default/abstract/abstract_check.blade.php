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
				<li class="on"><a href="{{ $conferenceBaseUrl }}/abstract/check_member">회원 등록</a></li>
				<li><a href="{{ $conferenceBaseUrl }}/abstract/check_non_member">비회원 등록</a></li>
			</ul>
			
			<p class="tb tac">회원으로 신청하신 경우, 로그인 후 확인하실 수 있습니다.</p>
			<form action="{{ route('member.login.store') }}" method="POST" class="inputs gbox">
				@csrf
				<input type="hidden" name="intended" value="{{ $conferenceBaseUrl }}/abstract/list">
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
                    <label for="user-id">아이디</label>
                    <input type="text" id="user-id" name="login_id" class="text w100p" placeholder="아이디 입력" required autocomplete="username">
                </div>
				@error('login_id')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
                <div class="input_row">
                    <label for="user-pw">비밀번호</label>
                    <input type="password" id="user-pw" name="password" class="text w100p" placeholder="비밀번호 입력" required autocomplete="current-password">
                </div>
				<div class="id_set flex_between">
					<div class="checkbox"><input type="checkbox" name="save-id" id="save-id"><label for="save-id"><i aria-hidden="true"></i><span>아이디 저장</span></label></div>
					<ul class="btns">
						<li><a href="{{ route('member.find_id') }}">아이디 찾기</a></li>
						<li><a href="{{ route('member.find_pw') }}">비밀번호 찾기</a></li>
					</ul>
				</div>
				@if (auth()->check() && auth()->user()?->role === 'user')
					<a href="{{ $conferenceBaseUrl }}/abstract/list" class="btn btn_wkk">초록 접수 확인</a>
				@else
					<button type="submit" class="btn btn_wkk">로그인</button>
				@endif
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

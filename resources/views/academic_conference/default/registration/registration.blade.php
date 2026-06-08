@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="registration-heading">
	<div class="inner">
		<h1 class="sub_title mb0" id="registration-heading">{{ $sName }}</h1>
		<p class="tb">등록 시 회원혜택을 받으시려면 회원 인증이 필요합니다.</p>
		
		<div class="member_inbox">
			@if (! ($canPreRegister ?? true))
				<div class="gbox after_info">
					<h2 class="tt">사전등록이 마감되었습니다.</h2>
					<p>사전등록 기간이 종료되어 신규 신청을 접수할 수 없습니다.</p>
				</div>
			@elseif (auth()->check() && auth()->user()?->role === 'user' && ($hasActiveMemberRegistration ?? false))
				<div class="gbox after_info">
					<h2 class="tt">이미 사전등록 신청 내역이 있습니다.</h2>
					<p>등록 확인 페이지에서 신청 내역을 확인해 주세요.</p>
					<a href="{{ $conferenceBaseUrl }}/registration/result" class="btn btn_wkk">등록 확인</a>
				</div>
			@else
			<form action="{{ route('member.login.store') }}" method="POST" class="inputs gbox">
				@csrf
				<input type="hidden" name="intended" value="{{ $conferenceBaseUrl }}/registration/form">
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
                    <label for="user-id">아이디</label>
                    <input type="text" id="user-id" name="login_id" class="text w100p" placeholder="아이디" required autocomplete="username">
                </div>
				@error('login_id')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
                <div class="input_row">
                    <label for="user-pw">비밀번호</label>
                    <input type="password" id="user-pw" name="password" class="text w100p" placeholder="비밀번호" required autocomplete="current-password">
                </div>
				<div class="id_set">
					<ul class="btns">
						<li><a href="{{ route('member.find_id') }}">아이디 찾기</a></li>
						<li><a href="{{ route('member.find_pw') }}">비밀번호 찾기</a></li>
					</ul>
				</div>
				@if (auth()->check() && auth()->user()?->role === 'user')
					<a href="{{ $conferenceBaseUrl }}/registration/form" class="btn btn_wkk">회원으로 신청하기</a>
				@else
					<button type="submit" class="btn btn_wkk">로그인</button>
				@endif
				<a href="{{ $conferenceBaseUrl }}/registration/form_non_member" class="btn btn_kwk">비회원으로 신청하기</a>
			</form>
			@endif
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

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
				<li><a href="{{ $conferenceBaseUrl }}/registration/check_member">회원 등록</a></li>
				<li class="on"><a href="{{ $conferenceBaseUrl }}/registration/check_non_member">비회원 등록</a></li>
			</ul>
			
			<p class="tb tac">사전 등록 시 입력하신 정보로 조회하실 수 있습니다.</p>
			<form action="{{ route('academic_conference.site.registration.check_non_member', $event->folder_name) }}" method="POST" class="inputs gbox">
				@csrf
				<h2 class="sound_only">로그인 정보 입력</h2>
				@error('lookup')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
				<div class="input_row">
                    <label for="user-name">이름</label>
                    <input type="text" id="user-name" name="name" class="text w100p" value="{{ old('name') }}" placeholder="이름을 입력해 주세요." required>
                </div>
				@error('name')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
                <div class="input_row">
                    <label for="user-email">이메일</label>
                    <input type="email" id="user-email" name="email" class="text w100p" value="{{ old('email') }}" placeholder="이메일 주소를 입력해주세요." required>
                </div>
				@error('email')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
                <div class="input_row">
                    <label for="user-phone">휴대폰 번호</label>
                    <input type="tel" id="user-phone" name="phone" class="text w100p" value="{{ old('phone') }}" placeholder="휴대폰 번호를 입력해주세요." required>
                </div>
				@error('phone')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
				<button type="submit" class="btn btn_wkk mt">사전등록 조회</button>
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

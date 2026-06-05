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
				<li class="on"><a href="{{ $conferenceBaseUrl }}/onsite_check_registration">회원 등록</a></li>
				<li><a href="{{ $conferenceBaseUrl }}/onsite_check_non_registration">비회원 등록</a></li>
			</ul>
			
			<p class="tb tac">등록 시 입력한 정보를 입력해 주세요.</p>
			<form action="{{ $conferenceBaseUrl }}/registration/result" method="POST" class="inputs gbox">
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
                    <label for="user-id">아이디</label>
                    <input type="text" id="user-id" class="text w100p" placeholder="아이디 입력" required>
                </div>
                <div class="input_row">
                    <label for="user-pw">비밀번호</label>
                    <input type="password" id="user-pw" class="text w100p" placeholder="비밀번호 입력" required>
                </div>
				<button type="submit" class="btn btn_wkk mt" onclick="location.href='{{ $conferenceBaseUrl }}/onsite_confirmation_complete'">등록 내역 조회하기</button>
			</form>
			
			<div class="after_txt">
				<p>정보가 기억나지 않으시면 안내 데스크에 문의해 주세요</p>
				<a href="{{ $conferenceBaseUrl }}/onsite_info" class="btn_link btn_kwg">현장 등록하러 가기</a>
			</div>
		</div>
		
	</div>
</section>

</main>
@endsection
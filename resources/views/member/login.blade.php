@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="login-heading">
	<div class="inner">
		<h1 class="sub_title" id="login-heading">{{ $sName }}</h1>
		
		<div class="member_inbox" id="member-login-page"@if(! empty($loginPopup)) data-login-popup="{{ $loginPopup }}"@endif>
			<form action="{{ route('member.login.store') }}" method="POST" class="inputs" id="member-login-form" novalidate>
				@csrf
				<h2 class="sound_only">로그인 정보 입력</h2>
				<div class="input_row">
                    <label for="user-id" class="sound_only">아이디</label>
                    <input type="text" id="user-id" name="login_id" class="text w100p" placeholder="아이디" value="{{ old('login_id') }}" required autocomplete="username">
                </div>
				@error('login_id')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
                <div class="input_row">
                    <label for="user-pw" class="sound_only">비밀번호</label>
                    <input type="password" id="user-pw" name="password" class="text w100p" placeholder="비밀번호" required autocomplete="current-password">
                </div>
				<div class="id_set">
					<div class="checkbox"><input type="checkbox" name="save-id" id="save-id"><label for="save-id"><i aria-hidden="true"></i><span>아이디 저장</span></label></div>
					<ul class="btns">
						<li><a href="{{ route('member.find_id') }}">아이디 찾기</a></li>
						<li><a href="{{ route('member.find_pw') }}">비밀번호 찾기</a></li>
					</ul>
				</div>
				<button type="submit" class="btn btn_wbb">로그인</button>
				<a href="{{ route('member.register') }}" class="btn btn_bwb">회원가입</a>
			</form>
		</div>
	</div>
</section>
	
</main>

<div class="popup" id="pop_awaiting">
	<div class="dm js-login-popup-close" data-popup="pop_awaiting" role="button" tabindex="0" aria-label="닫기"></div>
	<div class="inbox">
		<h2 class="ptit">회원가입 승인이 <strong class="c_iden">대기 중</strong>입니다.</h2>
		<div class="glbox">
			<p>관리자가 회원 정보를 확인하고 있습니다.<br/>승인이 완료되면 등록된 메일로 안내드리겠습니다.</p>
		</div>
		<div class="btns flex_center"><button type="button" class="btn btn_wkk js-login-popup-close" data-popup="pop_awaiting">닫기</button></div>
	</div>
</div>

<div class="popup" id="pop_sleep">
	<div class="dm js-login-popup-close" data-popup="pop_sleep" role="button" tabindex="0" aria-label="닫기"></div>
	<div class="inbox">
		<button type="button" class="btn_close js-login-popup-close" data-popup="pop_sleep">Close</button>
		<h2 class="ptit">계정이 휴면 상태로 전환되었습니다.</h2>
		<div class="glbox">
			<p>개인정보 보호를 위해 1년 이상 서비스 이용이 없어 <br class="pc_vw">계정이 휴면 처리되었습니다.<br/>본인 확인 후 다시 서비스를 이용하실 수 있습니다.</p>
		</div>
		<div class="btns flex_center"><a href="{{ route('member.dormant_auth') }}" class="btn btn_wbb">활성화 하기</a></div>
	</div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/script_popup.js') }}"></script>
<script src="{{ asset('js/frontend/member-login.js') }}"></script>
@endpush

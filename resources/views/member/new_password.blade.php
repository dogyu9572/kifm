@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="new-password-heading">
	<div class="inner">
		<h1 class="sub_title" id="new-password-heading">{{ $sName }}</h1>
		
		<form action="{{ route('member.new_password.store') }}" method="POST" class="member_inbox" novalidate>
			@csrf
			<input type="hidden" name="reset_token" value="{{ old('reset_token', $resetToken ?? '') }}">
			<div class="tabs">
				<a href="/member/find_id">아이디 찾기</a>
				<a href="/member/find_pw" class="on">비밀번호 찾기</a>
			</div>
			<div class="inputs_li">
				<h2 class="sound_only">이메일 인증 정보 입력</h2>
				<ul>
					<li>
						<label for="dormant_pw" class="sound_only">새 비밀번호</label>
						<input type="password" id="dormant_pw" name="password" class="text w100p" placeholder="새 비밀번호" required autocomplete="new-password">
						@error('password')
							<p class="c_red" role="alert">{{ $message }}</p>
						@enderror
					</li>
					<li>
						<label for="dormant_pw_check" class="sound_only">새 비밀번호 확인</label>
						<input type="password" id="dormant_pw_check" name="password_confirmation" class="text w100p" placeholder="새 비밀번호 확인" required autocomplete="new-password">
						@error('password_confirmation')
							<p class="c_red" role="alert">{{ $message }}</p>
						@enderror
					</li>
				</ul>
				<p class="excl mt">영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요.</p>
				@error('reset_token')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
			</div>
			<button type="submit" class="btn btn_wbb">변경</button>
		</form>
		
	</div>
</section>
	
</main>

@endsection

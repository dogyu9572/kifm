@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="password-reset-heading">
	<div class="inner">
		<h1 class="sub_title" id="password-reset-heading">{{ $sName }}</h1>
		
		<div class="member_inbox">
			<div class="gbox tac"><strong>휴면 해제가 완료되었습니다.<br/>안전한 이용을 위해 새 비밀번호를 입력해 주세요.</strong></div>
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
			<button type="submit" class="btn btn_wbb" onclick="location.href='/member/login'">변경 완료 및 로그인하러 가기</button>
		</div>
		
	</div>
</section>
	
</main>

@endsection
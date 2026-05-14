@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="find-pw-heading">
	<div class="inner">
		<h1 class="sub_title" id="find-pw-heading">{{ $sName }}</h1>
		
		<div class="member_inbox">
			<div class="tabs">
				<a href="/member/find_id">아이디 찾기</a>
				<a href="/member/find_pw" class="on">비밀번호 찾기</a>
			</div>
			<div class="inputs_li">
				<h2 class="sound_only">이메일 인증 정보 입력</h2>
				<ul>
					<li>
						<label for="dormant_id" class="tit">아이디<span class="c_iden">*</span></label>
						<input type="text" class="text w100p" placeholder="아이디를 입력해주세요.">
					</li>
					<li>
						<label for="dormant_email" class="tit">이메일 주소<span class="c_iden">*</span></label>
						<input type="text" class="text w100p" placeholder="이메일 주소를 입력해주세요.">
					</li>
					<li>
						<label for="dormant_phone" class="tit">휴대폰번호<span class="c_iden">*</span></label>
						<input type="text" class="text w100p" placeholder="휴대폰번호를 입력해주세요.">
					</li>
				</ul>
			</div>
			<button type="submit" class="btn btn_wbb" onclick="location.href='/member/new_password'">본인인증 후 비밀번호 찾기</button>
		</div>
		
	</div>
</section>
	
</main>

@endsection
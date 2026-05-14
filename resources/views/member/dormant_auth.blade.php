@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="dormant-auth-heading">
	<div class="inner">
		<h1 class="sub_title" id="dormant-auth-heading">{{ $sName }}</h1>
		
		<div class="member_inbox">
			<div class="gbox tac"><strong>보안을 위해 현재 휴면 계정으로 전환되었습니다.</strong><br/>이메일 인증을 완료하면 즉시 서비스를 이용하실 수 있습니다.</div>
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
			<button type="submit" class="btn btn_wbb" onclick="location.href='/member/password_reset'">이메일 인증하기</button>
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
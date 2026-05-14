@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="find-id-result-heading">
	<div class="inner">
		<h1 class="sub_title" id="find-id-result-heading">{{ $sName }}</h1>
		
		<div class="member_inbox">
			<p class="tac end_txt">개인정보 도용 피해방지를 위해 일부 정보는 *로 표기됩니다.<br/>비밀번호가 기억나지 않으실 경우 재설정이 가능합니다.</p>
			<div class="gbox tac end_box"><strong>home***</strong><p class="small">가입일 : 2026.03.01</p></div>
			<a href="/member/login" class="btn btn_wbb">로그인</a>
			<a href="/member/find_pw" class="btn btn_kwg">비밀번호 찾기</a>
		</div>
		
	</div>
</section>
	
</main>

@endsection
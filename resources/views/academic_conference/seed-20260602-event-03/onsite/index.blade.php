@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@section('content')
<main class="main_wrap">
<h1 class="sound_only">대한기능의학회 인트로</h1>

<section class="onsite_intro_wrap" aria-labelledby="intro-title">
	<img src="/images/logo.png" alt="대한기능의학회" class="logo" aria-hidden="true">
	<div class="intro_contents inner">
		<div class="tac">
			<p>KIFM {{ $event->year }} The {{ $event->year }} Korean Institute for Functional Medicine</p>
			<h2 class="title">{{ $event->title }} <strong class="c_iden">현장등록</strong> 시스템</h2>
		</div>
		<div class="links">
			<a href="{{ $conferenceBaseUrl }}/onsite_info" class="i1"><h3>현장 등록 신청하기</h3><p>아직 등록을 하지 않으신 분께서는 <br class="pc_vw" aria-hidden="true">여기서 현장 등록을 진행해 주세요.</p><i class="btn">신청하기</i></a>
			<a href="{{ $conferenceBaseUrl }}/onsite_check_registration" class="i2"><h3>등록 내역 조회하기</h3><p>이미 사전, 현장 등록(결제)를 <br class="pc_vw" aria-hidden="true">완료 하신 분은 여기서 등록 내역을 확인하세요.</p><i class="btn">조회하기</i></a>
		</div>
		<div class="intro_footer">문의사항은 안내 데스크로 방문해 주세요.</div>
	</div>
</section>
</main>
@endsection

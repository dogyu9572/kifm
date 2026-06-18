@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)
@section('content')
<main class="sub_area">
	
<article class="scon terms_area email_collection_refusal_wrap" aria-labelledby="email-collection-refusal-title">
	<h1 class="sub_title" id="email-collection-refusal-title">{{ $sName }}</h1>
	<div class="inner">
	
		<section class="blue_box email_collection_refusal_area flex_center flex_colm">
			<h2 class="sound_only">무단 수집 거부 고지</h2>
			<div class="main_tit">대한기능의학회는 본 웹사이트에 게시된 이메일 주소가 전자우편 수집 프로그램이나 <br class="pc_vw">그 밖의 기술적 장치를 이용하여 무단으로 수집되는 것을 거부합니다.</div>
			<div class="refusal_sub_info">
				<p>관련 법규: 정보통신망 이용촉진 및 정보보호 등에 관한 법률</p>
				<p>공지 사항: 이를 위반하여 무단 수집 시 관련 법령에 따라 형사 처벌될 수 있음을 유념하시기 바랍니다.</p>
			</div>
			<time datetime="2026-02-25" class="date">게시일: 2026년 2월 25일</time>
		</section>
		
	</div>
</article>
	
</main>

@endsection
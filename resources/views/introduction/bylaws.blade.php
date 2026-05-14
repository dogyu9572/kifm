@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="bylaws-heading">
	<div class="inner">
		<h1 class="sub_title" id="bylaws-heading">{{ $dName }}</h1>
		
		<div class="bylaws_wrap">
			<article class="bylaws_item">
				<h2><button type="button" class="accordion_trigger" aria-expanded="false" aria-controls="section1_content" id="section1_label">제1장 총칙 회칙<i aira-hidden="true"></i></button></h2>
				<div class="con" id="section1_content" role="region" aria-labelledby="section1_label" hidden>
					<h3>제1조 (명칭)</h3>
					<p>본 회는 대한기능의학회(이하 “본회”라고 한다)라 칭하고, 영문표기는 Korean Institute of Functional Medicine (KIFM)로 한다.</p>
					<h3>제2조(소재지)</h3>
					<p>본회의 사무소는 “경기도 수원시 영통구 월드컵로 164, 아주대학교 의과대학 10층 1013호”에 둔다.<br/>
					본회는 필요에 따라 지역별로 지부학회를 둘 수 있다.</p>
					<h3>제3조(설립 목적)</h3>
					<p>본회는 미래의학의 새로운 패러다임인 기능의학과 관련한 연구와 교육 및 제도적 정착 등을 통해 질병의 근본 원인에 대한 개인 맞춤형 접근으로 신체 기능을  회복 및 최적화함으로써  국민의 보건 향상에 이바지함을 목적으로 한다.</p>
					<h3>제4조(사업)</h3>
					<p>본회는 제3조의 목적을 달성하기 위하여 다음의 사업을 한다.</p>
					<ol>
						<li>1. 대한기능의학의 연구발표와 학술강연회 개최 사업</li>
						<li>2. 대한기능의학의 교육과 보급 사업</li>
					</ol>
				</div>
			</article>
			<article class="bylaws_item">
				<h2><button type="button" class="accordion_trigger" aria-expanded="false" aria-controls="section2_content" id="section2_label">제2장 회 원<i aira-hidden="true"></i></button></h2>
				<div class="con" id="section2_content" role="region" aria-labelledby="section2_label" hidden>
					내용
				</div>
			</article>
			<div class="revision">
				<p>2024. 4. 23. 개정</p>
				<p>2025. 9. 30. 개정</p>
			</div>
		</div>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_bylaws_index.js') }}"></script>
@endpush
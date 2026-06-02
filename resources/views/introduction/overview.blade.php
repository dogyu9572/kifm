@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area overview_wrap">

<section class="scon overview01" aria-labelledby="overview01-heading">
	<div class="inner">
		<h1 class="sub_title" id="overview01-heading">기능의학회</h1>

		<div class="copy_tit">
			<h2>증상을 넘어 <strong>원인</strong>으로, <br class="mo_vw">질병을 넘어 <strong>사람</strong>으로</h2>
			<p>대한기능의학회가 현대 의학의 한계를 넘는 <br class="mo_vw">새로운 의료의 패러다임을 제시합니다.</p>
		</div>
		<ul class="box_list">
			<li class="i1">
				<span>What is Functional Medicine</span>
				<h3>기능의학이란?</h3>
				<p>단순히 통증이나 증상을 억제하는 임시방편이 아닙니다.<br>기능의학은 환자의 유전적 특성, 환경, 생활 습관을 심층적으로 분석하여 <br class="pc_vw">질병의 근본 원인(Root Cause)을 찾아냅니다.<br>인체 스스로가 가진 자연 치유 능력을 회복시키는 것이 우리가 추구하는 진정한 치료의 시작입니다.</p>
			</li>
			<li class="i2">
				<span>Our Mission</span>
				<h3>설립 배경 및 미션</h3>
				<p>오늘날 의료 현장은 정체된 치료 효과와 환자마다 다른 반응이라는 거대한 벽에 부딪혀 있습니다.<br>대한기능의학회는 이 벽을 허물기 위해 전국의 연구자, <br class="pc_vw">교수, 개업의들이 뜻을 모아 설립한 학술 공동체입니다</p>
			</li>
		</ul>
	</div>
</section>

<section class="scon overview02" aria-labelledby="overview02-heading">
	<div class="inner">
		<div class="copy_tit">
			<h2 id="overview02-heading">우리가 기능의학을 <br class="mo_vw"><strong>실천하는 이유</strong></h2>
			<p>검증된 글로벌 기준 위에 과학적 근거를 더하고, <br class="mo_vw">전국 전문가들과 함께 성장합니다.<br>IFM의 핵심 커리큘럼을 국내 의료 현장에 뿌리내려, <br class="mo_vw">기능의학이 한국 의료의 새로운 표준이 되도록 합니다.</p>
		</div>
		<ul class="why_list">
			<li class="i1"><h3>글로벌 표준의 <strong>국산화</strong></h3><p>미국 IFM(The Institute for Functional Medicine)의 <br class="pc_vw">핵심 커리큘럼을 국내 의료 환경에 맞춰 <strong>체계적 보급</strong></p></li>
			<li class="i2"><h3><strong>과학적</strong> 근거 중심</h3><p>기능의학이 한국 의료 제도권 내에서 객관적이고 과학적인 학문으로 인정받을 수 있도록 연구와 검증을 지속 </p></li>
			<li class="i3"><h3>의료진 <strong>네트워크</strong></h3><p>전국의 전문가들이 지식을 공유하고 함께 성장하는 <br class="pc_vw">교육 프로그램을 운영</p></li>
		</ul>
	</div>
</section>

<section class="scon overview03" aria-labelledby="overview03-heading">
	<div class="inner">
		<div class="copy_tit">
			<h2 id="overview03-heading">우리의 주요 활동</h2>
			<p>글로벌 기준의 교육 보급부터 과학적 연구, <br class="mo_vw">전문가 네트워크 운영까지 <br>기능의학이 한국 의료 현장에 깊이 뿌리내릴 수 있도록 <br class="mo_vw">세 가지 핵심 활동을 이어가고 있습니다.</p>
		</div>
		<ul class="act_list">
			<li>
				<span>What We Do 01</span>
				<h3>학술 및 연구 최신 기능의학 <br class="pc_vw">트렌드 분석 및 국내 임상 데이터 구축</h3>
				<p>국내외 최신 기능의학 연구 동향을 분석하고, <br class="pc_vw">한국인 특성에 맞는 임상 데이터를 <br class="pc_vw">체계적으로 축적합니다.<br>근거 중심의 연구를 통해 기능의학의 학문적 신뢰도를 높여갑니다.</p>
			</li>
			<li>
				<span>What We Do 02</span>
				<h3>교육 프로그램 IFM 연계 커리큘럼 및 <br class="pc_vw">수준별 전문가 인증 과정 운영</h3>
				<p>미국 IFM의 공인 커리큘럼을 기반으로 입문부터 심화까지 단계별 교육 과정을 운영합니다.<br>임상 현장에서 바로 적용 가능한 실践 중심 교육으로 전문가를 양성합니다.</p>
			</li>
			<li>
				<span>What We Do 03</span>
				<h3>제도 개선 기능의학의 <br class="pc_vw">제도권 안착을 위한 정책 제안 및 대외 협력</h3>
				<p>기능의학이 국내 의료 제도 안에서 공식적으로 인정받을 수 있도록 관련 기관과 협력하고, <br class="pc_vw">정책 제안 및 입법 활동을 적극적으로 추진합니다.</p>
			</li>
		</ul>
	</div>
</section>

<section class="scon overview04" aria-labelledby="overview04-heading">
	<div class="inner">
		<div class="copy_tit mb0">
			<h2 id="overview04-heading">현대 의학의 정밀함에 <strong>기능의학의 통찰력</strong>을 더했습니다 </h2>
			<p>환자에게는 희망을, 의료진에게는 새로운 확신을 드리는 대한기능의학회와 함께 의료의 새로운 가능성을 열어가시길 바랍니다.</p>
		</div>
		<div class="end">기능의학회 일동</div>
	</div>
</section>

</main>

@endsection
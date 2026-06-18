@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')

<main class="sub_area">

<section class="scon" aria-labelledby="what_is_fm-heading">
    <div class="inner">
        <h1 class="sub_title" id="what_is_fm-heading">{{ $sName }}</h1>
    </div>

	<div class="what_is_fm_area01">
		<div class="inner">
			<div class="inbox">
				<h2 class="tit">기능의학의 시작</h2>
				<strong>검사에서 이상이 없다는데도 왜 이렇게 불편할까요? 기능의학은 그 질문에서 출발합니다.</strong>
				<p>두통, 만성 피로, 소화 불편, 두근거림… 대학병원에서 MRI, CT, 내시경, 혈액검사를 다 받았는데 “이상 없음”이라는 말을 들어본 적 있으신가요?<br>
				이렇게 명확한 병명은 없지만 분명히 몸이 불편한 상태를 의학에서는 ‘의학적으로 설명되지 않는 증상(Medically Unexplained Symptoms, MUS)’ <br class="pc_vw">
				이라고 부릅니다. 정신의학에서는 이를 신체 증상 장애(somatic symptom disorder)로 분류하기도 하지만, <br class="pc_vw">
				모든 환자를 정신과적 문제로 완벽하게 설명할 수는 없습니다. 일차 의료 기관을 방문하는 환자의 약 17%가 이에 해당한다고 보고됩니다.<br>
				기능의학은 이러한 환자들을 위해, 기존 의학이 놓쳤던 몸의 기능적 불균형을 찾아내고 회복시키는 것을 목표로 합니다.</p>
			</div>
		</div>
	</div>
	
	<div class="what_is_fm_area02">
		<div class="inner">
			<h2 class="gtit">기존 의학 VS <strong class="c_iden">기능의학</strong> <br class="mo_vw">어떻게 다른가요?</h2>
			<p class="tb">기존 의학과 기능의학은 같은 환자를 보더라도 <br class="mo_vw">접근하는 방식이 다릅니다.</p>
			<ul class="chart_box">
				<li class="left">
					<div class="tit"><h3>기존 의학</h3><p>기관 · 구조 중심</p></div>
					<ul class="list">
						<li>호흡기, 소화기 등 장기별 접근</li>
						<li>검사에서 이상 소견이 있어야 진단</li>
						<li>증상에 맞는 약물 처방 중심</li>
						<li>질병이 시작된 이후에 개입</li>
					</ul>
				</li>
				<li class="right">
					<div class="tit"><h3>기능의학</h3><p>기능 · 환자 중심</p></div>
					<ul class="list">
						<li>몸 전체의 시스템과 기능을 종합적으로 봄</li>
						<li>정상 범위 내에서도 기능 저하를 파악</li>
						<li>생활습관 · 영양 · 환경 개선 중심</li>
						<li>기능 저하 단계에서 선제적으로 접근</li>
					</ul>
				</li>
			</ul>
			<div class="btm">기능의학은 기존 의학을 대체하는 것이 아닙니다.<br>기존 의학적 진단과 치료를 기반으로 하면서, <strong class="c_iden">더 근본적인 원인을 찾아 함께 해결</strong>하는 통합적인 접근법입니다.</div>
		</div>
	</div>
		
</section>

</main>

@endsection
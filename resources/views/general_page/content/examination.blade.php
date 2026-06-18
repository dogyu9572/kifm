@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')

<main class="sub_area">

<section class="scon" aria-labelledby="examination-heading">
    <div class="inner">
        <h1 class="sub_title" id="examination-heading">{{ $sName }}</h1>

        <div class="examination_area">
            
			<h2 class="btit">기능의학 검사 개요</h2>
			<p>기능의학 검사는 증상, 병력, 생활습관, 영양상태, 스트레스, 장 건강, 호르몬 균형 등을 함께 살펴 건강 상태를 더 입체적으로 이해하기 위한 참고 자료입니다.<br>
			그렇다고 모든 검사가 모든 사람에게 필요한 것은 아닙니다. 검사는 현재 증상, 기존 검사 결과, 복용 중인 약물, 생활습관, 질환력 등을 종합적으로 고려하여 의사 상담 후 개인에게 필요한 항목을 선택합니다.<br>
			검사 결과는 단독으로 질병을 진단하거나 치료 방향을 결정하는 근거가 되기보다, 진료 과정에서 참고하는 자료로 이해하는 것이 좋습니다. 결과 해석은 반드시 증상과 진찰 소견, <br class="pc_vw">
			기존 혈액검사 및 영상검사 결과와 함께 종합적으로 이루어져야 합니다.</p>
			
            <h2 class="btit">주요 기능의학 검사 설명</h2>
			<ol class="list">
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination01.svg" alt=""></div>
					<div class="txt">
						<h3><span>1</span>소변 유기산 검사</h3>
						<ul class="dots_list">
							<li>에너지 대사, 영양소 이용, 산화스트레스 관련 정보를 봅니다.</li>
							<li>피로, 집중저하, 소화불편 평가에 참고할 수 있습니다.</li>
							<li>식사·보충제·약물의 영향을 받을 수 있습니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination02.svg" alt=""></div>
					<div class="txt">
						<h3><span>2</span>수소호기검사</h3>
						<ul class="dots_list">
							<li>복부팽만, 가스, 설사·변비와 관련된 장 기능을 봅니다.</li>
							<li>소장 내 세균 과증식이 의심될 때 참고할 수 있습니다.</li>
							<li>검사 준비와 방법에 따라 결과가 달라질 수 있습니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination03.svg" alt=""></div>
					<div class="txt">
						<h3><span>3</span>장내미생물 검사</h3>
						<ul class="dots_list">
							<li>장내 미생물의 전반적 구성을 살펴봅니다.</li>
							<li>장 건강 관리의 참고 자료가 될 수 있습니다.</li>
							<li>결과는 증상, 식습관, 생활습관과 함께 해석해야 합니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination04.svg" alt=""></div>
					<div class="txt">
						<h3><span>4</span>음식 IgG 검사</h3>
						<ul class="dots_list">
							<li>특정 음식에 대한 IgG 반응을 봅니다.</li>
							<li>음식과 증상의 관련성을 참고할 때 보조적으로 활용됩니다.</li>
							<li>급성 알레르기를 확인하는 음식 알레르기 검사와는 다른 검사로 <br class="pc_vw">해석에 주의가 필요합니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination05.svg" alt=""></div>
					<div class="txt">
						<h3><span>5</span>기본 혈액·영양 검사</h3>
						<ul class="dots_list">
							<li>혈당, 지질, 갑상선, 비타민 D·B12, 철 상태 등을 봅니다.</li>
							<li>피로, 체중변화, 대사이상 평가의 기본 검사입니다.</li>
							<li>증상과 병력을 함께 해석해야 합니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination06.svg" alt=""></div>
					<div class="txt">
						<h3><span>6</span>호르몬 검사</h3>
						<ul class="dots_list">
							<li>갑상선, 인슐린, 성호르몬, 코르티솔, DHEA-S 등을 봅니다.</li>
							<li>수면, 체중, 피로, 생리·갱년기 증상 평가에 참고합니다.</li>
							<li>검사 시점, 스트레스, 약물의 영향을 받을 수 있습니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination07.svg" alt=""></div>
					<div class="txt">
						<h3><span>7</span>HRV 검사(심박변이도)</h3>
						<ul class="dots_list">
							<li>심박변이도를 통해 자율신경 균형과 스트레스·회복 상태를 봅니다.</li>
							<li>수면 부족, 피로, 스트레스 관리에 참고할 수 있습니다.</li>
							<li>한 번의 수치보다 반복 측정의 경향이 중요합니다.</li>
						</ul>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_examination08.svg" alt=""></div>
					<div class="txt">
						<h3><span>8</span>모발 미네랄 검사</h3>
						<ul class="dots_list">
							<li>일부 미네랄과 중금속 노출 경향을 봅니다.</li>
							<li>영양·환경 요인을 이해하는 보조 자료로 참고할 수 있습니다.</li>
							<li>외부 오염의 영향을 받을 수 있어 단독 진단에는 한계가 있습니다.</li>
						</ul>
					</div>
				</li>
			</ol>
			<div class="excl_wrap gbox">
				<h2 class="excl">기억하세요</h2>
				<ul class="dots_list">
					<li>검사는 진료의 일부입니다.</li>
					<li>모든 사람에게 모든 검사가 필요한 것은 아닙니다.</li>
					<li>결과는 의사 상담 후 종합적으로 해석해야 합니다.</li>
					<li>본 내용은 일반적인 건강 정보이며, 개별 진료를 대체하지 않습니다. 증상이 있거나 검사 결과가 궁금한 경우 의료진과 상담하시기 바랍니다.</li>
				</ul>
			</div>
        </div>
    </div>
</section>

</main>

@endsection
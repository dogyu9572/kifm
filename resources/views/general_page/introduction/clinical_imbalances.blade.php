@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')

<main class="sub_area">

<section class="scon" aria-labelledby="clinical_imbalances-heading">
    <div class="inner">
        <h1 class="sub_title" id="clinical_imbalances-heading">{{ $sName }}</h1>

        <div class="clinical_imbalances_area">
            <div class="blus_box">기능의학에서는 다양한 증상의 근본 원인을 7가지 핵심 불균형 (core clinical imbalances)으로 이해합니다.<br>하나의 불균형이 여러 증상들을 동시에 일으킬 수 있고, 여러 불균형이 동시에 존재하면서 서로 영향을 주기도 합니다.</div>
			<h2 class="sound_only">7가지 핵심 임상 불균형 목록</h2>
			<ul class="list">
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances01.svg" alt=""></div>
					<div class="txt">
						<h3>소화 · 흡수 (Assimilation)</h3>
						<p>음식물을 소화·흡수하고 장내 환경을 건강하게 유지하는 기능이 복부팽만, 소화불량, 변비·설사, 피로감, 피부질환, 영양 결핍과 관련될 수 있습니다.<br>
						대표적으로 소화효소 · 위산 부족, 장내 세균 불균형(dysbiosis), 장 점막 기능 저하 및 장 투과성 증가(leaky gut)가 영향을 미칠 수 있습니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances02.svg" alt=""></div>
					<div class="txt">
						<h3>구조적 균형 (Structural Integrity)</h3>
						<p>신체 구조와 세포 수준의 구조적 건강이 만성 통증, 두통, 관절통, <br class="pc_vw">
						운동 기능 저하 및 피로와 관련 있을 수 있습니다.<br>
						대표적으로 척추·근골격계 불균형(측만증, 일자목, 턱관절 장애), <br class="pc_vw">
						세포막의 지방산 불균형 등 미시적 구조 문제까지 포함합니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances03.svg" alt=""></div>
					<div class="txt">
						<h3>호르몬 · 신경전달 (Communication)</h3>
						<p>호르몬과 신경전달물질을 통한 인체 내 정보 전달 체계가 수면장애, 불안, 우울, <br class="pc_vw">
						집중력 저하, 체중증가, 만성 피로와 관련 있을 수 있습니다.<br>
						갑상선 기능 이상, 부신의 스트레스 반응 이상, 성호르몬 불균형, 인슐린 저항성 외 <br class="pc_vw">
						도파민, 세로토닌, GABA 등의 신경전달물질 불균형 등이 영향을 미치게 됩니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances04.svg" alt=""></div>
					<div class="txt">
						<h3>순환 · 운반 (Transport)</h3>
						<p>영양소, 산소, 호르몬 및 노폐물을 운반하는 체계의 문제가 손발 저림, <br class="pc_vw">
						부종, 피로, 운동능력의 저하 및 회복력 감소에 영향을 미칠 수 있습니다.<br>
						심혈관 기능 저하, 혈액순환 장애, 림프 순환 저하, <br class="pc_vw">
						탈수 및 미세순환 이상 등 장애가 포함됩니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances05.svg" alt=""></div>
					<div class="txt">
						<h3>해독 · 배출 (Biotransformation & Elimination)</h3>
						<p>체내·외 독소를 처리하고 배출하는 기능으로 간의 1·2단계 해독 기능 저하, <br class="pc_vw">
						환경독소의 노출, 중금속 축적, 미세플라스틱 및 내분비교란물질의 노출, <br class="pc_vw">
						장·신장·피부를 통한 배설 기능 저하가 관련될 수 있습니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances06.svg" alt=""></div>
					<div class="txt">
						<h3>에너지 생성 (Energy)</h3>
						<p>세포 내 미토콘드리아에서 에너지를 생산하는 과정의 이상이 만성 피로, <br class="pc_vw">
						운동능력 저하, 비만, 대사증후군, 브레인 포그를 일으킬 수 있습니다.<br>
						미토콘드리아 기능의 저하, 혈당 대사의 이상, 지방산 산화 장애 및 <br class="pc_vw">
						영양소 결핍 등이 관련될 수 있습니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_clinical_imbalances07.svg" alt=""></div>
					<div class="txt">
						<h3>면역 · 방어 · 수복 (Defense & Repair)</h3>
						<p>인체를 보호하고 손상된 조직을 회복시키는 기능이 알레르기, 자가면역질환, <br class="pc_vw">
						만성 통증, 잦은 감염이나 회복의 지연을 일으킬 수 있습니다.<br>
						만성 염증, 면역 기능의 저하, 자가면역 반응, 반복 감염 및 <br class="pc_vw">
						조직 회복 능력 저하가 관련될 수 있습니다.</p>
					</div>
				</li>
			</ul>
        </div>
    </div>
</section>

</main>

@endsection
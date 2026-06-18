@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')

<main class="sub_area">

<section class="scon" aria-labelledby="process-heading">
    <div class="">
        <h1 class="sub_title" id="process-heading">{{ $sName }}</h1>
    </div>

	<div class="process_area">
		<div class="inner">
			<h2 class="gtit tac">모든 현상에는 <strong class="c_iden">원인</strong>이 있다</h2>
			<p class="tb">기능의학은 “모든 현상에는 원인이 있다”는 원칙에서 출발합니다.<br>증상의 원인을 함께 찾고, 스스로 건강을 조절할 수 있다는 주체감을 회복하는 것 <br class="mo_vw">— 그것이 기능의학이 추구하는 진료입니다.</p>
			<ul class="list">
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_process01.svg" alt=""></div>
					<div class="txt">
						<span>01</span>
						<h3>환자의 이야기를 완성합니다</h3>
						<p>유전적 소인, 생활 환경, 악화 계기를 종합해 <br class="pc_vw">
						“왜 지금 이 증상이 생겼는지”의 <br class="pc_vw">
						이야기를 만들어 드립니다. 기능의학에서는 이를 ‘retelling the patient’s story’라고 합니다.<br>
						원인을 찾아주는 것만으로도 환자의 불안이 해소되고 <br class="pc_vw">
						치료 순응도가 높아집니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_process02.svg" alt=""></div>
					<div class="txt">
						<span>02</span>
						<h3>기능의학 검사로 불균형을 확인합니다</h3>
						<p>일반 혈액검사 외에도 <br class="pc_vw">
						모발 미네랄 검사(중금속·미네랄 상태), <br class="pc_vw">
						소변 유기산 검사(대사·에너지·해독 기능), <br class="pc_vw">
						심박변이도(HRV) 검사(자율신경 균형), <br class="pc_vw">
						부신·성장 호르몬 검사 등 <br class="pc_vw">
						최신 기능 검사를 활용합니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_process03.svg" alt=""></div>
					<div class="txt">
						<span>03</span>
						<h3>개인 맞춤 치료로 불균형을 개선합니다</h3>
						<p>영양 치료(경구·정맥), 생활습관 교정, <br class="pc_vw">
						5R 장 건강 프로그램(Remove, Replace, Reinoculate, Repair, Rebalance), 호르몬 보충, <br class="pc_vw">
						킬레이션 치료 등을 불균형의 종류와 <br class="pc_vw">
						정도에 맞게 적용합니다. 필요한 경우 <br class="pc_vw">
						기존 약물 치료도 병행합니다.</p>
					</div>
				</li>
				<li><div class="icon" aria-hidden="true"><img src="/images/icon_process04.svg" alt=""></div>
					<div class="txt">
						<span>04</span>
						<h3>지속적으로 확인하며 함께 나아갑니다</h3>
						<p>생활습관 교정은 신체 변화를 체감하기까지 <br class="pc_vw">
						보통 3~6개월의 꾸준한 실천이 필요합니다.<br>
						조급해하지 않고 주기적인 재평가와 함께 <br class="pc_vw">
						장기적인 건강 관리를 함께 해나갑니다.</p>
					</div>
				</li>
			</ul>
		</div>
	</div>
</section>

</main>

@endsection
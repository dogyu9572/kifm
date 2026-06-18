@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')

<main class="sub_area">

<section class="scon" aria-labelledby="fm_tree-heading">
    <div class="inner">
        <h1 class="sub_title" id="fm_tree-heading">{{ $sName }}</h1>
    </div>

	<div class="fm_tree_area">
		<div class="inner">
			<h2 class="gtit"><strong class="c_iden">나무</strong>로 이해하는 우리 몸</h2>
			<p class="tb">기능의학에서는 우리 몸을 하나의 나무에 비유합니다.<br>나뭇잎과 가지에 나타나는 문제(증상과 질병)만 바라보는 것이 아니라, 그 뿌리와 토양, 즉 우리의 생활 습관과 환경, 유전적 특성까지 함께 살펴봅니다.</p>
			<div class="gbox">
				<div class="tree_area" aria-hidden="true"><img src="/images/img_tree.svg" alt=""></div>
				<ul class="list">
					<li>
						<div class="icon" aria-hidden="true"><img src="/images/icon_tree01.svg" alt=""></div>
						<div class="txt">
							<span>잎과 가지</span>
							<h3>증상과 질병</h3>
							<p>두통 · 피로감 · 소화불량 · 두근거림 · 불면 · 만성 염증 질환 · 대사증후군</p>
						</div>
					</li>
					<li>
						<div class="icon" aria-hidden="true"><img src="/images/icon_tree02.svg" alt=""></div>
						<div class="txt">
							<span>몸통</span>
							<h3>7가지 핵심 임상 불균형</h3>
							<p>소화·흡수 · 면역·염증 · 에너지 · 해독·배출 · 순환·운반 · 호르몬·신경 · 구조적 균형</p>
						</div>
					</li>
					<li>
						<div class="icon" aria-hidden="true"><img src="/images/icon_tree03.svg" alt=""></div>
						<div class="txt">
							<span>뿌리</span>
							<h3>유전·경험·환경적 취약점과 악화 계기</h3>
							<p>유전적 소인 · 악화 계기(triggers) · 정신·감정적 영향</p>
						</div>
					</li>
					<li>
						<div class="icon" aria-hidden="true"><img src="/images/icon_tree04.svg" alt=""></div>
						<div class="txt">
							<span>토양</span>
							<h3>개인 생활습관과 환경</h3>
							<p>식사 · 영양 · 수면·이완 · 운동·활동 · 스트레스 · 대인관계 · 환경·독소 노출</p>
						</div>
					</li>
				</ul>
				<div class="btm">
					<p>이러한 핵심 임상적 불균형들이 해결되지 못하고 진행되면 결국 잎과 가지, 열매에 해당하는 개별 기관의 증상과 질병으로 나타나게 됩니다.</p>
					<div class="end_txt">그래서 기능의학은 <strong class="c_iden">잎(증상)</strong>을 치료하면서 동시에 <strong class="c_iden">뿌리와 토양(원인)</strong>을 함께 돌봅니다.</div>
				</div>
				
			</div>
		</div>
	</div>
		
</section>

</main>

@endsection
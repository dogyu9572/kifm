@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area pb0">

<section class="scon invitation_wrap" aria-labelledby="invitation-title">
	<div class="inner">
		<h1 class="sub_title" id="invitation-title">{{ $sName }}</h1>
		
		<div class="invitation_area">
			<div class="txt">
				<h2>정밀 의료의 완성, <br/><strong class="c_iden">기능의학으로 여는 건강한 미래</strong></h2>
				<div class="con">
					<p>안녕하십니까, 대한기능의학회 회원 여러분 그리고 현장의 의료진 여러분.<br/>
						유난히 뜨거웠던 여름을 지나 결실의 계절인 가을, 우리 학회는 '질병 너머의 건강(Health Beyond Disease)'이라는 주제로 2026년 추계학술대회를 개최하고자 합니다.
					</p>
					<p>최근 고령화 사회와 만성질환의 급증 속에서, 증상 완화 중심의 현대 의학을 보완하여 질병의 근본 원인을 탐구하는 기능의학의 역할은 그 어느 때보다 중요해졌습니다.<br/>
						이번 학술대회에서는 특히 'AI 기반 정밀 영양학'과 '장내 미생물 생태계의 임상적 적용'을 중심으로 국내외 최고 석학분들을 모시고 심도 있는 논의를 진행할 예정입니다.
					</p>
					<p>바쁘신 진료 일정 중에도 부디 참석하시어, 최신 지견을 나누고 동료 의료진들과의 따뜻한 네트워크를 형성하는 소중한 시간이 되시길 진심으로 기원합니다.</p>
					<p>감사합니다.</p>
					<div class="name">
						<img src="/images/img_name_kim_beom_taek.svg" title="Kim Beom-taek" alt="Kim Beom-taek">
						<p>대한기능의학회 학회장</p>
					</div>
				</div>
			</div>
			<div class="img" aria-hidden="true"><img src="/images/img_invitation.png" alt=""></div>
		</div>
	</div>
	<div class="marquee_container">
		<div class="marquee_wrapper">
			<div class="marquee_group">
				<div class="marquee_content">The 2026 KIFM Annual Fall Conference</div>
			</div>
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $container = $('.marquee_container');
    const $wrapper = $('.marquee_wrapper');
    const $group = $('.marquee_group');
    
    while ($group.width() < $(window).width()) {
        $group.append($group.children().first().clone());
    }

    $wrapper.append($group.clone());

    const speed = 10; 
    let currentX = 0;

    function scrollMarquee() {
        const groupWidth = $group.outerWidth();
        const remainingDistance = groupWidth + currentX;
        const duration = remainingDistance * speed;

        $({ moveX: currentX }).animate({ moveX: -groupWidth }, {
            duration: duration,
            easing: 'linear',
            step: function(now) {
                currentX = now;
                $wrapper.css('transform', 'translateX(' + now + 'px)');
            },
            complete: function() {
                currentX = 0;
                scrollMarquee();
            }
        });
    }

    scrollMarquee();

    $container.on('mouseenter', function() {
        $({ moveX: currentX }).stop();
        $wrapper.stop();
    }).on('mouseleave', function() {
        scrollMarquee();
    });
});
</script>
@endpush
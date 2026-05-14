@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="program-heading">
	<div class="inner">
		<h1 class="sub_title" id="program-heading">{{ $sName }}</h1>
		
		<div class="program_area">
			<div class="tabs full_line">
				<li><a href="#session1">Day 1</a></li>
				<li><a href="#session2">Day 2</a></li>
				<li><a href="#session3">Day 3</a></li>
			</div>
			
			<div class="session_wrap">
				<section class="session_area" id="session1">
					<div class="session">session 1</div>
					<h2>개원가에서 활용 가능한 줄기세포 기반 치료의 현주소</h2>
					<div class="chair">Chair : 김범택 (ㅇㅇㅇ병원), 최낙원 (ㅇㅇㅇ 병원)</div>
					<ul class="session_list">
						<li><div class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></div>
							<strong>개원가에서 활용 가능한 줄기세포 기반 치료의 현주소</strong>
							<div class="presentation">이재철 (Van.h clinic)</div>
						</li>
						<li><div class="time"><time datetime="09:30">09:30</time> ~ <time datetime="10:00">10:00</time></div>
							<strong>첨단재생임상연구 자가지방기질혈관 분획물 임상연구 경험과 전망</strong>
							<div class="presentation">박정원 (신소애여성병원)</div>
						</li>
						<li><div class="time"><time datetime="10:00">10:00</time> ~ <time datetime="10:30">10:30</time></div>
							<strong>첨단재생바이오법 개정에 따른 의료기관과 산업계 협력 방안</strong>
							<div class="presentation">정미현 (첨단재생의료산업협회)</div>
						</li>
						<li><div class="time"><time datetime="10:30">10:30</time> ~ <time datetime="10:45">10:45</time></div>
							<strong>Coffee Break</strong>
						</li>
					</ul>
				</section>
				
				<section class="session_area" id="session2">
					<div class="session">session 2</div>
					<h2>기능의학 클리닉에서의 줄기세포 치료</h2>
					<div class="chair">Chair : 김범택 (ㅇㅇㅇ병원), 최낙원 (ㅇㅇㅇ 병원)</div>
					<ul class="session_list">
						<li><div class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></div>
							<strong>개원가에서 활용 가능한 줄기세포 기반 치료의 현주소</strong>
							<div class="presentation">이재철 (Van.h clinic)</div>
						</li>
						<li><div class="time"><time datetime="09:30">09:30</time> ~ <time datetime="10:00">10:00</time></div>
							<strong>첨단재생임상연구 자가지방기질혈관 분획물 임상연구 경험과 전망</strong>
							<div class="presentation">박정원 (신소애여성병원)</div>
						</li>
						<li><div class="time"><time datetime="10:00">10:00</time> ~ <time datetime="10:30">10:30</time></div>
							<strong>첨단재생바이오법 개정에 따른 의료기관과 산업계 협력 방안</strong>
							<div class="presentation">정미현 (첨단재생의료산업협회)</div>
						</li>
						<li><div class="time"><time datetime="10:30">10:30</time> ~ <time datetime="10:45">10:45</time></div>
							<strong>Coffee Break</strong>
						</li>
					</ul>
				</section>
				
				<section class="session_area" id="session3">
					<div class="session">session 3</div>
					<h2>개원가에서 활용 가능한 줄기세포 기반 치료의 현주소</h2>
					<div class="chair">Chair : 김범택 (ㅇㅇㅇ병원), 최낙원 (ㅇㅇㅇ 병원)</div>
					<ul class="session_list">
						<li><div class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></div>
							<strong>개원가에서 활용 가능한 줄기세포 기반 치료의 현주소</strong>
							<div class="presentation">이재철 (Van.h clinic)</div>
						</li>
						<li><div class="time"><time datetime="09:30">09:30</time> ~ <time datetime="10:00">10:00</time></div>
							<strong>첨단재생임상연구 자가지방기질혈관 분획물 임상연구 경험과 전망</strong>
							<div class="presentation">박정원 (신소애여성병원)</div>
						</li>
						<li><div class="time"><time datetime="10:00">10:00</time> ~ <time datetime="10:30">10:30</time></div>
							<strong>첨단재생바이오법 개정에 따른 의료기관과 산업계 협력 방안</strong>
							<div class="presentation">정미현 (첨단재생의료산업협회)</div>
						</li>
						<li><div class="time"><time datetime="10:30">10:30</time> ~ <time datetime="10:45">10:45</time></div>
							<strong>Coffee Break</strong>
						</li>
					</ul>
				</section>
			</div>
		</div>
		
		<div class="btns_btm">
			<a href="/academic_conference/registration/reg" class="btn btn_wbb">사전등록 바로가기</a>
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $window = $(window);
    const $header = $('header');
    const $programArea = $('.program_area');
    const $tabsArea = $('.tabs');
    const $tabs = $('.tabs li');
    const $sessions = $('.session');
    const initialProgramTop = $programArea.offset().top;
    $tabs.find('a').on('click', function(e) {
        e.preventDefault();
        const targetId = $(this).attr('href');
        const $targetSection = $(targetId);
        if ($targetSection.length) {
            const headerHeight = $header.outerHeight();
            const tabsHeight = $tabsArea.outerHeight();
            const scrollTarget = $targetSection.offset().top - headerHeight - tabsHeight - 10;
            $('html, body').stop().animate({
                scrollTop: scrollTarget
            }, 500);
        }
    });
    $window.on('scroll', function() {
        const scrollTop = $window.scrollTop();
        const headerHeight = $header.outerHeight();
        const tabsHeight = $tabsArea.outerHeight();
        if (scrollTop + headerHeight >= initialProgramTop) {
            $programArea.addClass('fixed');
            $programArea.css('top', headerHeight + 'px'); 
        } else {
            $programArea.removeClass('fixed');
            $programArea.css('top', ''); 
        }
        let currentIdx = -1;
        const triggerPoint = headerHeight + tabsHeight + 20;
        $sessions.each(function(index) {
            const sectionTop = $(this).offset().top;
            if (scrollTop >= sectionTop - triggerPoint) {
                currentIdx = index;
            }
        });
        if (currentIdx !== -1) {
            $tabs.removeClass('on').eq(currentIdx).addClass('on');
        } else {
            $tabs.removeClass('on');
        }
    });
});
</script>
@endpush
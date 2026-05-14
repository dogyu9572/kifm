@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon core_values_wrap" aria-labelledby="core-values-heading">
	<div class="inner">
		<h1 class="sub_title" id="core-values-heading">{{ $sName }}</h1>
		
		<div class="history_top">
			<div class="history_title">증상을 넘어 <strong class="c_iden">원인</strong>으로, 질병을 넘어 <strong class="c_iden">사람</strong>으로</div>
			<p>대한기능의학회가 현대 의학의 한계를 넘는 새로운 의료의 패러다임을 제시합니다.</p>
		</div>
	</div>
	<div class="history_img" aria-hidden="true"></div>
	<div class="inner">
		<div class="history_body">
			<h2 class="sound_only">학회 연혁 목록</h2>
			<ul class="history_tabs">
				<li><a href="#history1">2023 ~ 현재</a></li>
				<li><a href="#history2">2019 ~ 2022</a></li>
				<li><a href="#history3">2015 ~ 2018</a></li>
				<li><a href="#history4">2013 ~ 2014</a></li>
			</ul>
			
			<article id="history1">
				<h3>2023 ~ 현재</h3>
				<dl>
					<div><dt>2025. 02</dt><dd>'한국형 기능의학 표준 모델' 제도권 안착 추진 중</dd></div>
					<div><dt>2024. 01</dt><dd>AI 기반 정밀 영양 및 기능의학 진단 보조 도구 연구 착수</dd></div>
					<div><dt>2023. 04</dt><dd>학회 창립 10주년 기념 국제 컨퍼런스(ICFM) 개최 (해외 연자 초청 및 아시아 허브 도약)</dd></div>
				</dl>
			</article>
			
			<article id="history2">
				<h3>2019 ~ 2022</h3>
				<dl>
					<div><dt>2025. 02</dt><dd>'한국형 기능의학 표준 모델' 제도권 안착 추진 중</dd></div>
					<div><dt>2024. 01</dt><dd>AI 기반 정밀 영양 및 기능의학 진단 보조 도구 연구 착수</dd></div>
					<div><dt>2023. 04</dt><dd>학회 창립 10주년 기념 국제 컨퍼런스(ICFM) 개최 (해외 연자 초청 및 아시아 허브 도약)</dd></div>
				</dl>
			</article>
			
			<article id="history3">
				<h3>2015 ~ 2018</h3>
				<dl>
					<div><dt>2025. 02</dt><dd>'한국형 기능의학 표준 모델' 제도권 안착 추진 중</dd></div>
					<div><dt>2024. 01</dt><dd>AI 기반 정밀 영양 및 기능의학 진단 보조 도구 연구 착수</dd></div>
					<div><dt>2023. 04</dt><dd>학회 창립 10주년 기념 국제 컨퍼런스(ICFM) 개최 (해외 연자 초청 및 아시아 허브 도약)</dd></div>
				</dl>
			</article>
			
			<article id="history4">
				<h3>2015 ~ 2018</h3>
				<dl>
					<div><dt>2025. 02</dt><dd>'한국형 기능의학 표준 모델' 제도권 안착 추진 중</dd></div>
					<div><dt>2024. 01</dt><dd>AI 기반 정밀 영양 및 기능의학 진단 보조 도구 연구 착수</dd></div>
					<div><dt>2023. 04</dt><dd>학회 창립 10주년 기념 국제 컨퍼런스(ICFM) 개최 (해외 연자 초청 및 아시아 허브 도약)</dd></div>
					<div><dt>2023. 04</dt><dd>학회 창립 10주년 기념 국제 컨퍼런스(ICFM) 개최 (해외 연자 초청 및 아시아 허브 도약)</dd></div>
					<div><dt>2023. 04</dt><dd>학회 창립 10주년 기념 국제 컨퍼런스(ICFM) 개최 (해외 연자 초청 및 아시아 허브 도약)</dd></div>
				</dl>
			</article>
			
			<!-- 데이터 삽입시 최 하단의 div가 최소 5개가 되는 것을 추천 -->
		</div>
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $header = $('.header');
    const $historyBody = $('.history_body');
    const $tabs = $('.history_tabs');
    const $tabItems = $('.history_tabs li');
    const $tabLinks = $('.history_tabs a');
    const $articles = $('article[id^="history"]');

    $(window).on('scroll', function() {
        let scrollTop = $(window).scrollTop();
        let headerH = $header.outerHeight();
        let tabsH = $tabs.outerHeight();
        let triggerPoint = headerH + tabsH + 50;
        if (scrollTop >= $historyBody.offset().top - headerH) {
            $historyBody.addClass('fixed');
        } else {
            $historyBody.removeClass('fixed');
        }
        $articles.each(function(index) {
            let targetPos = $(this).offset().top - triggerPoint;
            let nextTargetPos = $articles.eq(index + 1).length 
                                ? $articles.eq(index + 1).offset().top - triggerPoint 
                                : $(document).height();
            if (scrollTop >= targetPos && scrollTop < nextTargetPos) {
                $articles.removeClass('on');
                $(this).addClass('on');

                $tabItems.removeClass('on');
                $tabItems.eq(index).addClass('on');
            }
        });
        if (scrollTop < $articles.first().offset().top - triggerPoint) {
            $articles.removeClass('on');
            $tabItems.removeClass('on');
        }
    });
    $tabLinks.on('click', function(e) {
        e.preventDefault();
        const targetId = $(this).attr('href');
        const $targetElement = $(targetId);
        if ($targetElement.length) {
            let headerH = $header.outerHeight();
            let tabsH = $tabs.outerHeight();
            let movePos = $targetElement.offset().top - (headerH + tabsH);
            $('html, body').stop().animate({
                scrollTop: movePos
            }, 500);
            if (history.pushState) {
                history.pushState(null, null, window.location.pathname);
            }
        }
    });
});
</script>
@endpush
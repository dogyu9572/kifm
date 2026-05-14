@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@section('content')
<main class="main_wrap">

<section class="mvisual" aria-labelledby="visual-title">
	<div class="inner">
		<span class="top">KIFM 2026 The 2026 Korean Institute for Functional Medicine</span>
		<h1 id="visual-title"><strong>KIFM 2026년</strong> 대한기능의학회 추계학술대회</h1>
		<span class="color">Obesity Care in Functional Medicnine </span>
		<div class="d_day"><time datetime="2026-11-16"><strong>2026</strong>년 <strong>11</strong>월 <strong>16</strong>일 (일)</time><p>고려대학교 의과대학 본관 2층 유광사홀</p></div>
		<dl class="dead_line">
			<div>
				<dt>사전등록 마감일</dt>
				<dd>
					<time class="date" datetime="2026-12-01">2026.12.01 (월) 까지</time>
					<div class="ani_date" aria-label="남은 기간 7일"><strong>7</strong>DAYS</div>
				</dd>
			</div>
			<div>
				<dt>초록 접수 마감</dt>
				<dd>
					<time class="date" datetime="2026-12-01">2026.12.01 (월) 까지</time>
					<div class="ani_date" aria-label="남은 기간 7일"><strong>7</strong>DAYS</div>
				</dd>
			</div>
		</dl>
	</div>
</section>

<section class="main_links" aria-label="학술대회 주요 서비스">
	<div class="inner">
		<ul class="link_list">
			<li><a href="{{ route('academic_conference.reg_check_member') }}" class="i1">사전등록확인</a></li>
			<li><a href="{{ route('academic_conference.abstract_check') }}" class="i2">초록 접수 확인</a></li>
			<li><a href="{{ route('academic_conference.program') }}" class="i3">프로그램 안내</a></li>
			<li><a href="{{ route('mypage.participation_history') }}" class="i4">참가내역서/영수증 조회</a></li>
		</ul>
	</div>
</section>

<section class="mcon mc01" aria-labelledby="speakers-title">
	<div class="inner">
		<div class="mtit"><h2 id="speakers-title">Plenary & Keynote Speakers</h2>
			<div class="navi">
				<button type="button" class="arrow prev" aria-label="이전 연자 보기">이전</button>
				<button type="button" class="arrow next" aria-label="다음 연자 보기">다음</button>
			</div>
		</div>
		<div class="mc01_slide">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<div class="imgfit"><img src="/images/img_sample_academic_mc01.jpg" alt=""></div>
					<div class="txt">
						<div class="name">홍길동 교수</div>
						<p class="belong">대한대학교 의과대학</p>
						<p class="c_iden">Plenary Session: Obesity & Metabolic Care</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="imgfit"><img src="/images/img_sample_academic_mc01.jpg" alt=""></div>
					<div class="txt">
						<div class="name">홍길동 교수</div>
						<p class="belong">대한대학교 의과대학</p>
						<p class="c_iden">Plenary Session: Obesity & Metabolic Care</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="imgfit"><img src="/images/img_sample_academic_mc01.jpg" alt=""></div>
					<div class="txt">
						<div class="name">홍길동 교수</div>
						<p class="belong">대한대학교 의과대학</p>
						<p class="c_iden">Plenary Session: Obesity & Metabolic Care</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="imgfit"><img src="/images/img_sample_academic_mc01.jpg" alt=""></div>
					<div class="txt">
						<div class="name">홍길동 교수</div>
						<p class="belong">대한대학교 의과대학</p>
						<p class="c_iden">Plenary Session: Obesity & Metabolic Care</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="imgfit"><img src="/images/img_sample_academic_mc01.jpg" alt=""></div>
					<div class="txt">
						<div class="name">홍길동 교수</div>
						<p class="belong">대한대학교 의과대학</p>
						<p class="c_iden">Plenary Session: Obesity & Metabolic Care</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="imgfit"><img src="/images/img_sample_academic_mc01.jpg" alt=""></div>
					<div class="txt">
						<div class="name">홍길동 교수</div>
						<p class="belong">대한대학교 의과대학</p>
						<p class="c_iden">Plenary Session: Obesity & Metabolic Care</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="mcon mc02" aria-labelledby="notice-title">
	<div class="inner">
		<h2 class="mtit" id="notice-title">Notice</h2>
		<ul class="list">
			<li><a href="#this"><span class="subject">2025년 7월 20일 - 하계연수강좌</span><time class="date" datetime="2025-07-20">2025.07.20</time></a></li>
			<li><a href="#this"><span class="subject">2025년 7월 20일 - 하계연수강좌</span><time class="date" datetime="2025-07-20">2025.07.20</time></a></li>
			<li><a href="#this"><span class="subject">2025년 7월 20일 - 하계연수강좌</span><time class="date" datetime="2025-07-20">2025.07.20</time></a></li>
		</ul>
	</div>
</section>

<section class="mcon mc03"aria-labelledby="sponsors-title">
	<div class="inner">
		<h2 class="mtit" id="sponsors-title">Sponsors</h2>
		<section class="slide_area">
			<div class="tit">
				<h3>Supported by</h3>
				<div class="navi">
					<button type="button" class="arrow prev" aria-label="이전 후원사">이전</button>
					<button type="button" class="arrow next" aria-label="다음 후원사">다음</button>
					<button type="button" class="papl pause on" aria-label="슬라이드 정지">정지</button>
					<button type="button" class="papl play" aria-label="슬라이드 재생">재생</button>
				</div>
			</div>
			<div class="sponsors_slide">
				<div class="swiper-wrapper">
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
				</div>
			</div>
		</section>
		<section class="slide_area">
			<div class="tit">
				<h3>Supported by</h3>
				<div class="navi">
					<button type="button" class="arrow prev" aria-label="이전 후원사">이전</button>
					<button type="button" class="arrow next" aria-label="다음 후원사">다음</button>
					<button type="button" class="papl pause on" aria-label="슬라이드 정지">정지</button>
					<button type="button" class="papl play" aria-label="슬라이드 재생">재생</button>
				</div>
			</div>
			<div class="sponsors_slide">
				<div class="swiper-wrapper">
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="#this"><img src="/images/img_sample_sponsors.jpg" alt=""></a></div>
				</div>
			</div>
		</section>
	</div>
</section>

<section class="mcon mc04" aria-labelledby="location-title">
	<div class="inner">
		<h2 class="sound_only" id="location-title">오시는길, 초록집 다운로드</h2>
		<ul class="flex">
			<li class="i1"><a href="/academic_conference/venue"><span>KIFM 2026년 대한기능의학회 추계학술대회</span><strong>오시는 길</strong></a></li>
			<li class="i2"><a href="/academic_conference/abstract"><span>KIFM 2026년 대한기능의학회 추계학술대회</span><strong>초록집 다운로드</strong></a></li>
		</ul>
	</div>
</section>

</main>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Plenary & Keynote Speakers 슬라이드 (mc01)
    const mc01Swiper = new Swiper('.mc01_slide', {
        slidesPerView: 1.2, // 모바일에서 다음 슬라이드 살짝 보이게
        spaceBetween: 12,
        loop: true,
        centeredSlides: false,
        breakpoints: {
            // 태블릿 이상
            768: {
                slidesPerView: 2.5,
                spaceBetween: 24,
            },
            // PC 이상
            1024: {
                slidesPerView: 4,
                spaceBetween: 32,
            }
        },
        navigation: {
            nextEl: '.mc01 .arrow.next',
            prevEl: '.mc01 .arrow.prev',
        },
    });

    // 2. Sponsors 슬라이드 (mc03)
    $('.sponsors_slide').each(function(index) {
        const $this = $(this);
        const $parent = $this.closest('.slide_area');
        
        const sponsorSwiper = new Swiper(this, {
            slidesPerView: 2,
            spaceBetween: 10,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                768: {
                    slidesPerView: 4,
                    spaceBetween: 18,
                },
                1200: {
                    slidesPerView: 5,
                    spaceBetween: 24,
                }
            },
            navigation: {
                nextEl: $parent.find('.arrow.next')[0],
                prevEl: $parent.find('.arrow.prev')[0],
            },
        });

        // 후원사 슬라이드 재생/정지 버튼 제어
        $parent.find('.papl.pause').on('click', function() {
            sponsorSwiper.autoplay.stop();
            $(this).hide();
            $parent.find('.papl.play').show().focus();
        });

        $parent.find('.papl.play').on('click', function() {
            sponsorSwiper.autoplay.start();
            $(this).hide();
            $parent.find('.papl.pause').show().focus();
        });
        $parent.find('.papl.play').hide();
    });
});
</script>
@endpush
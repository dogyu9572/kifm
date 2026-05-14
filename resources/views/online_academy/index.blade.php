@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon online_academy_head" aria-labelledby="online-academy-top-heading">
	<div class="inner">
		<h1 class="sound_only" id="online-academy-top-heading">대한기능의학회 온라인 아카데미 목록</h1>
		
		<div class="online_academy_slide_wrap">
			<div class="slide_txt">
				<div class="swiper-wrapper">
					<div class="swiper-slide">
						<a href="/online_academy/view">
							<span class="course">COURSE</span>
							<h2 class="tit">2026년 심혈관 질환 마스터 클래스 <br/>(기초 과정)</h2>
							<p>본 강의는 심혈관 질환 마스터 클래스에 대한 기초 강의입니다.<br/>본 강의는 심혈관 질환 마스터 클래스에 대한 기초 강의입니다.</p>
						</a>
					</div>
					<div class="swiper-slide">
						<a href="/online_academy/view">
							<span class="course">COURSE</span>
							<h2 class="tit">2026년 심혈관 질환 마스터 클래스 <br/>(기초 과정)</h2>
							<p>본 강의는 심혈관 질환 마스터 클래스에 대한 기초 강의입니다.<br/>본 강의는 심혈관 질환 마스터 클래스에 대한 기초 강의입니다.</p>
						</a>
					</div>
				</div>
			</div>
			<div class="slide_img">
				<div class="swiper-wrapper">
					<div class="swiper-slide"><a href="/online_academy/view"><img src="/images/img_sample_online_academy_head.jpg" alt=""></a></div>
					<div class="swiper-slide"><a href="/online_academy/view"><img src="/images/img_sample_online_academy_head.jpg" alt=""></a></div>
				</div>
			</div>
			<div class="navi">
				<button class="arrow prev" aria-label="이전 슬라이드">이전</button>
				<button class="arrow next" aria-label="다음 슬라이드">다음</button>
				<div class="line" aira-hidden="true"><span></span></div>
			</div>
		</div>
		
	</div>
</section>

<section class="scon online_academy_list" aria-labelledby="online-academy-list-heading">
	<div class="inner">
		<h2 class="sub_title no_ico" id="online-academy-list-heading">NEW Releases</h2>
		<ul class="tabs full_line mb48">
			<li class="on"><a href="#this">필수 과정</a></li>
			<li><a href="#this">학술대회 연계 과정</a></li>
			<li><a href="#this">연수강좌 연계 과정</a></li>
			<li><a href="#this">수시 과정</a></li>
			<li><a href="#this">온라인 심화과정</a></li>
		</ul>
		
		<div class="board_top board_top_academy">
			<div class="left">
				<select name="" id="" class="text">
					<option value="">개설 연도</option>
				</select>
				<div class="select_custom">
					<button type="" class="select_type">
						<span>강의 키워드</span>
						<ul class="user_select"></ul>
					</button>
					<div class="select_check">
						<div class="select_list">
							<button type="button">전체</button>
							<button type="button">심혈관 질환</button>
							<button type="button">영양학</button>
							<button type="button">면역 시스템</button>
							<button type="button">기초의학</button>
							<button type="button">기초의학</button>
							<button type="button">기초의학</button>
							<button type="button">기초의학</button>
							<button type="button">기초의학</button>
							<button type="button">기초의학</button>
						</div>
						<div class="btns_btm flex_center">
							<button type="button" class="btn btn_reset btn_kwg">초기화</button>
							<button type="button" class="btn btn_check btn_woo">적용하기</button>
						</div>
					</div>
				</div>
				<select name="" id="" class="text">
					<option value="">전체</option>
					<option value="">강의명</option>
					<option value="">강의주제</option>
					<option value="">교수명</option>
				</select>
				<input type="text" class="text" placeholder="검색어를 입력해주세요.">
				<button type="button" class="btn btn_wkk btn_search btn_small">검색</button>
				<button type="button" class="btn btn_kwg btn_reset btn_small">초기화</button>
			</div>
		</div>
		
		<ul class="gallery_list gallery_academy">
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
			<li>
				<a href="/online_academy/view">
					<span class="imgfit" aira-hidden="true"><img src="/images/img_sample_online_academy_list.jpg" alt=""></span>
					<span class="txt">
						<span class="type">학술대회<span class="time">90일 수강</span></span>
						<h3>2026년 심혈관 질환 마스터 클래스 (기초 과정)</h3>
						<p class="name">홍길동 교수 (대한대학교 의과대학)</p>
					</span>
				</a>
			</li>
		</ul>

		<nav class="board-pagination" aria-label="게시판 페이지 이동">
			<ul class="pagination">
				<li class="page-item arw_item"><a class="page-link" href="#" title="첫 페이지" aria-label="첫 페이지로 이동"><i class="arrow two first" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="이전 페이지" aria-label="이전 페이지로 이동"><i class="arrow one prev" aria-hidden="true"></i></a></li>
				<li class="page-item active"><span class="page-link" aria-current="page" aria-label="현재 페이지 1">1</span></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="2페이지로 이동">2</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="3페이지로 이동">3</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="4페이지로 이동">4</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="5페이지로 이동">5</a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="다음 페이지" aria-label="다음 페이지로 이동"><i class="arrow one next" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="마지막 페이지" aria-label="마지막 페이지로 이동"><i class="arrow two last" aria-hidden="true"></i></a></li>
			</ul>
		</nav>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
// 슬라이드
    const academyImgSlide = new Swiper('.slide_img', {
        loop: true,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        speed: 800,
    });

    const academyTxtSlide = new Swiper('.slide_txt', {
        loop: true,
        speed: 800,
        autoplay: {
            delay: 5000, // 5초 대기
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.online_academy_head .next',
            prevEl: '.online_academy_head .prev',
        },
        on: {
            // 슬라이드 변경 시작 시 프로그레스 바 초기화
            slideChangeTransitionStart: function() {
                $('.online_academy_head .line span').stop().css('width', '0');
            },
            // 슬라이드 변경 완료 후 프로그레스 바 애니메이션 실행
            slideChangeTransitionEnd: function() {
                $('.online_academy_head .line span').animate({
                    width: '100%'
                }, 5000, 'linear'); // autoplay delay와 동일한 시간
            },
            // 초기 로드 시 실행
            init: function() {
                $('.online_academy_head .line span').animate({
                    width: '100%'
                }, 5000, 'linear');
            }
        }
    });

    academyTxtSlide.controller.control = academyImgSlide;
// 탭 클릭, 페이지 이동시
	$('.tabs.full_line a').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const $target = $('.online_academy_list');
        const headerHeight = $('.header').outerHeight();
        $this.parent('li').addClass('on').siblings().removeClass('on');
        if ($target.length > 0) {
            const targetPos = $target.offset().top - headerHeight;
            $('html, body').stop().animate({
                scrollTop: targetPos
            }, 500);
        }
    });
// 셀렉트 커스텀
    const MAX_SELECT = 5;

    $('.select_type').on('click', function() {
        $('.select_check').stop().slideToggle(300);
    });

    $('.select_list button').on('click', function() {
        const $this = $(this);
        const isTotal = $this.text() === '전체';
        const isOn = $this.hasClass('on');

        if (isTotal) {
            $this.siblings().removeClass('on');
            $this.toggleClass('on');
        } else {
            $('.select_list button').filter(function() {
                return $(this).text() === '전체';
            }).removeClass('on');
            const checkedCount = $('.select_list button.on').length;
            if (!isOn && checkedCount >= MAX_SELECT) {
                alert(`최대 ${MAX_SELECT}개까지 선택 가능합니다.`);
                return;
            }
            $this.toggleClass('on');
        }
    });

    $('.select_check .btn_reset').on('click', function() {
        $('.select_list button').removeClass('on');
    });

    $('.select_check .btn_check').on('click', function() {
        const $userSelect = $('.user_select');
        const $customBox = $('.select_custom');
        $userSelect.empty();

        const $selectedButtons = $('.select_list button.on');

        if ($selectedButtons.length > 0) {
            $selectedButtons.each(function() {
                const text = $(this).text();
                $userSelect.append(`<li>${text}</li>`);
            });
            $customBox.addClass('on');
        } else {
            $customBox.removeClass('on');
        }

        $('.select_check').stop().slideUp(300);
    });

    $('.board_top_academy > .left > .btn_reset.btn_small').on('click', function() {
        const $parent = $(this).closest('.left');
        
        $parent.find('select').prop('selectedIndex', 0);
        $parent.find('input[type="text"]').val('');
        
        $parent.find('.select_list button').removeClass('on');
        $parent.find('.user_select').empty();
        $parent.find('.select_custom').removeClass('on');
        $parent.find('.select_check').hide();
    });
});
</script>
@endpush
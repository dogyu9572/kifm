@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@section('content')
<main class="main_wrap">
<h1 class="sound_only">대한기능의학회 메인</h1>

<!-- main_visual -->
<section class="main_visual_wrap" aria-labelledby="visual-title">
	<h2 class="sound_only" id="visual-title">메인 비주얼</h2>
	<div class="inner flex">
		<div class="mvisual">
			<div class="swiper mvisual-swiper">
				<div class="swiper-wrapper">
					<div class="swiper-slide"><img src="/images/img_main_visual_01.jpg" alt="메인 비주얼 1">
						<div class="txt">
							<p>미래의학의 새로운 패러다임인 <strong>기능의학 학회에<br/>여러분을 초대합니다.</strong></p>
						</div>
					</div>
					<div class="swiper-slide"><img src="/images/img_main_visual_01.jpg" alt="메인 비주얼 1">
						<div class="txt">
							<p>미래의학의 새로운 패러다임인 <strong>기능의학 학회에<br/>여러분을 초대합니다.</strong></p>
						</div>
					</div>
				</div>
				<div class="mvisual-control">
					<div class="paging"></div>
					<button type="button" class="arrow prev" aria-label="이전 슬라이드"></button>
					<button type="button" class="arrow next" aria-label="다음 슬라이드"></button>
					<button type="button" class="papl" aria-label="슬라이드 정지" aria-pressed="false"></button>
				</div>
			</div>
		</div>
		<div class="right">
			<!-- 로그인 전 -->
			<div class="log_area before">
				<h2><strong>로그인 후</strong> 맞춤정보를 확인해보세요</h2>
				<div class="inputs">
					<input type="text" class="text w100p" placeholder="아이디">
					<input type="password" class="text w100p" placeholder="비밀번호">
					<button type="submit" class="btn">로그인</button>
					<ul class="mem_links">
						<li><a href="/auth/">아이디 찾기</a></li>
						<li><a href="/auth/">비밀번호 찾기</a></li>
						<li><a href="/auth/register">회원가입</a></li>
					</ul>
				</div>
			</div>
			<!-- 로그인 후-->
			<!-- <div class="log_area after">
				<ul class="member_type">
					<li class="t1">정회원</li>
					<li class="t2">고문</li>
					<li class="t3">임원</li>
				</ul>
				<div class="name">
					<h2>안녕하세요, 홍길동 선생님!</h2>
					<a href="#this" class="more"></a>
				</div>
				<div class="member_info">
					<div class="tit">
						<strong>인증의 자격 정보</strong>
						<div class="date">2026.03.01 ~ 2027.03.01</div>
					</div>
					<div class="state_line"><div class="bar"></div></div>
					<div class="info">
						<div class="l">참석 현황 <strong class="c_iden">1</strong>/3회</div>
						<div class="r"><p class="excl">2회 부족</p></div>
					</div>
				</div>
				<div class="btns">
					<a href="/mypage/profile_edit" class="btn btn_wbb">마이페이지</a>
					<a href="/mypage/online_training" class="btn btn_wkk">강의실 입장</a>
				</div>
			</div> -->
			<ul class="page_links">
				<li class="i1"><a href="#this">회원가입 안내</a></li>
				<li class="i2"><a href="/academic_event/conference">학술대회</a></li>
				<li class="i3"><a href="/online_academy">온라인 아카데미</a></li>
				<li class="i4"><a href="#this">대한기능의학 위원회</a></li>
			</ul>
		</div>
	</div>
</section>

<!-- 학술대회 일정 / 학회 일정 -->
<section class="mcon mc01" aria-labelledby="schedule-title">
	<h2 class="sound_only" id="schedule-title">일정</h2>
	<div class="inner">
		<div class="left">
			<div class="mtit">
				<h3>학술대회 일정</h3>
				<div class="arrows flex">
					<button type="button" class="arrow prev" aria-label="학술대회 일정 이전 슬라이드"></button>
					<button type="button" class="arrow next" aria-label="학술대회 일정 다음 슬라이드"></button>
				</div>
			</div>
			<div class="schedule">
				<ul class="month">
					<li><button type="button">Jan</button></li>
					<li><button type="button">Feb</button></li>
					<li><button type="button">Mar</button></li>
					<li><button type="button">Apr</button></li>
					<li><button type="button">May</button></li>
					<li><button type="button">Jun</button></li>
					<li><button type="button">Jul</button></li>
					<li><button type="button">Aug</button></li>
					<li><button type="button">Sep</button></li>
					<li><button type="button">Oct</button></li>
					<li><button type="button">Nov</button></li>
					<li><button type="button">Dec</button></li>
				</ul>
				<div class="schedule_slide">
					<div class="swiper-wrapper">
						<div class="swiper-slide t1">
							<a href="#this" class="card-link">
								<span class="day" aria-hidden="true"><strong>20</strong>일 (금)</span>
								<h4 class="title">심화 연수 강좌 1차</h4>
								<time datetime="2026-02-01/2026-02-07" class="full-date"><span class="sound_only">기간: </span>2026.02.01 ~ 2026.02.07</time>
								<span class="type">연수강좌</span>
							</a>
						</div>
						<div class="swiper-slide t1">
							<a href="#this" class="card-link">
								<span class="day" aria-hidden="true"><strong>21</strong>일 (금)</span>
								<h4 class="title">심화 연수 강좌 1차</h4>
								<time datetime="2026-02-01/2026-02-07" class="full-date"><span class="sound_only">기간: </span>2026.02.01 ~ 2026.02.07</time>
								<span class="type">연수강좌</span>
							</a>
						</div>
						<div class="swiper-slide t2">
							<a href="#this" class="card-link">
								<span class="day" aria-hidden="true"><strong>22</strong>일 (금)</span>
								<h4 class="title">춘계학술대회</h4>
								<time datetime="2026-02-01/2026-02-07" class="full-date"><span class="sound_only">기간: </span>2026.02.01 ~ 2026.02.07</time>
								<span class="type">학술대회</span>
							</a>
						</div>
						<div class="swiper-slide t2">
							<a href="#this" class="card-link">
								<span class="day" aria-hidden="true"><strong>22</strong>일 (금)</span>
								<h4 class="title">춘계학술대회</h4>
								<time datetime="2026-02-01/2026-02-07" class="full-date"><span class="sound_only">기간: </span>2026.02.01 ~ 2026.02.07</time>
								<span class="type">학술대회</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="right">
			<div class="mtit">
				<h3>학회 일정</h3>
			</div>
			<div class="month_area">
				<div class="select_month">
					<button class="arrow prev" aria-label="이전달"></button>
					<button class="arrow next" aria-label="다음달"></button>
					<span class="tomonth">2026.04</span>
				</div>
				<div class="month">
					<table>
						<caption>학회 일정 달력</caption>
						<thead>
							<tr>
								<th>일</th>
								<th>월</th>
								<th>화</th>
								<th>수</th>
								<th>목</th>
								<th>금</th>
								<th>토</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 공지사항 / 온라인 아카데미 / 회원자료실 -->
<section class="mcon mc02" aria-labelledby="notice-title">
	<h2 class="sound_only" id="notice-title">공지사항 및 배너 링크</h2>
	<div class="inner">
		<div class="long notice">
			<div class="mtit"><h3>공지사항</h3><a href="#this" class="more" aria-label="대한기능의학회 공지사항 으로 이동"></a></div>
			<ul class="list">
				<li class="notice"><a href="/member_plaza/society_notices/view">사단법인 대한기능의학회 창립총회 개최 공고문<span class="date">2022.07.13</span></a></li>
				<li><a href="/member_plaza/society_notices/view">2025년 7월 20일 - 하계연수강좌<span class="date">2022.07.13</span></a></li>
				<li><a href="/member_plaza/society_notices/view">2025년 4월 20일 - 춘계학술대회<span class="date">2022.07.13</span></a></li>
				<li><a href="/member_plaza/society_notices/view">2025년 2월 16일 - 심화 연수강좌 3차<span class="date">2022.07.13</span></a></li>
			</ul>
		</div>
		<div class="short academy">
			<div class="mtit"><h3>온라인 아카데미</h3></div>
			<div class="main_gall">
				<a href="#this"><span class="imgfit"><img src="/images/img_sample_mc02_01.jpg" alt=""></span><span class="txt"><p>[대한기능의학회] 대사 건강의 출발점: 기능의학으로 풀어보는 인슐린 저항성/ 분당차병원 가정의학과 김영상</p></span></a>
			</div>
		</div>
		<div class="short archives">
			<div class="mtit"><h3>회원자료실</h3></div>
			<div class="main_gall">
				<a href="#this"><span class="imgfit"><img src="/images/img_sample_mc02_02.jpg" alt=""></span><span class="txt"><p>2025 추계 학술대회 초록집 (PDF)</p></span></a>
			</div>
		</div>
	</div>
</section>
	
</main>

@endsection

@section('popups')
@if($popups->count() > 0)
    @foreach($popups as $popup)
        @if($popup->popup_display_type === 'normal')
            {{-- 일반팝업 (새창) --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const popupUrl = '{{ route("popup.show", $popup->id) }}';
                    const popupFeatures = 'width={{ $popup->width }},height={{ $popup->height }},left={{ $popup->position_left ?? 100 }},top={{ $popup->position_top ?? 100 }},scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no';
                    window.open(popupUrl, 'popup_{{ $popup->id }}', popupFeatures);
                });
            </script>
        @else
            {{-- 레이어팝업 (오버레이) --}}
            <div class="popup-layer popup-fixed" 
                 id="popup-{{ $popup->id }}"
                 data-popup-id="{{ $popup->id }}"
                 data-display-type="layer"
                 style="position: absolute !important; width: {{ $popup->width }}px; height: auto; top: {{ $popup->position_top }}px; left: {{ $popup->position_left }}px; z-index: 99999;">
                
                <div class="popup-body">
                    @if($popup->popup_type === 'image' && $popup->popup_image)
                        @if($popup->url)
                            <a href="{{ $popup->url }}" target="{{ $popup->url_target }}">
                                <img src="{{ asset('storage/' . $popup->popup_image) }}" alt="{{ $popup->title }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $popup->popup_image) }}" alt="{{ $popup->title }}">
                        @endif
                    @elseif($popup->popup_type === 'html' && $popup->popup_content)
                        {!! $popup->popup_content !!}
                    @endif
                </div>
                
                <div class="popup-footer">
                    <label class="popup-today-label" data-popup-id="{{ $popup->id }}">
                        <input type="checkbox" class="popup-today-close" data-popup-id="{{ $popup->id }}">
                        1일 동안 보지 않음
                    </label>
                    <button type="button" class="popup-footer-close-btn" data-popup-id="{{ $popup->id }}">닫기</button>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
$(document).ready(function () {
// mvisual
	const mvisualSwiper = new Swiper('.mvisual-swiper', {
        loop: true,
        speed: 800,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.mvisual-control .paging',
            type: 'fraction',
			formatFractionCurrent: function (number) { return String(number).padStart(2, '0'); },
			formatFractionTotal: function (number) { return String(number).padStart(2, '0'); },
        },
        navigation: {
            nextEl: '.mvisual-control .next',
            prevEl: '.mvisual-control .prev',
        },
    });
    $('.mvisual-control .papl').on('click', function () {
        const isPressed = $(this).attr('aria-pressed') === 'true';
        if (isPressed) {
            mvisualSwiper.autoplay.start();
            $(this).attr('aria-pressed', 'false');
            $(this).attr('aria-label', '슬라이드 정지');
            $(this).removeClass('stop');
        } else {
            mvisualSwiper.autoplay.stop();
            $(this).attr('aria-pressed', 'true');
            $(this).attr('aria-label', '슬라이드 재생');
            $(this).addClass('stop');
        }
    });
// 학술대회 일정
    const scheduleSwiper = new Swiper('.schedule_slide', {
        slice: true,
		spaceBetween: 8,
        slidesPerView: 2, 
        breakpoints: {
            768: {
                slidesPerView: 3,
				spaceBetween: 16,
            }
        },
        navigation: {
            nextEl: '.arrows .next',
            prevEl: '.arrows .prev',
        },
    });
    $('.month li button').on('click', function () {
        const $parentLi = $(this).parent('li');
        const selectedMonth = $(this).text();
        $parentLi.addClass('on').siblings().removeClass('on');
        updateSchedule(selectedMonth);
    });
    function updateSchedule(month) {
        console.log(month + " 데이터로 교체합니다.");
    }
    $('.month li').first().addClass('on');
// 학회 일정
    let date = new Date(), realToday = new Date();
	const eventDates = ['2026-04-01', '2026-04-14', '2026-05-14'];
	function renderCalendar() {
		const viewYear = date.getFullYear(), viewMonth = date.getMonth();
		$('.tomonth').text(`${viewYear}.${String(viewMonth + 1).padStart(2, '0')}`);
		const prevLast = new Date(viewYear, viewMonth, 0), thisLast = new Date(viewYear, viewMonth + 1, 0);
		const PLDate = prevLast.getDate(), PLDay = prevLast.getDay(), TLDate = thisLast.getDate(), TLDay = thisLast.getDay();
		const prevDates = [], nextDates = [];
		if (PLDay !== 6) for (let i = 0; i < PLDay + 1; i++) prevDates.unshift(PLDate - i);
		for (let i = 1; i < 7 - TLDay; i++) nextDates.push(i);
		const thisDates = [...Array(TLDate + 1).keys()].slice(1);
		const dates = prevDates.concat(thisDates, nextDates), firstDateIndex = dates.indexOf(1), lastDateIndex = dates.lastIndexOf(TLDate);
		let html = '';
		dates.forEach((d, i) => {
			const condition = i >= firstDateIndex && i <= lastDateIndex ? 'this' : 'disabled';
			const dateStr = `${viewYear}-${String(viewMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
			const isToday = viewYear === realToday.getFullYear() && viewMonth === realToday.getMonth() && d === realToday.getDate() && condition === 'this';
			const hasEvent = condition === 'this' && eventDates.includes(dateStr) ? 'event' : '';
			if (i % 7 === 0) html += '<tr>';
			html += `<td class="${condition} ${isToday ? 'today' : ''} ${hasEvent}"><button type="button"><span>${d}</span></button></td>`;
			if (i % 7 === 6) html += '</tr>';
		});
		$('.month tbody').html(html);
	}
	renderCalendar();
	$('.select_month .prev').on('click', () => { date.setMonth(date.getMonth() - 1); renderCalendar(); });
	$('.select_month .next').on('click', () => { date.setMonth(date.getMonth() + 1); renderCalendar(); });
	$('.month tbody').on('click', 'button', function () {
		const targetTd = $(this).parent('td');
		if (targetTd.hasClass('disabled')) return;
		$('.month tbody td').removeClass('click');
		targetTd.addClass('click');
	});
	$('.month li').first().addClass('on');
// 로그인 후 인증의 참석 현황 게이지바
	$('.member_info').each(function() {
        const text = $(this).find('.info .l').text();
        const matches = text.match(/\d+/g);

        if (matches && matches.length >= 2) {
            const current = parseInt(matches[0]);
            const total = parseInt(matches[1]);
            const percentage = (current / total) * 100;

            $(this).find('.state_line .bar').css('width', percentage + '%');
        }
    });
});
</script>

@endpush
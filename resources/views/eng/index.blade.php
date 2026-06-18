@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@section('content')
<main class="main_wrap">
<h1 class="sound_only">대한기능의학회 일반인 메인</h1>

<!-- main_visual -->
<section class="main_visual_wrap" aria-labelledby="visual-title">
	<h2 class="sound_only" id="visual-title">메인 비주얼</h2>
	<div class="inner flex">
		<div class="mvisual w100p">
			<div class="swiper mvisual-swiper">
				<div class="swiper-wrapper">
					<!-- 일반 메인 기본 비주얼 노출 -->
					<div class="swiper-slide">
						<img src="/images/img_main_visual_01.jpg" alt="메인 비주얼">
						<div class="txt">
							<p>미래의학의 새로운 패러다임인 <strong>기능의학 학회에<br>여러분을 초대합니다.</strong></p>
						</div>
					</div>
				</div>
				<div class="mvisual-control">
					<div class="paging"></div>
					<button type="button" class="arrow prev" aria-label="이전 슬라이드"></button>
					<button type="button" class="arrow next" aria-label="다음 슬라이드"></button>
					<button type="button" class="papl" aria-label="슬라이드 정지" ariapressed="false"></button>
				</div>
			</div>
		</div>
	</div>
	<div class="inner">
		<div class="book_area">
			<h3 class="book_label">학술지</h3>
			<div class="book_slide swiper">
				<div class="swiper-wrapper">
					<div class="swiper-slide"><a href="">등록된 학술지가 없습니다.</a></div>
				</div>
			</div>
			<div class="book_control">
				<button type="button" class="arrow prev" aria-label="이전 슬라이드"></button>
				<button type="button" class="arrow next" aria-label="다음 슬라이드"></button>
			</div>
		</div>
	</div>
</section>

<!-- 학술대회 일정 / 학회 일정 (데이터 동적 바인딩 부분을 빈 화면으로 정리) -->
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
					@foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
						<li><button type="button">{{ $month }}</button></li>
					@endforeach
				</ul>
				<div class="schedule_slide">
					<div class="swiper-wrapper">
						<div class="swiper-slide empty">
							<p class="schedule_empty">등록된 학술행사가 없습니다.</p>
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
					<button type="button" class="arrow prev" aria-label="이전달"></button>
					<button type="button" class="arrow next" aria-label="다음달"></button>
					<span class="tomonth"></span>
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

<!-- 공지사항 / 온라인 아카데미 / 회원자료실 (기본 비어있는 상태로 고정) -->
<section class="mcon mc02" aria-labelledby="notice-title">
	<h2 class="sound_only" id="notice-title">학회 공지 및 배너 링크</h2>
	<div class="inner">
		<div class="long notice w100p">
			<div class="mtit"><h3>공지사항</h3><a href="/eng/news" class="more" aria-label="대한기능의학회 공지사항으로 이동"></a></div>
			<ul class="list">
				<li><a href="/eng/news_view">등록된 공지사항이 없습니다.<span class="date">-</span></a></li>
			</ul>
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
	// 변수 의존성이 없는 순수 Swiper 기본 구동 스크립트
	document.addEventListener('DOMContentLoaded', function () {
		if(document.querySelector('.mvisual-swiper')){
			new Swiper('.mvisual-swiper', {
				loop: true,
				navigation: { nextEl: '.mvisual-control .next', prevEl: '.mvisual-control .prev' },
				pagination: { el: '.mvisual-control .paging', type: 'fraction' }
			});
		}
	});
</script>
@endpush
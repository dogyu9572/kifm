@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName)
@section('gName', $gName)

@section('content')
@php
	$startDate = $conference->startDateParts($event);
	$preRegDeadline = $conference->deadline($event->pre_reg_end);
	$abstractDeadline = $conference->deadline($event->abstract_end);
	$mainTitle2 = $conference->mainTitle2($event);
	$mainSpeakers = $conference->mainSpeakers($event);
	$mainNotices = $conference->mainNotices($event);
	$mainSponsorGroups = $conference->mainSponsorGroups($event);
@endphp
<main class="main_wrap">

<section class="mvisual" aria-labelledby="visual-title" data-visual-bg="{{ $conference->imageUrl($event->pc_banner_path) }}">
	<div class="inner">
		<span class="top">{{ $conference->mainTitle1($event) }}</span>
		<h1 id="visual-title"><strong>KIFM {{ $event->year }}년</strong> {{ $conference->eventTitle($event) }}</h1>
		@if ($mainTitle2 !== '')
			<span class="color">{{ $mainTitle2 }}</span>
		@endif
		<div class="d_day">
			@if ($startDate)
				<time datetime="{{ $startDate['datetime'] }}"><strong>{{ $startDate['year'] }}</strong>년 <strong>{{ $startDate['month'] }}</strong>월 <strong>{{ $startDate['day'] }}</strong>일 ({{ $startDate['weekday'] }})</time>
			@endif
			<p>{{ $event->venue ?: '장소 미정' }}</p>
		</div>
		@if ($preRegDeadline || $abstractDeadline)
			<dl class="dead_line">
				@if ($preRegDeadline)
					<div>
						<dt>사전등록 마감일</dt>
						<dd>
							<time class="date" datetime="{{ $preRegDeadline['datetime'] }}">{{ $preRegDeadline['label'] }}</time>
							<div class="ani_date" aria-label="남은 기간 {{ $preRegDeadline['days'] }}일"><strong>{{ $preRegDeadline['days'] }}</strong>DAYS</div>
						</dd>
					</div>
				@endif
				@if ($abstractDeadline)
					<div>
						<dt>초록 접수 마감</dt>
						<dd>
							<time class="date" datetime="{{ $abstractDeadline['datetime'] }}">{{ $abstractDeadline['label'] }}</time>
							<div class="ani_date" aria-label="남은 기간 {{ $abstractDeadline['days'] }}일"><strong>{{ $abstractDeadline['days'] }}</strong>DAYS</div>
						</dd>
					</div>
				@endif
			</dl>
		@endif
	</div>
</section>

<section class="main_links" aria-label="학술대회 주요 서비스">
	<div class="inner">
		<ul class="link_list">
			<li><a href="{{ $conference->url($event, 'registration/check_member') }}" class="i1">사전등록확인</a></li>
			<li><a href="{{ $conference->url($event, 'abstract/check') }}" class="i2">초록 접수 확인</a></li>
			<li><a href="{{ $conference->url($event, 'program') }}" class="i3">프로그램 안내</a></li>
			<li><a href="{{ route('mypage.participation_history') }}" class="i4">참가내역서/영수증 조회</a></li>
		</ul>
	</div>
</section>

@if ($mainSpeakers->isNotEmpty())
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
					@foreach ($mainSpeakers as $speaker)
						<div class="swiper-slide">
							<div class="imgfit"><img src="{{ $speaker['image_url'] }}" alt="{{ $speaker['name'] }}"></div>
							<div class="txt">
								<div class="name">{{ $speaker['name'] }}</div>
								@if ($speaker['affiliation'] !== '')
									<p class="belong">{{ $speaker['affiliation'] }}</p>
								@endif
								@if ($speaker['title'] !== '')
									<p class="c_iden">{{ $speaker['title'] }}</p>
								@endif
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</section>
@endif

@if ($mainNotices->isNotEmpty())
	<section class="mcon mc02" aria-labelledby="notice-title">
		<div class="inner">
			<h2 class="mtit" id="notice-title">Notice</h2>
			<ul class="list">
				@foreach ($mainNotices as $notice)
					@php
						$noticeDate = \Illuminate\Support\Carbon::parse($notice->created_at);
					@endphp
					<li>
						<a href="{{ $conference->url($event, 'notice/view') }}?id={{ $notice->id }}">
							<span class="subject">{{ $notice->title }}</span>
							<time class="date" datetime="{{ $noticeDate->format('Y-m-d') }}">{{ $noticeDate->format('Y.m.d') }}</time>
						</a>
					</li>
				@endforeach
			</ul>
		</div>
	</section>
@endif

@if ($mainSponsorGroups->isNotEmpty())
	<section class="mcon mc03" aria-labelledby="sponsors-title">
		<div class="inner">
			<h2 class="mtit" id="sponsors-title">Sponsors</h2>
			@foreach ($mainSponsorGroups as $group)
				<section class="slide_area">
					<div class="tit">
						<h3>{{ $group['label'] }}</h3>
						<div class="navi">
							<button type="button" class="arrow prev" aria-label="이전 후원사">이전</button>
							<button type="button" class="arrow next" aria-label="다음 후원사">다음</button>
							<button type="button" class="papl pause on" aria-label="슬라이드 정지">정지</button>
							<button type="button" class="papl play" aria-label="슬라이드 재생">재생</button>
						</div>
					</div>
					<div class="sponsors_slide">
						<div class="swiper-wrapper">
							@foreach ($group['sponsors'] as $sponsor)
								<div class="swiper-slide"><a href="#this"><img src="{{ $sponsor['logo_url'] }}" alt="{{ $sponsor['name'] }}"></a></div>
							@endforeach
						</div>
					</div>
				</section>
			@endforeach
		</div>
	</section>
@endif

<section class="mcon mc04" aria-labelledby="location-title">
	<div class="inner">
		<h2 class="sound_only" id="location-title">오시는길, 초록집 다운로드</h2>
		<ul class="flex">
			<li class="i1"><a href="{{ $conference->url($event, 'venue') }}"><span>KIFM {{ $event->year }}년 {{ $conference->eventTitle($event) }}</span><strong>오시는 길</strong></a></li>
			<li class="i2"><a href="{{ $conference->url($event, 'abstract') }}"><span>KIFM {{ $event->year }}년 {{ $conference->eventTitle($event) }}</span><strong>초록집 다운로드</strong></a></li>
		</ul>
	</div>
</section>

</main>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('js/frontend/academic-conference-main.js') }}"></script>
@endpush

@extends('layouts.frontend')
@inject('onlineAcademy', 'App\Services\Frontend\PublicOnlineAcademyService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$authUser = auth()->user();
	$currentMember = $authUser?->role === 'user' ? $authUser : null;
@endphp
@if (session('alert'))
	<div data-page-alert="{{ session('alert') }}"></div>
@endif
<main class="sub_area">

@if ($featuredCourses->isNotEmpty())
	<section class="scon online_academy_head" aria-labelledby="online-academy-top-heading">
		<div class="inner">
			<h1 class="sound_only" id="online-academy-top-heading">대한기능의학회 온라인 아카데미 목록</h1>

			<div class="online_academy_slide_wrap">
				<div class="slide_txt">
					<div class="swiper-wrapper">
						@foreach ($featuredCourses as $featuredCourse)
							<div class="swiper-slide">
								<a href="{{ $onlineAcademy->entryUrl($featuredCourse, $currentMember) }}">
									<span class="course">COURSE</span>
									<h2 class="tit">{{ $featuredCourse->title }}</h2>
									<p>{{ $onlineAcademy->summaryText($featuredCourse, 140) }}</p>
								</a>
							</div>
						@endforeach
					</div>
				</div>
				<div class="slide_img">
					<div class="swiper-wrapper">
						@foreach ($featuredCourses as $featuredCourse)
							<div class="swiper-slide">
								<a href="{{ $onlineAcademy->entryUrl($featuredCourse, $currentMember) }}">
									<img src="{{ $onlineAcademy->imageUrl($featuredCourse->thumbnail_path, $onlineAcademy::FALLBACK_HEAD_IMAGE) }}" alt="">
								</a>
							</div>
						@endforeach
					</div>
				</div>
				<div class="navi">
					<button type="button" class="arrow prev" aria-label="이전 슬라이드">이전</button>
					<button type="button" class="arrow next" aria-label="다음 슬라이드">다음</button>
					<div class="line" aria-hidden="true"><span></span></div>
				</div>
			</div>

		</div>
	</section>
@endif

<section class="scon online_academy_list" aria-labelledby="online-academy-list-heading">
	<div class="inner">
		<h2 class="sub_title no_ico" id="online-academy-list-heading">NEW Releases</h2>
		<div class="mo_tabs_select">
			<button type="button" class="btn btn_select_opcl mo_vw"></button>
			<ul class="tabs full_line mb48" data-online-academy-tabs>
				@foreach ($courseTypeLabels as $typeCode => $typeLabel)
					@php
						$tabQuery = array_filter([
							'course_type' => $typeCode,
							'open_year' => $filters['open_year'] ?? null,
							'search_field' => ($filters['search_field'] ?? 'all') !== 'all' ? $filters['search_field'] : null,
							'search_keyword' => $filters['search_keyword'] ?? null,
						]);
						if (! empty($filters['keywords'])) {
							$tabQuery['keywords'] = $filters['keywords'];
						}
					@endphp
					<li class="{{ ($filters['course_type'] ?? '') === $typeCode ? 'on' : '' }}">
						<a href="{{ route('online_academy.index', $tabQuery) }}" data-course-type="{{ $typeCode }}">{{ $typeLabel }}</a>
					</li>
				@endforeach
			</ul>
		</div>

		<form method="GET" action="{{ route('online_academy.index') }}" class="board_top board_top_academy" id="online-academy-filter-form">
			<div class="left">
				<input type="hidden" name="course_type" value="{{ $filters['course_type'] ?? '' }}" data-course-type-input>
				<select name="open_year" class="text">
					<option value="">개설 연도</option>
					@foreach ($yearOptions as $year)
						<option value="{{ $year }}" @selected((string) ($filters['open_year'] ?? '') === (string) $year)>{{ $year }}년</option>
					@endforeach
				</select>
				<div class="select_custom @if(! empty($filters['keywords'])) on @endif" data-keyword-select>
					<button type="button" class="select_type">
						<span>강의 키워드</span>
						<ul class="user_select">
							@foreach (($filters['keywords'] ?? []) as $selectedKeyword)
								<li>{{ $selectedKeyword }}</li>
							@endforeach
						</ul>
					</button>
					<div class="select_check">
						<div class="select_list">
							<button type="button" data-keyword-value="전체" @class(['on' => empty($filters['keywords'])])>전체</button>
							@foreach ($keywordOptions as $keyword)
								<button type="button" data-keyword-value="{{ $keyword }}" @class(['on' => in_array($keyword, $filters['keywords'] ?? [], true)])>{{ $keyword }}</button>
							@endforeach
						</div>
						<div class="btns_btm flex_center">
							<button type="button" class="btn btn_reset btn_kwg">초기화</button>
							<button type="button" class="btn btn_check btn_woo">적용하기</button>
						</div>
					</div>
					<div class="js-keyword-hidden-inputs">
						@foreach (($filters['keywords'] ?? []) as $selectedKeyword)
							<input type="hidden" name="keywords[]" value="{{ $selectedKeyword }}">
						@endforeach
					</div>
				</div>
				<select name="search_field" class="text">
					@foreach ($searchFieldLabels as $fieldCode => $fieldLabel)
						<option value="{{ $fieldCode }}" @selected(($filters['search_field'] ?? 'all') === $fieldCode)>{{ $fieldLabel }}</option>
					@endforeach
				</select>
				<input type="text" name="search_keyword" class="text" value="{{ $filters['search_keyword'] ?? '' }}" placeholder="검색어를 입력해주세요.">
				<button type="submit" class="btn btn_wkk btn_search btn_small">검색</button>
				<a href="{{ route('online_academy.index') }}" class="btn btn_kwg btn_reset btn_small">초기화</a>
			</div>
		</form>

		<ul class="gallery_list gallery_academy">
			@forelse ($courses as $course)
				<li>
					<a href="{{ $onlineAcademy->entryUrl($course, $currentMember) }}">
						<span class="imgfit" aria-hidden="true"><img src="{{ $onlineAcademy->imageUrl($course->thumbnail_path, $onlineAcademy::FALLBACK_LIST_IMAGE) }}" alt=""></span>
						<span class="txt">
							<span class="type">{{ $courseTypeLabels[$course->course_type] ?? $course->course_type }}<span class="time">{{ $onlineAcademy->compactPeriodText($course) }}</span></span>
							<h3>{{ $course->title }}</h3>
							@if ($onlineAcademy->professorText($course) !== '')
								<p class="name">{{ $onlineAcademy->professorText($course) }}</p>
							@endif
						</span>
					</a>
				</li>
			@empty
				<li class="no_board">
					<span class="state"></span>
					<h4>검색 조건에 해당하는 학술대회가 없습니다.</h4>
				</li>
			@endforelse
		</ul>

		<x-frontend.pagination :paginator="$courses" />
	</div>
</section>

</main>
@endsection

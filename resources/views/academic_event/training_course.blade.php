@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@inject('trainingCourse', 'App\Services\Frontend\PublicTrainingCourseService')
<main class="sub_area">

<section class="scon academic_event_wrap" aria-labelledby="training-course-heading">
	<div class="inner">
		<h1 class="sub_title" id="training-course-heading">{{ $sName }}</h1>

		@if ($featuredTraining)
			@php
				$featuredStatus = $trainingCourse->status($featuredTraining);
				$featuredRound = $trainingCourse->publicRounds($featuredTraining)->first();
			@endphp
			<div class="academic_event_head">
				<div class="imgfit" aria-hidden="true"><img src="{{ $trainingCourse->imageUrl(null) }}" alt=""></div>
				<div class="txt">
					<a href="{{ $trainingCourse->detailUrl($featuredTraining) }}">
						<h2 class="mt0">{{ $featuredTraining->title }}</h2>
						<p class="eng_title">{{ $trainingCourse->headlineText($featuredTraining) }}</p>
						<ul class="info_list">
							<li class="i1"><strong>일시</strong>{{ $trainingCourse->roundDateText($featuredRound) }}</li>
							<li class="i2"><strong>사전등록</strong>{{ $trainingCourse->applicationPeriodText($featuredRound) }}</li>
							<li class="i3"><strong>장소</strong>{{ $featuredRound?->location_link ?: '-' }}</li>
						</ul>
					</a>
					<div class="btns">
						<a href="{{ $trainingCourse->detailUrl($featuredTraining) }}" class="btn btn_wkk btn_outlink">자세히 보기</a>
						<a href="{{ $trainingCourse->paymentUrl($featuredTraining) }}" class="btn btn_wrr btn_outlink">사전등록 바로가기</a>
					</div>
					<x-frontend.bookmark-button content-type="academic_event_training_course" :content-id="$featuredTraining->id" :title="$featuredTraining->title" :menu-label="$sName" :url="$trainingCourse->detailUrl($featuredTraining)" label="이 행사를 북마크에 추가" />
				</div>
			</div>
		@endif

		<div class="academic_event_body">
			<div class="bdbtit">
				<h3>최신 {{ $sName }} 목록</h3>
				<ul class="tabs" role="tablist" data-training-course-tabs>
					@foreach ($statusLabels as $code => $label)
						<li @class(['on' => $filters['status'] === $code])>
							<a href="{{ route('academic_event.training_course', array_filter(['status' => $code === 'all' ? null : $code, 'year' => $filters['year'], 'keyword' => $filters['keyword']])) }}" role="tab" aria-selected="{{ $filters['status'] === $code ? 'true' : 'false' }}" data-training-status="{{ $code }}">{{ $label }}</a>
						</li>
					@endforeach
				</ul>
			</div>
			<form method="GET" action="{{ route('academic_event.training_course') }}" class="board_top" id="training-course-filter-form">
				<div class="left">
					<label for="training-year" class="sound_only">연도 선택</label>
					<input type="hidden" name="status" value="{{ $filters['status'] === 'all' ? '' : $filters['status'] }}" data-training-status-input>
					<span aria-hidden="true" class="tt">행사년도</span>
					<select name="year" id="training-year" class="years" data-auto-submit-form>
						<option value="">전체</option>
						@foreach ($yearOptions as $year)
							<option value="{{ $year }}" @selected((string) $year === $filters['year'])>{{ $year }}</option>
						@endforeach
					</select>
				</div>
				<div class="right">
					<div class="search_area">
						<label for="training-search" class="sound_only">연수명 검색</label>
						<input type="text" id="training-search" name="keyword" class="text" value="{{ $filters['keyword'] }}" placeholder="연수명을 입력해주세요">
						<button type="submit" class="btn_search">검색</button>
					</div>
				</div>
			</form>
			<ul class="list training-course-list">
				@forelse ($trainings as $training)
					@php
						$status = $trainingCourse->status($training);
						$round = $trainingCourse->publicRounds($training)->first();
					@endphp
					<li @class(['end' => $status['code'] === 'closed'])>
						<a href="{{ $trainingCourse->detailUrl($training) }}">
							<span class="states_area flex">
								<span class="state {{ $status['class'] }}"><span class="sound_only">상태:</span>{{ $status['label'] }}</span>
								<!-- <span class="state online">온라인 강의 시청 가능</span> -->
							</span>
							<h4>{{ $training->title }}</h4>
							<p class="summary">{{ $trainingCourse->headlineText($training) }}</p>
							<ul class="details">
								<li><strong>일시</strong> {{ $trainingCourse->roundDateText($round) }}</li>
								<li><strong>장소</strong> {{ $round?->location_link ?: '-' }}</li>
							</ul>
						</a>
						<x-frontend.bookmark-button content-type="academic_event_training_course" :content-id="$training->id" :title="$training->title" :menu-label="$sName" :url="$trainingCourse->detailUrl($training)" label="이 행사를 북마크에 추가" />
					</li>
				@empty
					<li class="training-course-empty">
						<div>
							<span class="state end"><span class="sound_only">상태:</span>없음</span>
							<h4>등록된 연수강좌가 없습니다.</h4>
							<p>검색 조건을 변경해 주세요.</p>
						</div>
					</li>
				@endforelse
			</ul>
		</div>

		<x-frontend.pagination :paginator="$trainings" />
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/script_bookmark.js') }}"></script>
<script src="{{ asset('js/frontend/training-course.js') }}"></script>
@endpush

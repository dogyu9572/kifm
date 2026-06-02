@extends('layouts.frontend')
@inject('publicAcademicEvent', 'App\Services\Frontend\PublicAcademicEventService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_wrap" aria-labelledby="conference-heading">
	<div class="inner">
		<h1 class="sub_title" id="conference-heading">{{ $sName }}</h1>

		@if ($featuredConference)
			<div class="academic_event_head">
				<div class="imgfit" aria-hidden="true"><img src="{{ $publicAcademicEvent->imageUrl($featuredConference->pc_banner_path) }}" alt=""></div>
				<div class="txt">
					<p class="eng_title c_iden">{{ $publicAcademicEvent->headlineText($featuredConference) }}</p>
					<h2>{{ $featuredConference->title }}</h2>
					<ul class="info_list">
						<li class="i1"><strong>일시</strong>{{ $publicAcademicEvent->eventDateText($featuredConference) }}</li>
						<li class="i2"><strong>사전등록</strong>{{ $publicAcademicEvent->preRegistrationText($featuredConference) }}</li>
						<li class="i3"><strong>장소</strong>{{ $featuredConference->venue ?: '-' }}</li>
					</ul>
					<div class="btns">
						<a href="{{ $publicAcademicEvent->eventUrl($featuredConference) }}" target="_blank" title="새창 열림" class="btn btn_wkk btn_outlink">홈페이지 바로가기</a>
						<a href="{{ $publicAcademicEvent->registrationUrl($featuredConference) }}" target="_blank" title="새창 열림" class="btn btn_wrr btn_outlink">사전등록 바로가기</a>
					</div>
					<x-frontend.bookmark-button content-type="academic_event" :content-id="$featuredConference->id" :title="$featuredConference->title" :menu-label="$sName" :url="$publicAcademicEvent->eventUrl($featuredConference)" label="이 행사를 북마크에 추가" />
				</div>
			</div>
		@endif

		<div class="academic_event_body">
			<div class="bdbtit">
				<h3>최신 {{ $sName }} 목록</h3>
				<ul class="tabs" role="tablist">
					@foreach ($statusLabels as $statusCode => $statusLabel)
						@php
							$statusQuery = array_filter([
								'status' => $statusCode !== 'all' ? $statusCode : null,
								'year' => $filters['year'] ?? null,
								'keyword' => $filters['keyword'] ?? null,
							]);
						@endphp
						<li class="{{ ($filters['status'] ?? 'all') === $statusCode ? 'on' : '' }}">
							<a href="{{ route('academic_event.conference', $statusQuery) }}" role="tab" aria-selected="{{ ($filters['status'] ?? 'all') === $statusCode ? 'true' : 'false' }}">{{ $statusLabel }}</a>
						</li>
					@endforeach
                </ul>
			</div>
			<form method="GET" action="{{ route('academic_event.conference') }}" class="board_top">
				<div class="left">
					<label for="event-year" class="sound_only">행사년도 선택</label>
					<input type="hidden" name="status" value="{{ ($filters['status'] ?? 'all') !== 'all' ? $filters['status'] : '' }}">
					<select name="year" id="event-year" class="years" data-auto-submit-form>
						<option value="">행사년도</option>
						@foreach ($yearOptions as $year)
							<option value="{{ $year }}" @selected((string) ($filters['year'] ?? '') === (string) $year)>{{ $year }}년</option>
						@endforeach
					</select>
				</div>
				<div class="right">
					<div class="search_area">
						<label for="event-search" class="sound_only">대회명 검색</label>
						<input type="text" id="event-search" name="keyword" class="text" value="{{ $filters['keyword'] ?? '' }}" placeholder="대회명을 입력해주세요">
						<button type="submit" class="btn_search">검색</button>
					</div>
				</div>
			</form>
			<ul class="list">
				@forelse ($conferences as $conference)
					@php $status = $publicAcademicEvent->status($conference); @endphp
					<li class="{{ $status['class'] }}">
						<a href="{{ $publicAcademicEvent->eventUrl($conference) }}" @if($status['code'] === 'closed') data-closed-conference @endif>
							<span class="states_area flex">
								<span class="state {{ $status['class'] }}"><span class="sound_only">상태:</span>{{ $status['label'] }}</span>
							</span>
							<h4>{{ $conference->title }}</h4>
							<p class="summary">{{ $publicAcademicEvent->headlineText($conference) }}</p>
							<ul class="details">
								<li><strong>일시</strong> {{ $publicAcademicEvent->eventDateText($conference) }}</li>
								<li><strong>사전등록</strong> {{ $publicAcademicEvent->preRegistrationText($conference) }}</li>
								<li><strong>장소</strong> {{ $conference->venue ?: '-' }}</li>
							</ul>
						</a>
						<x-frontend.bookmark-button content-type="academic_event" :content-id="$conference->id" :title="$conference->title" :menu-label="$sName" :url="$publicAcademicEvent->eventUrl($conference)" label="이 행사를 북마크에 추가" />
					</li>
				@empty
					<li class="no_board">
						<span class="state"></span>
						<h4>검색 조건에 해당하는 학술대회가 없습니다.</h4>
					</li>
				@endforelse
			</ul>
		</div>

		<x-frontend.pagination :paginator="$conferences" :window-size="5" />

	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/script_bookmark.js') }}"></script>
<script src="{{ asset('js/frontend/academic-event-conference.js') }}"></script>
@endpush

@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$keyword = trim((string) request('keyword'));
	$speakers = $event->speakers;

	if ($keyword !== '') {
		$speakers = $speakers->filter(function ($speaker) use ($conference, $keyword) {
			$target = implode(' ', [
				$speaker->name,
				$conference->speakerTitle($speaker),
				$conference->speakerAffiliation($speaker),
				$conference->speakerPosition($speaker),
			]);

			return stripos($target, $keyword) !== false;
		});
	}
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="speakers-heading">
	<div class="inner">
		<h1 class="sub_title" id="speakers-heading">{{ $sName }}</h1>
		
		<div class="board_top">
			<div class="left">&nbsp;</div>
			<div class="right flex">
				<form class="search_area" method="GET" action="{{ $conferenceBaseUrl }}/speakers">
					<label for="event-search" class="sound_only">검색어 검색</label>
					<input type="text" id="event-search" name="keyword" class="text" value="{{ $keyword }}" placeholder="검색어를 입력하세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		@if ($speakers->isNotEmpty())
			<ul class="speakers_area">
				@foreach ($speakers as $speaker)
					<li>
						<button type="button" class="speakers_btn" data-speaker-popup-target="pop_speaker_{{ $speaker->id }}">
							<span class="imgfit" aria-hidden="true"><img src="{{ $conference->speakerImageUrl($speaker) }}" alt=""></span>
							<span class="txt">
								<span class="top">
									<span class="session">Speaker</span>
								</span>
								<h2>{{ $conference->speakerTitle($speaker) }}</h2>
								@if ($conference->speakerRoleLine($speaker) !== '')
									<p class="position">{{ $conference->speakerRoleLine($speaker) }}</p>
								@endif
								<p class="name">{{ $speaker->name }}</p>
								<i class="btn btn_wkk">연자보기</i>
							</span>
						</button>
					</li>
				@endforeach
			</ul>
		@else
			<div class="speakers_area">
				<p>등록된 연자가 없습니다.</p>
			</div>
		@endif
	</div>
</section>

</main>

@foreach ($speakers as $speaker)
	<div class="popup pop_speakers" id="pop_speaker_{{ $speaker->id }}" data-speaker-popup>
		<div class="dm" data-speaker-popup-close></div>
		<div class="inbox">
			<button type="button" class="btn_close" data-speaker-popup-close>Close</button>
			<div class="head">
				<div class="imgfit" aria-hidden="true"><img src="{{ $conference->speakerImageUrl($speaker) }}" alt=""></div>
				<div class="txt">
					<h2 class="name">{{ $speaker->name }}</h2>
					@if ($conference->speakerRoleLine($speaker) !== '')
						<div class="position">{{ $conference->speakerRoleLine($speaker) }}</div>
					@endif
				</div>
			</div>
			<div class="gbox con">
				<dl class="scroll">
					<div>
						<dt>발표 주제</dt>
						<dd>
							<p>{{ $conference->speakerTitle($speaker) }}</p>
						</dd>
					</div>
					<div>
						<dt>주요 약력</dt>
						<dd>
							@if ($conference->contentHtml($speaker->bio) !== '')
								{!! $conference->contentHtml($speaker->bio) !!}
							@else
								<p>등록된 약력이 없습니다.</p>
							@endif
						</dd>
					</div>
				</dl>
			</div>
			<div class="btns flex_center"><button type="button" class="btn btn_wkk" data-speaker-popup-close>닫기</button></div>
		</div>
	</div>
@endforeach

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-program.js') }}"></script>
@endpush

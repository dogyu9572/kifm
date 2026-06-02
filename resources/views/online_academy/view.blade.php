@extends('layouts.frontend')
@inject('onlineAcademy', 'App\Services\Frontend\PublicOnlineAcademyService')
@section('title', $gName . ' | ' . $course->title)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$lectureFileUrl = $onlineAcademy->lectureFileUrl($course);
	$videoEmbedUrl = $onlineAcademy->videoEmbedUrl($course->video_url);
	$topicItems = $onlineAcademy->topicList($course->topics);
	$keywordItems = $onlineAcademy->keywordList($course->keywords);
	$progress = $onlineAcademy->progressData($enrollment ?? null);
@endphp
<main class="sub_area">

<section class="scon online_academy_view" aria-labelledby="online-academy-heading"
	data-progress-url="{{ route('online_academy.progress', $course) }}"
	data-initial-progress="{{ $progress['progress_rate'] }}"
	data-initial-position="{{ $progress['last_position_sec'] }}"
	data-initial-duration="{{ $progress['video_duration_sec'] ?: $onlineAcademy->courseDurationSeconds($course) }}">
	<div class="inner">

		<div class="board_view nbd_t">
			<div class="view_top">
				<button type="button" class="btn_back" data-history-back>뒤로</button>
			</div>
			<div class="tit_area">
				<h1 class="tit" id="online-academy-heading">{{ $course->title }}</h1>
				@if ($course->topic_detail)
					<div class="sub_tit">{{ $course->topic_detail }}</div>
				@endif
				@if ($lectureFileUrl)
					<a href="{{ $lectureFileUrl }}" class="btn_abso btn_kwk btn_download" download>강의록 다운로드</a>
				@endif
			</div>
			@error('progress')
				<p class="c_red">{{ $message }}</p>
			@enderror
			<div class="state_line">
				<div class="line"><div class="bar" role="progressbar" aria-valuenow="{{ $progress['progress_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div></div>
				<div class="flex">
					<div class="left">수강률 <strong class="percent_val">{{ $progress['progress_rate'] }}%</strong></div>
					<div class="right"><strong class="watched_min">{{ $progress['watched_min'] }}분</strong> / <span class="duration_text">{{ $onlineAcademy->durationText($course) }}</span></div>
				</div>
			</div>
			<div class="cont">
				<div class="video">
					@if ($videoEmbedUrl)
						<iframe src="{{ $videoEmbedUrl }}" title="{{ $course->title }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen data-vimeo-player></iframe>
					@elseif ($course->video_url)
						<a href="{{ $course->video_url }}" target="_blank" rel="noopener" class="video_link">
							<span>{{ $course->video_url }}</span>
						</a>
					@else
						<img src="{{ $onlineAcademy->imageUrl($course->thumbnail_path, $onlineAcademy::FALLBACK_VIEW_IMAGE) }}" alt="">
					@endif
				</div>
				<article class="txt">
					<div class="tit">강의 내용</div>
					@if ($course->content)
						<div>{!! $course->content !!}</div>
					@else
						<p>{{ $onlineAcademy->summaryText($course) }}</p>
					@endif
					@if ($topicItems !== [])
						<ul class="label">
							@foreach ($topicItems as $topic)
								<li>{{ $topic }}</li>
							@endforeach
						</ul>
					@endif
					@if ($keywordItems !== [])
						<div class="tit">키워드</div>
						<ul class="keyword">
							@foreach ($keywordItems as $keyword)
								<li># {{ $keyword }}</li>
							@endforeach
						</ul>
					@endif
					<div class="btn_area">
						<div class="txtbox" aria-hidden="true">학습을 모두 마치셨나요?<br/>아래 버튼을 누르면 간단한 테스트 후 수강 완료 처리가 됩니다.</div>
						<a href="{{ route('online_academy.exam', $course) }}" class="btn btn_test {{ $progress['is_completed'] ? '' : 'disabled' }}" aria-disabled="{{ $progress['is_completed'] ? 'false' : 'true' }}">시험보기</a>
					</div>
				</article>
			</div>
		</div>

	</div>
</section>

</main>
@endsection

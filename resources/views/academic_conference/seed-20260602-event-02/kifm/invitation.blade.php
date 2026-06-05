@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$greetingTitle = $event->greeting_title ?: $event->title;
	$greetingContent = $conference->contentHtml($event->greeting_content);
	$greetingImage = $conference->optionalImageUrl($event->greeting_image_path);
	$marqueeText = 'The ' . $event->year . ' KIFM Conference';
@endphp
<main class="sub_area pb0">

<section class="scon invitation_wrap" aria-labelledby="invitation-title">
	<div class="inner">
		<h1 class="sub_title" id="invitation-title">{{ $sName }}</h1>
		
		<div class="invitation_area{{ $greetingImage ? '' : ' no_image' }}">
			<div class="txt">
				<h2>{!! nl2br(e($greetingTitle)) !!}</h2>
				<div class="con">
					@if ($greetingContent !== '')
						{!! $greetingContent !!}
					@else
						<p>등록된 초대의 글이 없습니다.</p>
					@endif					
				</div>
			</div>
			@if ($greetingImage)
				<div class="img" aria-hidden="true"><img src="{{ $greetingImage }}" alt=""></div>
			@endif
		</div>
	</div>
	<div class="marquee_container" data-kifm-marquee>
		<div class="marquee_wrapper">
			<div class="marquee_group">
				<div class="marquee_content">{{ $marqueeText }}</div>
			</div>
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-kifm.js') }}"></script>
@endpush

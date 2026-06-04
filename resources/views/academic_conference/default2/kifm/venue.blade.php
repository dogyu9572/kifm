@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$fullAddress = $conference->fullAddress($event);
	$walkingGuide = $conference->contentHtml($event->walking_guide);
	$shuttleGuide = $conference->contentHtml($event->shuttle_guide);
	$kakaoJavascriptKey = (string) config('local_doctor_map.kakao.javascript_key', '');
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="venue-title">
	<div class="inner">
		<h1 class="sub_title" id="venue-title">{{ $sName }}</h1>

		<div class="venue_area">
			<div class="map_area">
				<div
					id="daumRoughmapContainer1777006266657"
					class="root_daum_roughmap root_daum_roughmap_landing"
					data-kifm-roughmap
					data-kifm-map-address="{{ $fullAddress }}"
					data-kifm-map-lat="{{ $event->address_lat }}"
					data-kifm-map-lng="{{ $event->address_lng }}"
					data-kifm-map-key="{{ $kakaoJavascriptKey }}"
					data-timestamp="1777006266657"
					data-key="2aspkv7dzuzp"
					data-width="800"
					data-height="480"
				></div>
			</div>
			<div class="txt_area">
				<h2><strong class="c_iden">{{ $event->venue ?: '장소 미정' }}</strong>@if($event->address_detail)<br/>{{ $event->address_detail }}@endif</h2>
				<dl>
					<div class="i1">
						<dt>주소</dt>
						<dd>
							<p class="copy_txt">{{ $fullAddress !== '' ? $fullAddress : '주소 미정' }}</p>
							@if ($fullAddress !== '')
								<button type="button" class="btn_copy c_iden" data-copy-address>복사</button>
							@endif
						</dd>
					</div>
					@if ($walkingGuide !== '')
						<div class="i2">
							<dt>도보</dt>
							<dd>{!! $walkingGuide !!}</dd>
						</div>
					@endif
					@if ($shuttleGuide !== '')
						<div class="i3">
							<dt>셔틀버스</dt>
							<dd>{!! $shuttleGuide !!}</dd>
						</div>
					@endif
				</dl>
			</div>
		</div>
		@if ($event->venueFloors->isNotEmpty())
			<div class="venue_floor_area">
				@foreach ($event->venueFloors as $floor)
					@php $floorFileUrl = $conference->floorFileUrl($floor->file_path); @endphp
					@if ($floorFileUrl)
						<figure>
							<figcaption>{{ $floor->floor_name }}</figcaption>
							<a href="{{ $floorFileUrl }}" target="_blank" rel="noopener">{{ $floor->floor_name }} 안내 보기</a>
						</figure>
					@endif
				@endforeach
			</div>
		@endif
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-kifm.js') }}"></script>
@endpush

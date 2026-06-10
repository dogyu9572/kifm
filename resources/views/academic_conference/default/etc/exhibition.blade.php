@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
<main class="sub_area">

	<section class="scon" aria-labelledby="exhibition-heading">
		<div class="inner">
			<h1 class="sub_title" id="exhibition-heading">{{ $sName }}</h1>
			@php
				$exhibitionFloors = $event->venueFloors->filter(fn ($floor) => filled($floor->file_path) && $conference->floorFileUrl($floor->file_path));
			@endphp

		@if ($exhibitionFloors->isNotEmpty())
			<ul class="tabs full_line mb" data-exhibition-tabs>
				@foreach ($exhibitionFloors as $floor)
					@php $floorFileUrl = $conference->floorFileUrl($floor->file_path); @endphp
					<li @class(['on' => $loop->first])><a href="#exhibition-floor-{{ $floor->id }}" data-exhibition-tab>{{ $floor->floor_name }}</a></li>
				@endforeach
			</ul>

			<div class="exhibition_floor_area">
				@foreach ($exhibitionFloors as $floor)
					@php $floorFileUrl = $conference->floorFileUrl($floor->file_path); @endphp
					<figure id="exhibition-floor-{{ $floor->id }}" class="exhibition_floor_panel" data-exhibition-panel @if(! $loop->first) hidden @endif>
						@if ($conference->isImageFile($floor->file_path))
							<img src="{{ $floorFileUrl }}" alt="{{ $floor->floor_name }} 안내">
						@else
							<a href="{{ $floorFileUrl }}" target="_blank" rel="noopener" class="btn btn_wbb btn_outlink">{{ $floor->floor_name }} 안내 보기</a>
						@endif
					</figure>
				@endforeach
			</div>
		@else
			<div class="flex_center"><img src="{{ $conference->imageUrl($event->exhibition_image_path, 'images/img_exhibition.jpg') }}" alt=""></div>
		@endif
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-exhibition.js') }}"></script>
@endpush

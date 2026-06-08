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
		
		<ul class="tabs full_line mb">
			<li class="on"><a href="#this">1F</a></li>
			<li><a href="#this">2F</a></li>
			<li><a href="#this">3F</a></li>
		</ul>

		<div class="flex_center"><img src="{{ $conference->imageUrl($event->exhibition_image_path, 'images/img_exhibition.jpg') }}" alt=""></div>
	</div>
</section>

</main>
@endsection

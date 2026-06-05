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

		<div class="flex_center"><img src="{{ $conference->imageUrl($event->exhibition_image_path, 'images/img_exhibition.jpg') }}" alt=""></div>
	</div>
</section>

</main>
@endsection

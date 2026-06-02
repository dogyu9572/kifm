@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php $committeeContent = $conference->contentHtml($event->committee_content); @endphp
<main class="sub_area">

<section class="scon" aria-labelledby="committee-title">
	<div class="inner">
		<h1 class="sub_title" id="committee-title">{{ $sName }}</h1>
		
		<div class="committee_area">
			<h2 class="sound_only">{{ $sName }} 목록</h2>
			@if ($committeeContent !== '')
				{!! $committeeContent !!}
			@else
				<p>등록된 조직위원회 내용이 없습니다.</p>
			@endif
		</div>
	</div>
</section>

</main>
@endsection

@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="bylaws-heading">
	<div class="inner">
		<h1 class="sub_title" id="bylaws-heading">{{ $dName }}</h1>

		<div class="bylaws_wrap">
			@if ($post)
				{!! $post->content !!}
			@else
				<article class="bylaws_item">
					<div class="con">
						<p>등록된 회칙이 없습니다.</p>
					</div>
				</article>
			@endif
		</div>
		
	</div>
</section>
	
</main>

@endsection

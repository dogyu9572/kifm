@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon officers_wrap" aria-labelledby="officers-heading">
	<div class="inner">
		<h1 class="sub_title" id="officers-heading">{{ $sName }}</h1>

		<ul class="officers_head">
			@foreach($headExecutives as $executive)
				<li>
					@if($executive['photo_url'])
						<div class="imgfit" aria-hidden="true"><img src="{{ $executive['photo_url'] }}" alt=""></div>
					@endif
					<div class="txt">
						<h2 class="name"><strong>{{ $executive['name'] }}</strong><span class="c_iden">{{ $executive['position'] }}</span></h2>
						<div class="con">
							@if($executive['email'])
								<p class="i1"><span class="sound_only">이메일</span><a href="mailto:{{ $executive['email'] }}">{{ $executive['email'] }}</a></p>
							@endif
							@if($executive['organization'])
								<p class="i2">{{ $executive['organization'] }}</p>
							@endif
						</div>
					</div>
				</li>
			@endforeach
		</ul>

		<ul class="officers_body">
			@forelse($bodyExecutives as $executive)
				<li>
					<div class="txt">
						<h2 class="name"><strong>{{ $executive['name'] }}</strong><span>{{ $executive['position'] }}</span></h2>
						<div class="con">
							@if($executive['email'])
								<p class="i1"><span class="sound_only">이메일</span><a href="mailto:{{ $executive['email'] }}">{{ $executive['email'] }}</a></p>
							@endif
							@if($executive['organization'])
								<p class="i2">{{ $executive['organization'] }}</p>
							@endif
						</div>
					</div>
				</li>
			@empty
				<li>
					<div class="txt">
						<h2 class="name"><strong>등록된 임원진이 없습니다.</strong></h2>
					</div>
				</li>
			@endforelse
		</ul>
		
	</div>
</section>
	
</main>

@endsection

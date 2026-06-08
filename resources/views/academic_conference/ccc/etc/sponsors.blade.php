@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="sponsors-heading">
	<div class="inner">
		<h1 class="sub_title" id="sponsors-heading">{{ $sName }}</h1>
		
		<div class="sponsors_wrap">
			@foreach(($sponsorGroups ?? collect()) as $group)
				<section class="sponsor_group" aria-labelledby="tier-{{ $group['level'] }}">
					<div class="tit"><span>{{ $sName }}</span><h2 id="tier-{{ $group['level'] }}">{{ $group['label'] }}</h2></div>
					<ul class="con">
						@foreach($group['sponsors'] as $sponsor)
							<li>
								@if($sponsor['logo_url'])
									<img src="{{ $sponsor['logo_url'] }}" alt="{{ $sponsor['name'] }}">
								@else
									<span>{{ $sponsor['name'] }}</span>
								@endif
							</li>
						@endforeach
					</ul>
				</section>
			@endforeach
			
		</div>
		
	</div>
</section>

</main>
@endsection

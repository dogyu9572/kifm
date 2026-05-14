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
		
			<section class="sponsor_group" aria-labelledby="tier-vip">
				<div class="tit"><span>{{ $sName }}</span><h2 id="tier-vip">VIP</h2></div>
				<ul class="con">
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
				</ul>
			</section>
		
			<section class="sponsor_group" aria-labelledby="tier-gold">
				<div class="tit"><span>{{ $sName }}</span><h2 id="tier-gold">Gold</h2></div>
				<ul class="con">
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
				</ul>
			</section>
		
			<section class="sponsor_group" aria-labelledby="tier-silver">
				<div class="tit"><span>{{ $sName }}</span><h2 id="tier-silver">Silver</h2></div>
				<ul class="con">
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
				</ul>
			</section>
		
			<section class="sponsor_group" aria-labelledby="tier-exhibitors">
				<div class="tit"><span>{{ $sName }}</span><h2 id="tier-exhibitors">Exhibitors</h2></div>
				<ul class="con">
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
					<li><img src="/images/img_sample_sponsors.svg" alt=""></li>
				</ul>
			</section>
			
		</div>
		
	</div>
</section>

</main>
@endsection
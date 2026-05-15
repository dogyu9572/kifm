@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="subcommittee-heading">
	<div class="inner">
		<h1 class="sub_title" id="subcommittee-heading">{{ $sName }}</h1>

		<div class="subcommittee_top">
			<h2>대한기능의학회 <br/><strong class="c_iden">산하위원회</strong></h2>
			<div class="con">
				<p>대한기능의학회는 관련된 다양한 연구와 정보교환, 협업을 토대로 국내외 환자들의 치료에 기여하고자 합니다.<br/>위원회 참석 및 기타 문의는 대한기능의학회로 문의 바랍니다.</p>
				<ul class="tel_mail_infobox flex">
					<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
					<li class="i2"><a href="mailto:0182253645@naver.com">0182253645@naver.com</a></li>
				</ul>
			</div>
		</div>

		<ul class="subcommittee_list">
			@forelse ($committees as $c)
				@php
					$thumbUrl = ! empty($c->thumbnail_path)
						? asset('storage/'.$c->thumbnail_path)
						: asset('images/bg_sample_subcommittee_list.jpg');
				@endphp
				<li>
					<a href="{{ route('subcommittee.notice', $c) }}">
						<span class="imgfit" aria-hidden="true"><img src="{{ $thumbUrl }}" alt=""></span>
						<span class="txt">
							<h3>{{ $c->name }}</h3>
							<span class="con">
								<p>{{ $c->description ?? '' }}</p>
								<i class="btn btn_wbb">위원회 가입 신청</i>
							</span>
						</span>
					</a>
				</li>
			@empty
				<li>
					<p class="tac">등록된 산하위원회가 없습니다.</p>
				</li>
			@endforelse
		</ul>

	</div>
</section>

</main>

@endsection

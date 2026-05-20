@extends('layouts.frontend')
@section('title', $gName . ' | ' . $course->title)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon online_academy_test" aria-labelledby="online-academy-heading">
	<h1 class="sound_only" id="online-academy-heading">학습 테스트 페이지</h1>
	<div class="inner">

		<div class="view_top">
			<button type="button" class="btn_back btn_give_up" data-history-back>시험 포기하기</button>
		</div>

		<div class="test_page">
			<h2 class="test_title">학습 테스트</h2>
			<div class="test_state_line">
				<div class="line"><div class="bar"></div></div>
				<div class="step">{{ $stepText }}</div>
			</div>
			@if ($question)
				<article>
					<h3 class="test_title">Q{{ $currentStep }}</h3>
					<p class="tac">{{ $question->question }}</p>
					@if ($choices !== [])
						<ul class="a_list">
							@foreach ($choices as $choice)
								<li><div class="test_radio"><input type="radio" name="test_select" id="{{ $choice['id'] }}"><label for="{{ $choice['id'] }}"><i>{{ $choice['number'] }}</i><span>{{ $choice['text'] }}</span></label></div></li>
							@endforeach
						</ul>
					@endif
				</article>
			@endif
			<div class="btns_btm flex_center">
				<button type="button" class="btn btn_kwg prev" data-history-back>이전</button>
				<a href="{{ route('online_academy.end', ['course' => $course->id]) }}" class="btn btn_woo2 next">다음</a>
			</div>
		</div>

	</div>
</section>

</main>
@endsection

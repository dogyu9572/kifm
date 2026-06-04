@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$score = (int) ($result['score'] ?? 0);
	$correct = (int) ($result['correct'] ?? 0);
	$total = (int) ($result['total'] ?? 0);
	$passed = (bool) ($result['passed'] ?? false);
@endphp
<main class="sub_area" data-prevent-online-exam-back data-online-exam-back-url="{{ route('online_academy.index') }}">

<section class="scon online_academy_test" aria-labelledby="online-academy-end-heading">
	<h1 class="sound_only" id="online-academy-end-heading">학습 테스트 완료 페이지</h1>

	<div class="inner">

		<div class="test_page">

			<div class="test_end" data-passed="{{ $passed ? '1' : '0' }}">
				<div class="tit">{{ $passed ? '축하합니다.' : '아쉽지만 불합격 하셨습니다.' }}</div>
				<div class="gbox">
					<h2>{{ $score }}점</h2>
					@if ($passed)
						<p>{{ $total }}문제 중 {{ $correct }}문제를 맞히셨습니다. 이제 수강 완료 처리가 가능합니다.<br/>지금까지 고생하셨습니다.</p>
					@else
						<p>{{ $total }}문제 중 {{ $correct }}문제를 맞히셨습니다.<br/>다시 한번 도전해 보세요!</p>
					@endif
				</div>
			</div>

			<div class="btns_btm flex_center">
				<a href="{{ isset($course) ? route('online_academy.show', $course) : route('online_academy.view') }}" class="btn btn_kwg">강의로 돌아가기</a>
				<a href="{{ isset($course) ? route('online_academy.exam', $course) : route('online_academy.test') }}" class="btn btn_wkk">다시 응시하기</a>
				@if ($passed)
					<a href="{{ route('home.alt') }}" class="btn btn_woo2">수강 완료하기</a>
				@endif
			</div>
		</div>

	</div>
</section>

</main>
@endsection

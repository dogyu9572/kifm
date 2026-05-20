@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon online_academy_test" aria-labelledby="online-academy-heading">
	<h1 class="sound_only" id="online-academy-heading">학습 테스트 페이지</h1>
	<div class="inner">
	
		<div class="view_top">
			<button type="button" class="btn_back btn_give_up">시험 포기하기</button>
		</div>

		<div class="test_page">
			<h2 class="test_title">학습 테스트</h2>
			<div class="test_state_line">
				<div class="line"><div class="bar"></div></div>
				<div class="step">01/05</div>
			</div>
			<article>
				<h3 class="test_title">Q2</h3>
				<p class="tac">What is the primary benefit of a microservices architecture compared to a monolith?</p>
				<ul class="a_list">
					<li><div class="test_radio"><input type="radio" name="test_select" id="test_select01"><label for="test_select01"><i>1</i><span>Independent scalability of service</span></label></div></li>
					<li><div class="test_radio"><input type="radio" name="test_select" id="test_select02"><label for="test_select02"><i>2</i><span>Easier initial deployment</span></label></div></li>
					<li><div class="test_radio"><input type="radio" name="test_select" id="test_select03"><label for="test_select03"><i>3</i><span>Reduced network latency</span></label></div></li>
					<li><div class="test_radio"><input type="radio" name="test_select" id="test_select04"><label for="test_select04"><i>4</i><span>Simpler debugging process</span></label></div></li>
				</ul>
			</article>
			<div class="btns_btm flex_center">
				<button type="button" class="btn btn_kwg prev" data-history-back>이전</button>
				<a href="{{ route('online_academy.end') }}" class="btn btn_woo2 next">다음</a>
			</div>
		</div>
		
	</div>
</section>

</main>
@endsection

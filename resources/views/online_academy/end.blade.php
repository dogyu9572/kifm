@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon online_academy_test" aria-labelledby="online-academy-end-heading">
	<h1 class="sound_only" id="online-academy-end-heading">학습 테스트 완료 페이지</h1>
	
	<div class="inner">
		
		<div class="test_page">
		
			<div class="test_end">
				<div class="tit">축하합니다.</div>
				<div class="gbox">
					<h2>100점</h2>
					<p>모든 문제를 맞히셨습니다. 이제 수강 완료 처리가 가능합니다.<br/>지금까지 고생하셨습니다.</p>
				</div>
			</div>
			
			<div class="btns_btm flex_center">
				<a href="{{ route('online_academy.view') }}" class="btn btn_kwg">강의로 돌아가기</a>
				<a href="{{ route('online_academy.test') }}" class="btn btn_wkk">다시 응시하기</a>
				<a href="{{ route('home.alt') }}" class="btn btn_woo2">수강 완료하기</a>
			</div>
		</div>
		
	</div>
</section>

</main>
@endsection

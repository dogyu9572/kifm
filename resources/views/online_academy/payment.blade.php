@extends('layouts.frontend')
@inject('onlineAcademy', 'App\Services\Frontend\PublicOnlineAcademyService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$topicItems = $onlineAcademy->topicList($course->topics);
	$keywordItems = $onlineAcademy->keywordList($course->keywords);
	$price = (int) ($pricing['price'] ?? 0);
	$isEligible = (bool) ($pricing['eligible'] ?? false);
	$isPending = $enrollment && $enrollment->payment_status !== 'completed';
@endphp
<main class="sub_area">

<section class="scon academic_event_view_detail training_course_view_wrap" aria-labelledby="online-academy-heading">
	<h1 class="sound_only" id="online-academy-heading">결제정보 페이지</h1>
	<div class="inbox inner">
		@if (session('success'))
			<p class="c_blue" role="status">{{ session('success') }}</p>
		@endif
		@error('course_id')
			<p class="c_red" role="alert">{{ $message }}</p>
		@enderror

		<div class="gbox start_box">
			<h2 class="btit">{{ $course->title }}</h2>
			@if ($onlineAcademy->professorText($course) !== '')
				<p>{{ $onlineAcademy->professorText($course) }}</p>
				<br>
			@endif
			<ul class="dots_list">
				<li>수강기간: {{ $onlineAcademy->periodText($course) }}</li>
				<li>수강 가능: {{ $pricing['grade_label'] ?? '회원' }}</li>
			</ul>
		</div>

		@if ($course->topic_detail || $topicItems !== [])
			<section class="academy_detail">
				<h2 class="btit">강의 주제</h2>
				<div class="con">
					@if ($course->topic_detail)
						<p>{{ $course->topic_detail }}</p>
					@endif
					@if ($topicItems !== [])
						<br>
						<ul class="dots_list">
							@foreach ($topicItems as $topic)
								<li>{{ $topic }}</li>
							@endforeach
						</ul>
					@endif
				</div>
			</section>
		@endif

		<section class="academy_detail">
			<h2 class="btit">강의 내용</h2>
			<div class="con">
				@if ($course->content)
					{!! $course->content !!}
				@else
					<p>{{ $onlineAcademy->summaryText($course) }}</p>
				@endif
			</div>
		</section>

		@if ($keywordItems !== [])
			<section class="academy_detail">
				<h2 class="btit">키워드</h2>
				<ul class="tags_area">
					@foreach ($keywordItems as $keyword)
						<li># {{ $keyword }}</li>
					@endforeach
				</ul>
			</section>
		@endif

		<article class="abso_application">
			<h2 class="tit">결제정보</h2>
			<dl class="price_info">
				<div>
					<dt>결제항목</dt>
					<dd><strong>수강료</strong></dd>
				</div>
				<div>
					<dt>결제금액</dt>
					<dd><strong class="c_iden">{{ number_format($price) }}</strong>원</dd>
				</div>
				<div class="total">
					<dt>최종 결제 금액</dt>
					<dd><strong>{{ number_format($price) }}</strong>원</dd>
				</div>
			</dl>

			@if (! $isEligible)
				<p class="c_red" role="alert">{{ $pricing['message'] ?? '신청할 수 없는 강좌입니다.' }}</p>
			@elseif ($isPending)
				<p class="c_red" role="status">이미 신청 접수된 강좌입니다. 입금 확인 후 수강이 가능합니다.</p>
			@endif

			<form method="POST" action="{{ route('online_academy.payment.store') }}">
				@csrf
				<input type="hidden" name="course_id" value="{{ $course->id }}">
				<div class="check_area checkbox" @if($isPending) hidden @endif>
					<input type="checkbox" name="terms_agree" id="terms_agree" value="1" required @checked(old('terms_agree'))>
					<label for="terms_agree"><i></i><span>[필수] 결제 이용 약관, 개인정보 처리 동의</span></label>
				</div>
				@error('terms_agree')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
				<button type="submit" class="btn_submit btn_wbb" @disabled(! $isEligible || $isPending)>지금 수강 신청하기</button>
				<button type="button" class="btn_cancel btn_kwg mt" data-history-back>돌아가기</button>
			</form>
		</article>
	</div>
</section>

</main>
@endsection

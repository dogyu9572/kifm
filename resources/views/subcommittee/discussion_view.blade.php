@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="subcommittee-heading">
	<div class="inner">
		<div class="sub_title">임상 영양 대사 연구회</div>
		
		<div class="subcommittee_cont_top"><a href="/subcommittee/" class="btn_back_box">돌아가기</a></div>
		
		<div class="subcommittee_list_top">
			<div class="imgfit"><img src="/images/bg_sample_subcommittee_list_top.jpg" alt=""></div>
			<p>안녕하세요. 홍길동 선생님</p>
			<h2><strong class="c_iden">임상 영양 및 대사 의학 연구회</strong>를 찾아주셔서 감사합니다.</h2>
		</div>
		
		<ul class="tabs full_line mb">
			<li class="{{ ($dNum ?? '') == '01' ? 'on' : '' }}"><a href="/subcommittee/notice" @if(($dNum ?? '') == '01') aria-current="page" @endif>공지사항</a></li>
			<li class="{{ ($dNum ?? '') == '02' ? 'on' : '' }}"><a href="/subcommittee/discussion" @if(($dNum ?? '') == '02') aria-current="page" @endif>토론장</a></li>
			<li class="{{ ($dNum ?? '') == '03' ? 'on' : '' }}"><a href="/subcommittee/archives" @if(($dNum ?? '') == '03') aria-current="page" @endif>자료실</a></li>
		</ul>
		
		<div class="board_view chat_wrap">
			<div class="tit_area">
				<h1 class="tit" id="subcommittee-heading">General Questions Will there be a quiz on week 4?</h1>
				<div class="date"><strong>등록일</strong><p>2026.03.01</p></div>
				<button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
			</div>
			<div class="chat_area">
				<div class="chat you">
					<div class="imgfit"><img src="/images/img_sample_profile.jpg" alt=""></div>
					<div class="name">
						<strong>Jin-ho Park</strong>
						<div class="date">2026.03.17  14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="gbox">
						Hello everyone! I'm having a bit of trouble understanding the Circuit Breaker transition from 'Half-Open' to 'Closed'.<br/>
						Is it based on a timer or a set number of successful requests?
					</div>
				</div>
				<div class="chat me">
					<div class="imgfit"><img src="/images/img_sample_profile.jpg" alt=""></div>
					<div class="name">
						<strong>(나) Kang-min Lee</strong>
						<div class="date">2026.03.17 14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="gbox">
						Hello everyone! I'm having a bit of trouble understanding the Circuit Breaker transition from 'Half-Open' to 'Closed'.<br/>
						Is it based on a timer or a set number of successful requests?
					</div>
				</div>
				<div class="chat you">
					<div class="imgfit"><img src="/images/img_sample_profile.jpg" alt=""></div>
					<div class="name">
						<strong>Jin-ho Park</strong>
						<div class="date">2026.03.17  14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="gbox">
						Hello everyone! I'm having a bit of trouble understanding the Circuit Breaker transition from 'Half-Open' to 'Closed'.<br/>
						Is it based on a timer or a set number of successful requests?
					</div>
				</div>
				<div class="chat me">
					<div class="imgfit"><img src="/images/img_sample_profile.jpg" alt=""></div>
					<div class="name">
						<strong>(나) Kang-min Lee</strong>
						<div class="date">2026.03.17 14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="gbox">
						Hello everyone! I'm having a bit of trouble understanding the Circuit Breaker transition from 'Half-Open' to 'Closed'.<br/>
						Is it based on a timer or a set number of successful requests?
					</div>
				</div>
			</div>
			<div class="chat_input">
				<input type="text" class="text" placeholder="답글 내용을 작성해주세요.">
				<button type="submit" class="btn btn_wbb">저장</button>
			</div>
		</div>
		
		<div class="board_bottom">
			<div class="btn_abso">
				<button type="button" class="btn i1">수정</button>
				<button type="button" class="btn i2">삭제</button>
			</div>
			<a href="/subcommittee/list" class="btn btn_wkk btn_list">목록</a>
		</div>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_bookmark.js') }}"></script>
<script>
// 채팅 목록 항상 맨 밑으로
	var $chatArea = $('.chat_area');
	$chatArea.scrollTop($chatArea[0].scrollHeight);
// 수정삭제열기
	$('.option_area .btn_option').click(function(e){
		e.stopPropagation();
		var $targetList = $(this).next('ul');
		$('.option_area ul').not($targetList).slideUp("fast");
		$targetList.stop(true, true).slideToggle("fast");
	});
</script>
@endpush
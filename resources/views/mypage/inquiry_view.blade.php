@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>
		
		<div class="board_view">
			<div class="tit_area">
				<div class="state end">답변완료</div>
				<!-- <div class="state before">답변 전</div> -->
				<h1 class="tit" id="society-notices-heading">문의내역 제목이 들어가는 공간입니다.</h1>
				<div class="date"><strong class="sound_only">등록일</strong><p>2026.03.01</p></div>
				<div class="btn_abso">
					<a href="" class="btn i1">수정</a>
					<a href="" class="btn i2">삭제</a>
				</div>
			</div>
			<div class="file_area">
				<a href="#this" download><strong>첨부파일이 들어가는 공간입니다.</strong><span>(110.5KB)</span><i class="btn_download flex_center">다운로드</i></a>
			</div>
			<div class="cont">
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.<br/>
				문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.문의 내용입니다.
			</div>
			<!-- 답변 전 -->
			<div class="gbox contact_info">
				<h2>문의하신 내용이 접수 되었습니다.</h2>
				<p>최대한 빠른 시일내에 답변드리겠습니다.</p>
			</div>
			<!-- //답변 전 -->
			<!-- 답변 후 -->
			<div class="reply_wrap">
				<div class="writer">
					<div class="imgfit"><img src="/images/img_logo_profile.jpg" alt="" aria-hidden="true"></div>
					<div class="txt">
						<div class="name"><strong class="c_iden">담당자</strong>의 답변</div>
						<div class="date">2026.03.01</div>
					</div>
				</div>
				<div class="con">
					답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.답변입니다<br/>
					답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.답변입니다.
				</div>
				<div class="file_area">
					<a href="#this" download><strong>첨부파일이 들어가는 공간입니다.</strong><span>(110.5KB)</span></a>
				</div>
			</div>
			<!-- //답변 후 -->
		</div>
		
		<div class="board_bottom">
			<a href="/mypage/inquiry" class="btn btn_wkk btn_list">목록</a>
		</div>
		
	</div>
</section>
	
</main>

@endsection
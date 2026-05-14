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
		
		<div class="board_view">
			<div class="tit_area">
				<h1 class="tit" id="society-notices-heading">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</h1>
				<div class="date"><strong class="sound_only">등록일</strong><p>2026.03.01</p></div>
				<button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
			</div>
			<div class="file_area">
				<a href="#this" download><strong>첨부파일이 들어가는 공간입니다.</strong><span>(110.5KB)</span><i class="btn_download flex_center">다운로드</i></a>
			</div>
			<div class="cont">
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.<br/>
				게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.게시글 내용입니다.
			</div>
			<div class="prev_next">
				<a href="#this" class="prev"><strong>이전 글</strong><p>이전 글이 없습니다.</p></a>
				<a href="#this" class="next"><strong>다음 글</strong><p>다음 글이 없습니다.</p></a>
			</div>
		</div>
		
		<div class="board_bottom">
			<a href="/subcommittee/notice" class="btn btn_wkk btn_list">목록</a>
		</div>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_bookmark.js') }}"></script>
@endpush
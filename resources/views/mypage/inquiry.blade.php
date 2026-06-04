@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="find-id-heading">
	<div class="inner">
		<h1 class="sub_title" id="find-id-heading">{{ $sName }}</h1>
		
		@include('mypage.mypage_tab')

		<div class="board_top">
			<div class="left">
				<select name="" id="" class="text">
					<option value="">전체보기</option>
				</select>
			</div>
			<div class="right flex">
				<select name="" id="" class="text">
					<option value="">전체</option>
					<option value="">제목</option>
					<option value="">내용</option>
				</select>
				<form class="search_area">
					<label for="event-search" class="sound_only">제목 검색</label>
					<input type="text" id="event-search" class="text" placeholder="제목을 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
				<a href="{{ route('mypage.inquiry_write') }}" class="btn btn_wkk btn_write">문의하기</a>
			</div>
		</div>
		
		<div class="board_list mo_break_list">
			<table>
				<caption>임상 영양 및 대사 의학 연구회 공지사항 입니다.</caption>
				<colgroup>
					<col class="num">
					<col>
					<col class="reply">
					<col class="date_slim">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO.</th>
						<th scope="col">제목</th>
						<th scope="col">답변</th>
						<th scope="col">등록일</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($posts as $post)
					<tr>
						<td class="num pc_vw">{{ $post->id }}</td>
						<td class="tal"><a href="{{ route('mypage.inquiry_view', ['id' => $post->id]) }}">{{ $post->title }}</a></td>
						<td class="reply"><span class="state {{ $post->reply_status_class }}">{{ $post->reply_status_label }}</span></td>
						<td class="date">{{ \Illuminate\Support\Carbon::parse($post->created_at)->format('Y.m.d') }}</td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="4">등록된 문의가 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<nav class="board-pagination" aria-label="게시판 페이지 이동">
			<a href="{{ route('mypage.inquiry_write') }}" class="btn_abso btn btn_wkk btn_write">문의하기</a>
			<x-frontend.pagination :paginator="$posts" embedded />
		</nav>
		
	</div>
</section>
	
</main>

@endsection
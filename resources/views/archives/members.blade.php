@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<h1 class="sub_title" id="society-notices-heading">{{ $sName }}</h1>

		<div class="board_top">
			<div class="left">
				<div class="total">Total <strong class="c_iden">{{ $posts->total() }}</strong></div>
			</div>
			<form method="GET" action="{{ route('archives.members') }}" class="right flex">
				<select name="search_type" class="text">
					<option value="all" @selected(request('search_type', 'all') === 'all')>전체</option>
					<option value="title" @selected(request('search_type') === 'title')>제목</option>
					<option value="content" @selected(request('search_type') === 'content')>내용</option>
				</select>
				<div class="search_area">
					<label for="archive-search" class="sound_only">자료실 검색</label>
					<input type="text" id="archive-search" name="keyword" class="text" value="{{ request('keyword') }}" placeholder="검색어를 입력해 주세요">
					<button type="submit" class="btn_search">검색</button>
				</div>
			</form>
		</div>

		<div class="board_list mo_break_list slim_board">
			<table>
				<caption>{{ $sName }} 게시글 목록입니다.</caption>
				<colgroup>
					<col class="num_slim">
					<col class="type">
					<col>
					<col class="date">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO.</th>
						<th scope="col">분류</th>
						<th scope="col">제목</th>
						<th scope="col">등록일</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($posts as $post)
						<tr class="{{ $post->is_notice ? 'notice' : '' }}">
							@if ($post->is_notice)
								<td class="num" aria-label="공지사항"></td>
							@else
								<td class="num">{{ $posts->total() - (($posts->currentPage() - 1) * $posts->perPage()) - $loop->index }}</td>
							@endif
							<td class="type">학회 자료</td>
							<td class="tal"><a href="{{ route('archives.members_show', $post->id) }}">{{ $post->title }}</a></td>
							<td class="date">{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="3" class="tac">등록된 게시글이 없습니다.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<x-frontend.pagination :paginator="$posts" />

	</div>
</section>

</main>

@endsection

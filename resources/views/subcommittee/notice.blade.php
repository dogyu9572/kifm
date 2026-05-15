@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="subcommittee-heading">
	<div class="inner">
		@include('subcommittee.partials.committee_header', ['useCommitteeH1' => true, 'showCommitteeTabs' => true])

		<div class="board_top">
			<div class="left">
				<div class="total">Total <strong class="c_iden">{{ $posts->total() }}</strong></div>
			</div>
			<form method="GET" action="{{ route('subcommittee.notice', $committee) }}" class="right flex">
				<select name="search_type" class="text">
					<option value="all" @selected(request('search_type', 'all') === 'all')>전체</option>
					<option value="title" @selected(request('search_type') === 'title')>제목</option>
					<option value="content" @selected(request('search_type') === 'content')>내용</option>
				</select>
				<div class="search_area">
					<label for="subcommittee-notice-search" class="sound_only">공지사항 검색</label>
					<input type="text" id="subcommittee-notice-search" name="keyword" class="text" value="{{ request('keyword') }}" placeholder="검색어를 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</div>
			</form>
		</div>

		<div class="board_list">
			<table>
				<caption>{{ $committee->name }} 공지사항 목록입니다.</caption>
				<colgroup>
					<col class="num">
					<col>
					<col class="date">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO.</th>
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
							<td class="tal"><a href="{{ route('subcommittee.notice_show', [$committee, $post->id]) }}">{{ $post->title }}</a></td>
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

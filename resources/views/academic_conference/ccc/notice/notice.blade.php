@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="academic-notices-heading">
	<div class="inner">
		<h1 class="sub_title" id="academic-notices-heading">{{ $sName }}</h1>

		<div class="board_top">
			<div class="left">
				<div class="total">Total <strong class="c_iden">{{ number_format($notices?->total() ?? 0) }}</strong></div>
			</div>
			<div class="right flex">
				<select name="search_type" id="notice-search-type" class="text" form="notice-search-form">
					<option value="all" @selected(request('search_type', 'all') === 'all')>전체</option>
					<option value="title" @selected(request('search_type') === 'title')>제목</option>
					<option value="content" @selected(request('search_type') === 'content')>내용</option>
				</select>
				<form method="GET" action="{{ $conferenceBaseUrl }}/notice" class="search_area" id="notice-search-form">
					<label for="event-search" class="sound_only">대회명 검색</label>
					<input type="text" id="event-search" name="keyword" class="text" value="{{ request('keyword') }}" placeholder="대회명을 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list mo_break_list slim_board">
			<table>
				<caption>임상 영양 및 대사 의학 연구회 공지사항 입니다.</caption>
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
					@forelse(($notices ?? collect()) as $post)
						<tr @if($post->is_notice)class="notice"@endif>
							<td class="num">
								@if($post->is_notice)
									공지
								@else
									{{ ($notices->total() - (($notices->currentPage() - 1) * $notices->perPage()) - $loop->index) }}
								@endif
							</td>
							<td class="tal"><a href="{{ $conferenceBaseUrl }}/notice/view?id={{ $post->id }}">{{ $post->title }}</a></td>
							<td class="date">{{ \Illuminate\Support\Carbon::parse($post->created_at)->format('Y.m.d') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="3">등록된 공지사항이 없습니다.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if($notices)
			<x-frontend.pagination :paginator="$notices" />
		@endif
		
	</div>
</section>
	
</main>

@endsection

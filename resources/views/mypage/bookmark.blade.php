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
				<form method="GET" action="{{ route('mypage.bookmark') }}">
					<select name="content_type" class="text" data-bookmark-content-type-filter>
						<option value="all" @selected(($filterContentType ?? 'all') === 'all')>전체보기</option>
						@foreach ($contentTypeOptions as $contentType)
						<option value="{{ $contentType['value'] }}" @selected(($filterContentType ?? 'all') === $contentType['value'])>{{ $contentType['label'] }}</option>
						@endforeach
					</select>
					@if (! empty($filterKeyword))
					<input type="hidden" name="keyword" value="{{ $filterKeyword }}">
					@endif
				</form>
			</div>
			<div class="right flex">
				<select name="" id="" class="text" disabled>
					<option value="">전체</option>
					<option value="">제목</option>
					<option value="">내용</option>
				</select>
				<form method="GET" action="{{ route('mypage.bookmark') }}" class="search_area">
					<label for="event-search" class="sound_only">검색어 검색</label>
					@if (($filterContentType ?? 'all') !== 'all')
					<input type="hidden" name="content_type" value="{{ $filterContentType }}">
					@endif
					<input type="text" id="event-search" name="keyword" class="text" placeholder="검색어를 입력해주세요" value="{{ $filterKeyword }}">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list mo_break_list" data-mypage-bookmark data-destroy-url="{{ route('mypage.bookmark.destroy') }}">
			<table>
				<caption>임상 영양 및 대사 의학 연구회 공지사항 입니다.</caption>
				<colgroup>
					<col class="page">
					<col>
					<col class="date_slim">
					<col class="bookmark_box">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">메뉴명</th>
						<th scope="col">제목</th>
						<th scope="col">등록일</th>
						<th scope="col">북마크</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($bookmarks as $bookmark)
					<tr>
						<td class="page">{{ $bookmark->display_menu_label }}</td>
						<td class="tal">
							@if ($bookmark->snapshot_url)
							<a href="{{ $bookmark->snapshot_url }}">{{ $bookmark->snapshot_title ?? '-' }}</a>
							@else
							{{ $bookmark->snapshot_title ?? '-' }}
							@endif
						</td>
						<td class="date">{{ optional($bookmark->bookmarked_at)->format('Y.m.d') }}</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="북마크 해제" data-bookmark-id="{{ $bookmark->id }}"></button></td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="4">북마크한 콘텐츠가 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<x-frontend.pagination :paginator="$bookmarks" />
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/mypage-bookmark.js') }}"></script>
@endpush

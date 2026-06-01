@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="journals-heading">
	<div class="inner">
		<h1 class="sub_title" id="journals-heading">{{ $sName }}</h1>

		<div class="board_top">
			<div class="left">
				<div class="total">Total <strong class="c_iden">{{ $posts->total() }}</strong></div>
			</div>
			<form method="GET" action="{{ route('archives.journals') }}" class="right flex">
				<select name="search_type" class="text">
					<option value="all" @selected(request('search_type', 'all') === 'all')>전체</option>
					<option value="title" @selected(request('search_type') === 'title')>제목</option>
				</select>
				<div class="search_area">
					<label for="journal-search" class="sound_only">학술지 검색</label>
					<input type="text" id="journal-search" name="keyword" class="text" value="{{ request('keyword') }}" placeholder="검색어를 입력해 주세요">
					<button type="submit" class="btn_search">검색</button>
				</div>
			</form>
		</div>

		<div class="board_list">
			<table>
				<caption>{{ $sName }} 목록입니다.</caption>
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
						@php
							$customFields = is_string($post->custom_fields ?? null)
								? (json_decode($post->custom_fields, true) ?: [])
								: ($post->custom_fields ?? []);
							$linkUrl = $customFields['link_url'] ?? '#';
						@endphp
						<tr>
							<td class="num">{{ $posts->total() - (($posts->currentPage() - 1) * $posts->perPage()) - $loop->index }}</td>
							<td class="tal"><a href="{{ $linkUrl }}" target="_blank" rel="noopener">{{ $post->title }}</a></td>
							<td class="date">{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="3" class="tac">등록된 학술지가 없습니다.</td>
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

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
			<form method="GET" action="{{ route('member_plaza.society_album') }}" class="right flex">
				<select name="search_type" class="text">
					<option value="all" @selected(request('search_type', 'all') === 'all')>전체</option>
					<option value="title" @selected(request('search_type') === 'title')>제목</option>
					<option value="content" @selected(request('search_type') === 'content')>내용</option>
				</select>
				<div class="search_area">
					<label for="board-search" class="sound_only">학회 앨범 검색</label>
					<input type="text" id="board-search" name="keyword" class="text" value="{{ request('keyword') }}" placeholder="검색어를 입력해 주세요">
					<button type="submit" class="btn_search">검색</button>
				</div>
			</form>
		</div>

		<ul class="gallery_list">
			@forelse ($posts as $post)
				@php
					$thumbnail = ! empty($post->thumbnail)
						? asset('storage/'.$post->thumbnail)
						: asset('images/img_sample_society_album.jpg');
				@endphp
				<li>
					<article>
						<a href="{{ route('member_plaza.society_album_show', $post->id) }}" class="gallery_item">
							<i class="hover_obj" aria-hidden="true"></i>
							<div class="img_area" aria-hidden="true"><img src="{{ $thumbnail }}" alt=""></div>
							<div class="txt_area">
								<h2 class="title">{{ $post->title }}</h2>
								<time class="date" datetime="{{ \Carbon\Carbon::parse($post->created_at)->format('Y-m-d') }}">{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d') }}</time>
							</div>
						</a>
					</article>
				</li>
			@empty
				<li class="empty">등록된 게시글이 없습니다.</li>
			@endforelse
		</ul>

		<x-frontend.pagination :paginator="$posts" />

	</div>
</section>

</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.gallery_list .hover_obj').each(function() {
        $(this).html(`
            <svg class="border-svg" width="100%" height="100%" style="overflow:visible;">
                <defs>
                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#0088B8" />
                        <stop offset="100%" style="stop-color:#E60012" />
                    </linearGradient>
                </defs>
                <rect x="1" y="1" width="calc(100% - 2px)" height="calc(100% - 2px)" rx="8" ry="8" fill="none" stroke="url(#grad1)" stroke-width="2" stroke-linecap="round" />
            </svg>
        `);
    });
});
</script>
@endpush

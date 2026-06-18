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
				<div class="total">Total <strong class="c_iden">100</strong></div>
			</div>
			<div class="right flex">
				<select name="" id="" class="text">
					<option value="">전체</option>
					<option value="">제목</option>
					<option value="">내용</option>
				</select>
				<form class="search_area">
					<label for="event-search" class="sound_only">대회명 검색</label>
					<input type="text" id="event-search" class="text" placeholder="대회명을 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<ul class="gallery_list">
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="/general_page/news/media_events_view" class="gallery_item">
						<i class="hover_obj" aria-hidden="true"></i>
						<div class="img_area" aria-hidden="true"><img src="/images/img_sample_society_album.jpg" alt=""></div>
						<div class="txt_area">
							<h2 class="title">제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. 제목입니다. </h2>
							<time class="date" datetime="2025-02-16">2025.02.16</time>
						</div>
					</a>
				</article>
			</li>
		</ul>

		<nav class="board-pagination" aria-label="게시판 페이지 이동">
			<ul class="pagination">
				<li class="page-item arw_item"><a class="page-link" href="#" title="첫 페이지" aria-label="첫 페이지로 이동"><i class="arrow two first" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="이전 페이지" aria-label="이전 페이지로 이동"><i class="arrow one prev" aria-hidden="true"></i></a></li>
				<li class="page-item active"><span class="page-link" aria-current="page" aria-label="현재 페이지 1">1</span></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="2페이지로 이동">2</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="3페이지로 이동">3</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="4페이지로 이동">4</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="5페이지로 이동">5</a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="다음 페이지" aria-label="다음 페이지로 이동"><i class="arrow one next" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="마지막 페이지" aria-label="마지막 페이지로 이동"><i class="arrow two last" aria-hidden="true"></i></a></li>
			</ul>
		</nav>
		
	</div>
</section>

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

</main>

@endsection
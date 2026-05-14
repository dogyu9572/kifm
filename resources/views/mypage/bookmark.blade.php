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
					<label for="event-search" class="sound_only">검색어 검색</label>
					<input type="text" id="event-search" class="text" placeholder="검색어를 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list">
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
					<tr>
						<td class="page">공지사항</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">학술자료실</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
					<tr>
						<td class="page">자유게시판</td>
						<td class="tal"><a href="#this">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
						<td class="bookmark_box"><button type="button" class="bookmark on" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button></td>
					</tr>
				</tbody>
			</table>
		</div>

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
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_bookmark.js') }}"></script>
@endpush
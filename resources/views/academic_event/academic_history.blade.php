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
					<label for="event-search" class="sound_only">제목</label>
					<input type="text" id="event-search" class="text" placeholder="행사명을 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list board_bold">
			<table>
				<caption>학술대회 연혁입니다.</caption>
				<colgroup>
					<col class="dates">
					<col>
					<col class="down">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">행사 기간</th>
						<th scope="col">행사명</th>
						<th scope="col">행사자료</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
					</tr>
					<tr>
						<td class="dates">2026-03-15 ~ 2026-03-20</td>
						<td class="tac">제 24회 정기 춘계 학술대회: 인공지능과 현대의학의 만남</td>
						<td class="down"><a href="#this" class="btn btn_gwg btn_download">자료집</a></td>
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
@endpush

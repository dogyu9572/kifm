@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="subcommittee-heading">
	<div class="inner">
		<h1 class="sub_title" id="subcommittee-heading">임상 영양 대사 연구회</h1>
		
		<div class="subcommittee_cont_top"><a href="/subcommittee/" class="btn_back_box">돌아가기</a></div>
		
		<div class="subcommittee_list_top">
			<div class="imgfit"><img src="/images/bg_sample_subcommittee_list_top.jpg" alt=""></div>
			<p>안녕하세요. 홍길동 선생님</p>
			<h2><strong class="c_iden">임상 영양 및 대사 의학 연구회</strong>를 찾아주셔서 감사합니다.</h2>
		</div>
		
		<ul class="tabs full_line mb">
			<li class="{{ ($dNum ?? '') == '01' ? 'on' : '' }}"><a href="/subcommittee/notice" @if(($dNum ?? '') == '01') aria-current="page" @endif>공지사항</a></li>
			<li class="{{ ($dNum ?? '') == '02' ? 'on' : '' }}"><a href="/subcommittee/discussion" @if(($dNum ?? '') == '02') aria-current="page" @endif>토론장</a></li>
			<li class="{{ ($dNum ?? '') == '03' ? 'on' : '' }}"><a href="/subcommittee/archives" @if(($dNum ?? '') == '03') aria-current="page" @endif>자료실</a></li>
		</ul>
		
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
		
		<div class="board_list">
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
					<tr>
						<td class="num">10</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다. 제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">9</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">8</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">7</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">6</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">5</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">4</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">3</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">2</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">1</td>
						<td class="tal"><a href="/subcommittee/discussion/view">제목이 위치할 공간입니다. 제목이 위치할 공간입니다.제목이 위치할 공간입니다.</a></td>
						<td class="date">2025.01.01</td>
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
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
			<div class="left" aria-hidden="true">&nbsp;</div>
			<div class="right flex">
				<form class="search_area">
					<label for="event-search" class="sound_only">직책 검색</label>
					<input type="text" id="event-search" class="text" placeholder="직책으로 검색하세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list slide4 td_height">
			<table>
				<caption>활동중인 임원 회원 목록입니다.</caption>
				<thead>
					<tr>
						<th scope="col">직책</th>
						<th scope="col">임기</th>
						<th scope="col">상태</th>
						<th scope="col">출력</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>학술이사</td>
						<td>2022.01.01 ~ 2023.12.31</td>
						<td>재직중</td>
						<td><a href="/mypage/print_letter_appointment" class="btn btn_kwk" target="_blank">임명장 출력</a></td>
					</tr>
					<tr>
						<td>학술이사</td>
						<td>2022.01.01 ~ 2023.12.31</td>
						<td>재직중</td>
						<td><a href="/mypage/print_letter_appointment" class="btn btn_kwk" target="_blank">임명장 출력</a></td>
					</tr>
					<tr>
						<td>학술이사</td>
						<td>2022.01.01 ~ 2023.12.31</td>
						<td>재직중</td>
						<td><a href="/mypage/print_letter_appointment" class="btn btn_kwk" target="_blank">임명장 출력</a></td>
					</tr>
					<tr>
						<td>학술이사</td>
						<td>2022.01.01 ~ 2023.12.31</td>
						<td>재직중</td>
						<td><a href="/mypage/print_letter_appointment" class="btn btn_kwk" target="_blank">임명장 출력</a></td>
					</tr>
					<tr>
						<td>학술이사</td>
						<td>2022.01.01 ~ 2023.12.31</td>
						<td>재직중</td>
						<td><a href="/mypage/print_letter_appointment" class="btn btn_kwk" target="_blank">임명장 출력</a></td>
					</tr>
				</tbody>
			</table>
		</div>
		
	</div>
</section>
	
</main>

@endsection
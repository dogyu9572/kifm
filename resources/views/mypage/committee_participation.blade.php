@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="committee-participation-heading">
	<div class="inner">
		<h1 class="sub_title" id="committee-participation-heading">{{ $sName }}</h1>
		
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
		
		<div class="board_list slide5 td_height">
			<table>
				<caption>참여중인 위원회 목록입니다.</caption>
				<thead>
					<tr>
						<th scope="col">직책</th>
						<th scope="col">구분</th>
						<th scope="col">신청 일자</th>
						<th scope="col">상태</th>
						<th scope="col">확인</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($committees as $committee)
					<tr>
						<td>{{ $committee->name }}</td>
						<td>위원</td>
						<td>-</td>
						<td>참여 중</td>
						<td><a href="{{ route('subcommittee.notice', ['committee' => $committee->id]) }}" class="btn btn_kwk">위원회 바로가기</a></td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="5">참여 중인 위원회가 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		
	</div>
</section>

</main>

@endsection

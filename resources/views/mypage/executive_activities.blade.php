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
				<form class="search_area" method="GET" action="{{ route('mypage.executive_activities') }}">
					<label for="event-search" class="sound_only">직책 검색</label>
					<input type="text" id="event-search" name="keyword" class="text" placeholder="직책으로 검색하세요" value="{{ request('keyword') }}">
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
					@forelse ($executives as $executive)
					@php
						$term = $executive->term_start_date?->format('Y.m.d');
						if ($executive->is_indefinite) {
							$termEnd = '무기한';
						} elseif ($executive->term_end_date) {
							$termEnd = $executive->term_end_date->format('Y.m.d');
						} else {
							$termEnd = '-';
						}
						$statusLabel = $executive->termStatusLabel() === '임기중' ? '재직중' : $executive->termStatusLabel();
					@endphp
					<tr>
						<td>{{ $roleLabels[$executive->executive_role] ?? $executive->executive_role }}</td>
						<td>{{ $term }} ~ {{ $termEnd }}</td>
						<td>{{ $statusLabel }}</td>
						<td><a href="{{ route('mypage.print_letter_appointment', ['executive_id' => $executive->id]) }}" class="btn btn_kwk" target="_blank">임명장 출력</a></td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="4">임원 활동 내역이 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		
	</div>
</section>
	
</main>

@endsection
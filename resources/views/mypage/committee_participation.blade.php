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
						<td>2022.01.01</td>
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

<div class="popup pop_account" id="pop_cancel">
	<div class="dm" onclick="layerHide('pop_cancel');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_cancel');">Close</button>
		<div class="ptit">신청 취소 안내</div>
		<div class="con">
			<div class="gbox">정말로 해당 위원회 신청을 취소하시겠습니까?<br>취소 후에는 복구가 불가능하며, <br>다시 신청 절차를 거쳐야 합니다.</div>
		</div>
		<div class="btns flex_center">
			<button type="button" class="btn btn_wkk" onclick="layerHide('pop_cancel');">닫기</button>
			<button type="button" class="btn btn_kwg">신청 취소</button>
		</div>
	</div>
</div>

<div class="popup pop_account" id="pop_rejection">
	<div class="dm" onclick="layerHide('pop_rejection');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_rejection');">Close</button>
		<div class="ptit">반려 사유 확인</div>
		<div class="con">
			<div class="gbox">최근 2년 이내의 학술 활동 실적 증빙 서류가 미비하여 <br>본 학회 위원 자격 요건을 충족하지 못하였습니다. <br>증빙 서류 보완 후 다시 신청해 주시기 바랍니다.</div>
		</div>
		<div class="btns flex_center">
			<button type="button" class="btn btn_wkk" onclick="layerHide('pop_rejection');">닫기</button>
		</div>
	</div>
</div>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_popup.js') }}"></script>
@endpush
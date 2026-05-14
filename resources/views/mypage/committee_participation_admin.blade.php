@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="committee-participation-admin-heading">
	<div class="inner">
		<h1 class="sub_title" id="committee-participation-admin-heading">{{ $sName }}</h1>
		
		@include('mypage.mypage_tab')
		
		<div class="committee_participation_admin_top">
			<h2 class="sound_only">위원회 참여 현황 통계</h2>
			<dl class="status_list">
				<div class="status_item">
					<dt>전체 인원</dt>
					<dd class="count"><strong class="c_iden">490</strong> / 500<span class="sound_only">명</span></dd>
				</div>
				<div class="status_item">
					<dt>전체 신청</dt>
					<dd class="count"><strong>20</strong><span class="sound_only">건</span></dd>
				</div>
				<div class="status_item">
					<dt>검토 대기</dt>
					<dd class="count"><strong>500</strong><span class="sound_only">건</span></dd>
				</div>
				<div class="status_item">
					<dt>승인 완료</dt>
					<dd class="count"><strong>490</strong><span class="sound_only">건</span></dd>
				</div>
				<div class="status_item">
					<dt>반려 건수</dt>
					<dd class="count"><strong>120</strong><span class="sound_only">건</span></dd>
				</div>
			</dl>
		</div>

		<div class="board_top">
			<div class="left">
				<select name="" id="" class="text">
					<option value="">전체보기</option>
				</select>
			</div>
			<div class="right flex">
				<form class="search_area">
					<label for="event-search" class="sound_only">직책 검색</label>
					<input type="text" id="event-search" class="text" placeholder="이름 또는 이메일 검색">
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
					<tr>
						<td>홍길동</td>
						<td>korea@naver.com</td>
						<td>2022.01.01</td>
						<td>참여 중</td>
						<td>
							<button type="button" class="btn btn_kwk">가입 승인</button>
							<button type="button" class="btn btn_rwr" onclick="layerShow('pop_rejection_act');">반려</button>
						</td>
					</tr>
					<tr>
						<td>홍길동</td>
						<td>korea@naver.com</td>
						<td>2022.01.01</td>
						<td>승인 대기</td>
						<td>
							<button type="button" class="btn btn_kwk">가입 승인</button>
							<button type="button" class="btn btn_rwr" onclick="layerShow('pop_rejection_act');">반려</button>
						</td>
					</tr>
					<tr>
						<td>홍길동</td>
						<td>korea@naver.com</td>
						<td>2022.01.01</td>
						<td>참여 중</td>
						<td>
							<button type="button" class="btn btn_kwk">가입 승인</button>
							<button type="button" class="btn btn_rwr" onclick="layerShow('pop_rejection_act');">반려</button>
						</td>
					</tr>
					<tr>
						<td>홍길동</td>
						<td>korea@naver.com</td>
						<td>2022.01.01</td>
						<td>반려</td>
						<td><button type="button" class="btn btn_kwk" onclick="layerShow('pop_rejection');">반려 사유 확인</button></td>
					</tr>
				</tbody>
			</table>
		</div>
		
	</div>
</section>

<div class="popup pop_account" id="pop_rejection_act">
	<div class="dm" onclick="layerHide('pop_rejection_act');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_rejection_act');">Close</button>
		<div class="ptit">반려 사유 입력</div>
		<div class="con">
			<div class="gbox">홍길동 님의 신청을 반려합니다.<br/>신청자에게 전달될 사유를 입력해 주세요.</div>
			<div class="tit">반려 사유</div>
			<textarea name="" id="" cols="30" rows="10" class="text w100p" placeholder="신청자에게 노출될 구체적인 사유를 입력해 주세요."></textarea>
		</div>
		<div class="btns flex_center">
			<button type="button" class="btn btn_wkk" onclick="layerHide('pop_rejection_act');">취소</button>
			<button type="button" class="btn btn_kwg">반려 처리</button>
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
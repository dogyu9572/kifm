@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon participation_history_wrap" aria-labelledby="participation-history-heading">
	<div class="inner">
		<h1 class="sub_title" id="participation-history-heading">{{ $sName }}</h1>
		
		@include('mypage.mypage_tab')

		<div class="board_top">
			<div class="left">
				<select name="" id="" class="text">
					<option value="">전체보기</option>
				</select>
			</div>
			<div class="right flex">
				<form class="search_area">
					<label for="event-search" class="sound_only">강의명 검색</label>
					<input type="text" id="event-search" class="text" placeholder="강의명으로 검색해 주세요.">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list tac">
			<table>
				<caption>임상 영양 및 대사 의학 연구회 공지사항 입니다.</caption>
				<colgroup>
					<col>
					<col class="online80">
					<col class="online240">
					<col class="online160">
					<col class="online160">
					<col class="online160">
					<col class="online160">
					<col class="online160">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">강의명</th>
						<th scope="col">평점</th>
						<th scope="col">수강 기간</th>
						<th scope="col">상태</th>
						<th scope="col">수료증</th>
						<th scope="col">영수증 출력</th>
						<th scope="col">강의보기</th>
						<th scope="col">신청 내역 보기</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>2025년 대한기능의학회 추계학술대회</td>
						<td>10점</td>
						<td>2026.03.01 ~ 2026.05.31</td>
						<td>수강 완료</td>
						<td><a href="/mypage/print_completion" class="btn btn_kwk" target="_blank">이수증</a></td>
						<td><a href="/mypage/print_receipt_save" class="btn btn_kwk" target="_blank">영수증 출력</a></td>
						<td><a href="#this" class="btn btn_kwk">강의보기</a></td>
						<td><a href="/mypage/online_training/view" type="button" class="btn btn_kwk">신청 내역 보기</a></td>
					</tr>
					<tr>
						<td>2025년 대한기능의학회 추계학술대회</td>
						<td>10점</td>
						<td>2026.03.01 ~ 2026.05.31</td>
						<td>수강 가능</td>
						<td>-</td>
						<td><a href="/mypage/print_receipt_save" class="btn btn_kwk" target="_blank">영수증 출력</a></td>
						<td><a href="#this" class="btn btn_kwk">강의보기</a></td>
						<td><a href="/mypage/online_training/view" type="button" class="btn btn_kwk">신청 내역 보기</a></td>
					</tr>
					<tr>
						<td>2025년 대한기능의학회 추계학술대회</td>
						<td>10점</td>
						<td>-</td>
						<td>결제 대기<br/><button type="button" class="btn_un c_iden" onclick="layerShow('pop_bank');">입금 계좌번호 보기</button></td>
						<td>-</td>
						<td>-</td>
						<td><a href="#this" class="btn btn_kwk">강의보기</a></td>
						<td><a href="/mypage/online_training/view" type="button" class="btn btn_kwk">신청 내역 보기</a>
							<button type="button" class="btn btn_rwr" onclick="layerShow('pop_cancel');">신청 취소</button>
						</td>
					</tr>
					<tr>
						<td>2025년 대한기능의학회 추계학술대회</td>
						<td>10점</td>
						<td>-</td>
						<td><span class="c_red">기간 만료<br/>(수강 완료)</span></td>
						<td><a href="/mypage/print_completion" class="btn btn_kwk" target="_blank">이수증</a></td>
						<td><a href="/mypage/print_receipt_save" class="btn btn_kwk" target="_blank">영수증 출력</a></td>
						<td><button type="button" class="btn btn_rrr">재결제</button></td>
						<td><a href="/mypage/online_training/view" type="button" class="btn btn_kwk">신청 내역 보기</a></td>
					</tr>
					<tr>
						<td>2025년 대한기능의학회 추계학술대회</td>
						<td>10점</td>
						<td>-</td>
						<td><span class="c_red">기간 만료<br/>(수강 완료)</span></td>
						<td>-</td>
						<td><a href="/mypage/print_receipt_save" class="btn btn_kwk" target="_blank">영수증 출력</a></td>
						<td><button type="button" class="btn btn_rrr">재결제</button></td>
						<td><a href="/mypage/online_training/view" type="button" class="btn btn_kwk">신청 내역 보기</a></td>
					</tr>
					<!-- 내역이 없을 경우 -->
					<!-- <tr class="empty">
						<td colspan="8">수강 내역이 없습니다.</td>
					</tr> -->
				</tbody>
			</table>
		</div>
		
		<div class="gbox excl_wrap">
			<div class="tt excl">온라인 아카데미 안내</div>
			<ul class="dots_list">
				<li>수강 완료: 모든 강의를 100% 시청한 상태이며 이수증(수료증) 출력이 가능합니다.</li>
				<li>기간 만료: 수강 가능 기간이 종료되었습니다. 학습이 끝나지 않은 경우 '재결제'를 통해 기간을 연장할 수 있습니다.</li>
				<li>개인 사정으로 인해 부득이하게 기간 내 수강을 완료하지 못한 경우, 사무국(02-1234-5678)으로 유선 문의 주시면 예외 규정에 따라 연장 검토가 가능합니다.</li>
				<li>이수증은 학습 기간 종료 후에도 '나의 강의실'에서 언제든지 재출력할 수 있습니다.</li>
			</ul>
		</div>
		
	</div>
</section>

<div class="popup pop_account" id="pop_bank">
	<div class="dm" onclick="layerHide('pop_bank');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_bank');">Close</button>
		<div class="ptit">입금계좌확인</div>
		<div class="con">
			<div class="gbox">사무국에서 온라인 입금 확인 후 납부 처리를 완료합니다.</div>
			<div class="payment">
				<dl>
					<div>
						<dt>결제 수단</dt>
						<dd>무통장 입금</dd>
					</div>
					<div>
						<dt>환불 받으실 계좌</dt>
						<dd>
							<p>홍길동</p>
							<p>국민은행</p>
							<p>111111-22-333333</p>
						</dd>
					</div>
				</dl>
			</div>
		</div>
	</div>
</div>

<div class="popup pop_account" id="pop_cancel">
	<div class="dm" onclick="layerHide('pop_cancel');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_cancel');">Close</button>
		<div class="ptit">신청 취소</div>
		<div class="con">
			<div class="gbox">
				신청을 취소하실 경우, 기존 신청 내용은 모두 삭제됩니다.
				<p class="c_iden">*무통장 입금의 경우 영업일 기준 2~3일내로 환불됩니다.</p>
			</div>
			<div class="payment">
				<dl>
					<div>
						<dt>결제 수단</dt>
						<dd>무통장 입금</dd>
					</div>
					<div>
						<dt>환불 받으실 계좌</dt>
						<dd>
							<p>홍길동</p>
							<p>국민은행</p>
							<p>111111-22-333333</p>
						</dd>
					</div>
				</dl>
			</div>
		</div>
		<div class="btns flex_center">
			<button type="button" class="btn btn_wkk" onclick="layerHide('pop_cancel');">닫기</button>
			<button type="button" class="btn btn_kwg" id="btnCancel">신청 취소</button>
		</div>
	</div>
</div>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_popup.js') }}"></script>
<script>
//신청 취소
	$('#btnCancel').on('click', function(e) {
        e.preventDefault();
        if (confirm("정말로 신청을 취소하시겠습니까?")) {
            alert("신청취소가 완료되었습니다.");
            // $(this).closest('form').submit();
            // location.href = '/logout';
			$(".popup").fadeOut("fast");
        }
    });
</script>
@endpush
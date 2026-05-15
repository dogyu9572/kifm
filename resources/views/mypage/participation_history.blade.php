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
				<div class="total">Total <strong class="c_iden">{{ $registrations->total() }}</strong></div>
			</div>
			<div class="right flex">
				<form method="GET" action="{{ route('mypage.participation_history') }}" class="flex">
					<select name="year" class="text">
						<option value="">연도</option>
						@for ($y = (int) now()->format('Y'); $y >= 2010; $y--)
						<option value="{{ $y }}" @selected((string) $filterYear === (string) $y)>{{ $y }}년</option>
						@endfor
					</select>
					<select name="month" class="text">
						<option value="">월</option>
						@for ($m = 1; $m <= 12; $m++)
						<option value="{{ $m }}" @selected((string) $filterMonth === (string) $m)>{{ str_pad((string) $m, 2, '0', STR_PAD_LEFT) }}월</option>
						@endfor
					</select>
					<button type="submit" class="btn_search_solo">검색</button>
				</form>
			</div>
		</div>
		
		<div class="board_list tac">
			<table>
				<caption>임상 영양 및 대사 의학 연구회 공지사항 입니다.</caption>
				<colgroup>
					<col>
					<col class="part_his134">
					<col class="part_his134">
					<col class="part_his134">
					<col class="part_his180">
					<col class="part_his134">
					<col class="part_his134">
					<col class="part_his134">
					<col class="part_his134">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">행사명</th>
						<th scope="col">평점</th>
						<th scope="col">결제 금액</th>
						<th scope="col">결제 방법</th>
						<th scope="col">신청일</th>
						<th scope="col">신청 상태</th>
						<th scope="col">참가증명서</th>
						<th scope="col">영수증 출력</th>
						<th scope="col">신청 내역 보기</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($registrations as $row)
					@php
						$statusLabel = $paymentStatusLabels[$row->payment_status] ?? $row->payment_status;
						$methodLabel = $paymentMethodLabels[$row->payment_method] ?? $row->payment_method;
						$isCompleted = $row->payment_status === 'completed';
					@endphp
					<tr>
						<td>{{ $row->event?->title ?? '-' }}</td>
						<td>-</td>
						<td>{{ number_format((int) $row->total_amount) }}원</td>
						<td>{{ $methodLabel }}</td>
						<td>
							@if ($row->registered_at)
								{{ $row->registered_at->format('Y.m.d') }}
							@endif
							@if ($row->paid_at)
								<br/>(결제일: {{ $row->paid_at->format('Y.m.d') }})
							@endif
						</td>
						<td>{{ $statusLabel }}</td>
						<td>
							@if ($isCompleted)
								<a href="{{ route('mypage.print_participation', ['registration_id' => $row->id]) }}" class="btn btn_kwk" target="_blank">참가 증명서</a>
							@else
								-
							@endif
						</td>
						<td>
							@if ($isCompleted)
								<a href="{{ route('mypage.print_receipt_save', ['registration_id' => $row->id]) }}" class="btn btn_kwk" target="_blank">영수증 출력</a>
							@else
								-
							@endif
						</td>
						<td>
							<a href="{{ route('mypage.participation_history_view', ['id' => $row->id]) }}" class="btn btn_kwk">신청 내역 보기</a>
						</td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="9">신청하신 내역이 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		
		<div class="gbox excl_wrap">
			<div class="tt excl">꼭 확인해 주세요!</div>
			<ul class="dots_list">
				<li>참가 신청 취소는 학술대회 당일 전날까지 가능합니다.</li>
				<li>사전등록 마감 전일까지는 수수료를 제외한 전액 환불이 가능합니다.</li>
				<li>영수증은 결제 완료 시, 참가증명서는 행사일 다음 날부터 출력이 가능합니다.</li>
				<li>무통장 입금은 관리자 확인 후 상태가 변경됩니다. 확인까지 영업일 기준 1~2일이 소요될 수 있습니다.</li>
				<li>'미등록' 상태이거나 로그인이 안 될 경우, 사무국으로 문의해 주시기 바랍니다.</li>
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
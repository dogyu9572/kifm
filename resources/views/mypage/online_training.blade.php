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
			<form method="GET" action="{{ route('mypage.online_training') }}" class="left">
				<select name="status" class="text">
					<option value="all" @selected(($filterStatus ?? 'all') === 'all')>전체보기</option>
					@foreach ($statusLabels as $status => $label)
					<option value="{{ $status }}" @selected(($filterStatus ?? 'all') === $status)>{{ $label }}</option>
					@endforeach
				</select>
				@if (! empty($filterYear))
				<input type="hidden" name="year" value="{{ $filterYear }}">
				@endif
				@if (! empty($filterKeyword))
				<input type="hidden" name="keyword" value="{{ $filterKeyword }}">
				@endif
				<button type="submit" class="btn_search_solo">검색</button>
			</form>
			<form method="GET" action="{{ route('mypage.online_training') }}" class="right flex">
				<label for="event-search" class="sound_only">강의명 검색</label>
				<select name="year" class="text">
					<option value="">연도</option>
					@for ($y = (int) now()->format('Y'); $y >= 2010; $y--)
					<option value="{{ $y }}" @selected((string) $filterYear === (string) $y)>{{ $y }}년</option>
					@endfor
				</select>
				@if (($filterStatus ?? 'all') !== 'all')
				<input type="hidden" name="status" value="{{ $filterStatus }}">
				@endif
				<div class="search_area">
					<input type="text" id="event-search" name="keyword" class="text" placeholder="강의명으로 검색해 주세요." value="{{ $filterKeyword }}">
					<button type="submit" class="btn_search">검색</button>
				</div>
			</form>
		</div>

		<div class="board_list tac tbl_break_list">
			<table>
				<caption>온라인 교육 수강내역입니다.</caption>
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
					@forelse ($enrollments as $enrollment)
					@php
						$isCompleted = $enrollment->enrollment_status === 'completed';
						$isExpired = $enrollment->enrollment_status === 'expired';
						$isPaymentCompleted = in_array($enrollment->payment_status, ['completed', 'paid'], true);
						$statusLabel = $statusLabels[$enrollment->enrollment_status] ?? $enrollment->enrollment_status;
						$periodStart = $enrollment->applied_at;
						$periodEnd = $enrollment->expire_at ?: $enrollment->course?->period_end;
					@endphp
					<tr>
						<td class="online1">{{ $enrollment->course?->title ?? '-' }}</td>
						<td class="online2">-</td>
						<td class="online3">
							@if ($periodStart || $periodEnd)
							{{ optional($periodStart)->format('Y.m.d') ?: '-' }} ~ {{ optional($periodEnd)->format('Y.m.d') ?: '-' }}
							@else
							-
							@endif
						</td>
						<td class="online4">
							@if ($isExpired)
							<span class="c_red">{{ $statusLabel }}</span>
							@else
							{{ $statusLabel }}
							@endif
							@if (! $isPaymentCompleted && isset($paymentStatusLabels[$enrollment->payment_status]))
							<br/>({{ $paymentStatusLabels[$enrollment->payment_status] }})
							@endif
						</td>
						<td class="online5">
							@if ($isCompleted)
							<a href="{{ route('mypage.print_completion', ['enrollment_id' => $enrollment->id]) }}" class="btn btn_kwk" target="_blank">이수증</a>
							@else
							-
							@endif
						</td>
						<td class="online6">
							@if ($isPaymentCompleted)
							<a href="{{ route('mypage.print_receipt_save', ['enrollment_id' => $enrollment->id]) }}" class="btn btn_kwk" target="_blank">영수증 출력</a>
							@else
							-
							@endif
						</td>
						<td class="online7">
							@if ($enrollment->course)
							<a href="{{ route('online_academy.show', $enrollment->course) }}" class="btn btn_kwk">강의보기</a>
							@else
							-
							@endif
						</td>
						<td class="online8"><a href="{{ route('mypage.online_training_view', ['id' => $enrollment->id]) }}" class="btn btn_kwk">신청 내역 보기</a></td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="8">수강 내역이 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<x-frontend.pagination :paginator="$enrollments" />

		<div class="gbox excl_wrap">
			<div class="tt excl">온라인 아카데미 안내</div>
			<ul class="dots_list">
				<li>수강 완료: 모든 강의를 100% 시청한 상태이며 이수증(수료증) 출력이 가능합니다.</li>
				<li>기간 만료: 수강 가능 기간이 종료되었습니다. 학습이 끝나지 않은 경우 '재결제'를 통해 기간을 연장할 수 있습니다.</li>
				<li>개인 사정으로 인해 부득이하게 기간 내 수강을 완료하지 못한 경우, 사무국(02-1234-5678)으로 유선 문의 주시면 예외 규정에 따라 연장 검토가 가능합니다.</li>
				<li>이수증은 학습 기간 종료 후에도 '나의 강의실'에서 언제든지 재출력할 수 있습니다.</li>
			</ul>
		</div>

	</div>
</section>

</main>

@endsection

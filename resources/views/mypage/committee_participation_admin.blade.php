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
					<dd class="count"><strong class="c_iden">{{ number_format($committeeStats['member_count']) }}</strong> / {{ number_format($committeeStats['committee_capacity']) }}<span class="sound_only">명</span></dd>
				</div>
				<div class="status_item">
					<dt>전체 신청</dt>
					<dd class="count"><strong>{{ number_format($committeeStats['total']) }}</strong><span class="sound_only">건</span></dd>
				</div>
				<div class="status_item">
					<dt>검토 대기</dt>
					<dd class="count"><strong>{{ number_format($committeeStats['pending']) }}</strong><span class="sound_only">건</span></dd>
				</div>
				<div class="status_item">
					<dt>승인 완료</dt>
					<dd class="count"><strong>{{ number_format($committeeStats['approved']) }}</strong><span class="sound_only">건</span></dd>
				</div>
				<div class="status_item">
					<dt>반려 건수</dt>
					<dd class="count"><strong>{{ number_format($committeeStats['rejected']) }}</strong><span class="sound_only">건</span></dd>
				</div>
			</dl>
		</div>

		<div class="board_top">
			<div class="left">
				<form method="GET" action="{{ route('mypage.committee_participation_admin') }}">
					<select name="status" class="text">
						<option value="all" @selected(($filterStatus ?? 'all') === 'all')>전체보기</option>
						@foreach ($statusLabels as $status => $label)
						<option value="{{ $status }}" @selected(strtoupper((string) $filterStatus) === $status)>{{ $label }}</option>
						@endforeach
					</select>
					@if (! empty($filterKeyword))
					<input type="hidden" name="keyword" value="{{ $filterKeyword }}">
					@endif
					<button type="submit" class="btn_search_solo">검색</button>
				</form>
			</div>
			<div class="right flex">
				<form method="GET" action="{{ route('mypage.committee_participation_admin') }}" class="search_area">
					<label for="event-search" class="sound_only">이름 또는 이메일 검색</label>
					@if (($filterStatus ?? 'all') !== 'all')
					<input type="hidden" name="status" value="{{ $filterStatus }}">
					@endif
					<input type="text" id="event-search" name="keyword" class="text" placeholder="이름 또는 이메일 검색" value="{{ $filterKeyword }}">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>

		<div class="board_list slide5 td_height">
			<table>
				<caption>위원회 신청 목록입니다.</caption>
				<thead>
					<tr>
						<th scope="col">이름</th>
						<th scope="col">이메일</th>
						<th scope="col">신청 일자</th>
						<th scope="col">상태</th>
						<th scope="col">확인</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($applications as $application)
					<tr>
						<td>{{ $application->applicant_name ?: '-' }}</td>
						<td>{{ $application->email ?: '-' }}</td>
						<td>{{ optional($application->applied_at)->format('Y.m.d') ?: '-' }}</td>
						<td>{{ $statusLabels[$application->status] ?? $application->status }}</td>
						<td>
							@if ($application->status === 'REJECTED' && $application->reject_reason)
							<button type="button" class="btn btn_kwk" data-reject-reason="{{ $application->reject_reason }}">반려 사유 확인</button>
							@elseif ($application->committee)
							<a href="{{ route('subcommittee.notice', ['committee' => $application->committee->id]) }}" class="btn btn_kwk">위원회 바로가기</a>
							@else
							-
							@endif
						</td>
					</tr>
					@empty
					<tr class="empty">
						<td colspan="5">위원회 신청 내역이 없습니다.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<x-frontend.pagination :paginator="$applications" />

	</div>
</section>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/mypage-committee-admin.js') }}"></script>
@endpush

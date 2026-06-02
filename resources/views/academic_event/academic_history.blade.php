@extends('layouts.frontend')
@inject('publicBoard', 'App\Services\Frontend\PublicBoardService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="academic-history-heading">
	<div class="inner">
		<h1 class="sub_title" id="academic-history-heading">{{ $sName }}</h1>

		<div class="board_top">
			<div class="left">
				<div class="total">Total <strong class="c_iden">{{ number_format($histories->total()) }}</strong></div>
			</div>
			<form method="GET" action="{{ route('academic_event.academic_history') }}" class="right flex">
				<label for="academic-history-field" class="sound_only">검색 구분</label>
				<select name="search_type" id="academic-history-field" class="text">
					<option value="all" @selected(($filters['search_type'] ?? 'all') === 'all')>전체</option>
					<option value="title" @selected(($filters['search_type'] ?? 'all') === 'title')>제목</option>
					<option value="content" @selected(($filters['search_type'] ?? 'all') === 'content')>내용</option>
				</select>
				<div class="search_area">
					<label for="academic-history-search" class="sound_only">행사명 검색</label>
					<input type="text" id="academic-history-search" name="keyword" class="text" value="{{ $filters['keyword'] ?? '' }}" placeholder="행사명을 입력해주세요">
					<button type="submit" class="btn_search">검색</button>
				</div>
			</form>
		</div>
		
		<div class="board_list board_bold mo_break_list">
			<table>
				<caption>학술대회 연혁입니다.</caption>
				<colgroup>
					<col class="dates">
					<col>
					<col class="down">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">행사 기간</th>
						<th scope="col">행사명</th>
						<th scope="col">행사자료</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($histories as $history)
						@php
							$attachmentUrl = $publicBoard->firstAttachmentUrl($history->attachments);
						@endphp
						<tr>
							<td class="dates">{{ $publicBoard->academicConferenceHistoryPeriod($history) ?: '-' }}</td>
							<td class="tac">{{ $history->title }}</td>
							<td class="down">
								@if ($attachmentUrl)
									<a href="{{ $attachmentUrl }}" class="btn btn_gwg btn_download" target="_blank" rel="noopener" download="{{ $publicBoard->firstAttachmentName($history->attachments) }}">자료집</a>
								@else
									-
								@endif
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="3" class="tac">등록된 학술대회 연혁이 없습니다.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<x-frontend.pagination :paginator="$histories" :window-size="5" />
	</div>
</section>
	
</main>

@endsection

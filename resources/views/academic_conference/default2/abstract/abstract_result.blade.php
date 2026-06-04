@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$isFrontendMember = ($currentMember?->role ?? null) === 'user';
	$showMemberSubmitButton = $isFrontendMember && ! ($hasMemberAbstractSubmission ?? false);
@endphp
<main class="sub_area">

<section class="scon">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>
		
		<div class="member_inbox">
			@unless ($abstractSubmission && $abstractSummary)
				<div class="gbox after_info">
					<h2 class="tt">초록 접수 내역이 없습니다.</h2>
					<p>확인 가능한 초록 접수 내역이 없습니다. 입력하신 정보를 다시 확인해 주세요.</p>
				</div>
				<div class="btns_btm flex_center">
					@if ($showMemberSubmitButton)
						<a href="{{ $conferenceBaseUrl }}/abstract/submission" class="btn btn_kwg">접수하기</a>
						<a href="{{ $conferenceBaseUrl }}" class="btn btn_wbb">메인 페이지로</a>
					@elseif ($isFrontendMember)
						<a href="{{ $conferenceBaseUrl }}" class="btn btn_kwg">메인 페이지로</a>
					@else
						<a href="{{ $conferenceBaseUrl }}/abstract/check_member" class="btn btn_kwg">회원 초록 조회</a>
						<a href="{{ $conferenceBaseUrl }}/abstract/check_non_member" class="btn btn_wbb">비회원 초록 조회</a>
					@endif
				</div>
			@else
				<div class="registration_check_wrap">
					<div class="infobox">
						<h3 class="tit">초록 접수 정보</h3>
						<dl>
							<div>
								<dt>접수번호</dt>
								<dd>{{ $abstractSummary['abstract_no'] }}</dd>
							</div>
							<div>
								<dt>접수 일시</dt>
								<dd>{{ $abstractSummary['submitted_at'] }}</dd>
							</div>
							<div>
								<dt>이름</dt>
								<dd>{{ $abstractSummary['author_name'] }}</dd>
							</div>
							<div>
								<dt>휴대폰번호</dt>
								<dd>{{ $abstractSummary['author_mobile'] }}</dd>
							</div>
							<div>
								<dt>이메일</dt>
								<dd>{{ $abstractSummary['author_email'] }}</dd>
							</div>
							<div>
								<dt>발표구분</dt>
								<dd>{{ $abstractSummary['presentation_type'] }}</dd>
							</div>
							<div>
								<dt>발표 분야</dt>
								<dd>{{ $abstractSummary['field'] }}</dd>
							</div>
							<div>
								<dt>초록 제목</dt>
								<dd>{{ $abstractSummary['title'] }}</dd>
							</div>
							<div>
								<dt>첨부파일</dt>
								<dd>
									@forelse ($abstractSubmission->files as $file)
										<a href="{{ asset('storage/' . $file->stored_path) }}" target="_blank" rel="noopener">{{ $file->original_name }}</a>@if(!$loop->last)<br/>@endif
									@empty
										-
									@endforelse
								</dd>
							</div>
						</dl>
					</div>
				</div>
				@if ($memberAbstracts->count() > 1)
					<div class="gbox mt">
						<h3 class="normal_tit">나의 초록 접수 내역</h3>
						<ul class="dots_list">
							@foreach ($memberAbstracts as $row)
								<li>{{ $row->abstract_no ?: ('ABS-' . $row->id) }} / {{ $row->title }} / {{ optional($row->submitted_at)->format('Y-m-d H:i') }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				<div class="btns_btm flex_center">
					<a href="{{ $conferenceBaseUrl }}" class="btn btn_kwg">메인 페이지로</a>
					<a href="{{ route('academic_conference.site.abstract.modify', [$event->folder_name, $abstractSubmission]) }}" class="btn btn_wbb">초록 수정</a>
				</div>
			@endunless
		</div>
		
	</div>
</section>

</main>
@endsection

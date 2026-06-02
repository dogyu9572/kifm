@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_end abstract_type" aria-labelledby="abstract-end-heading">
    <div class="inner">
		<div class="inbox">
		
			<div class="title_area">
				<h1 id="abstract-end-heading" class="title">초록 제출이 완료되었습니다.</h1>
				<p><a href="{{ $conferenceBaseUrl }}/abstract/check_member" class="c_iden"><strong>‘Abstract > 초록접수 조회’</strong></a> 페이지에서 진행 상태를 확인하실 수 있습니다.<br/>제출하신 초록은 검토 및 심사를 거쳐 추후 안내드립니다.</p>
			</div>
			
			<div class="gbox">
				<h2 class="sound_only">접수번호 요약</h2>
				@if ($abstractSummary)
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
					</dl>
				@else
					<p>확인 가능한 초록 접수 내역이 없습니다.</p>
				@endif
			</div>
			
			<div class="btns_btm">
				<a href="{{ $conferenceBaseUrl }}" class="btn btn_kwk">메인으로</a>
				<a href="{{ $conferenceBaseUrl }}/abstract/check_member" class="btn btn_wbb">초록접수 확인</a>
			</div>
			
		</div>
	</div>
</section>
</main>

@endsection

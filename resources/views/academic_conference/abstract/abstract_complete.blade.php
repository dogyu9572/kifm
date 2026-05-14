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
				<p><a href="" class="c_iden"><strong>‘Abstract > 초록접수 조회’</strong></a> 페이지에서 진행 상태를 확인하실 수 있습니다.<br/>제출하신 초록은 검토 및 심사를 거쳐 추후 안내드립니다.</p>
			</div>
			
			<div class="gbox">
				<h2 class="sound_only">접수번호 요약</h2>
				<dl>
					<div>
						<dt>접수번호</dt>
						<dd>ABS-2026-000123</dd>
					</div>
					<div>
						<dt>접수 일시</dt>
						<dd>2026-02-23    18:23:21</dd>
					</div>
					<div>
						<dt>이름</dt>
						<dd>홍길동</dd>
					</div>
					<div>
						<dt>휴대폰번호</dt>
						<dd>010-1234-5678</dd>
					</div>
					<div>
						<dt>이메일</dt>
						<dd>home@naver.com</dd>
					</div>
				</dl>
			</div>
			
			<div class="btns_btm">
				<a href="/academic_conference" class="btn btn_kwk">메인으로</a>
				<a href="/academic_conference/abstract/check" class="btn btn_wbb">초록접수 확인</a>
			</div>
			
		</div>
	</div>
</section>
</main>

@endsection
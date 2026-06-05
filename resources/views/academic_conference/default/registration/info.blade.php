@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="registration-information-heading">
	<div class="inner">
		<h1 class="sub_title" id="registration-information-heading">{{ $sName }}</h1>
		@php
			$preRegGuideHtml = $conference->contentHtml($event->pre_reg_guide);
			$regFeeGuideHtml = $conference->contentHtml($event->reg_fee_guide);
			$certDocGuideHtml = $conference->contentHtml($event->cert_doc_guide);
			$regInfoGuideHtml = $conference->contentHtml($event->reg_info_guide);
			$preRegPeriod = $conference->dateRangeText($event->pre_reg_start, $event->pre_reg_end, '2026. 03. 02 (화) ~ 2026. 03 .31 (화)');
			$onsiteRegPeriod = $conference->dateRangeText($event->onsite_reg_start, $event->onsite_reg_end, '학술대회 당일');
		@endphp
		
		<section class="info_section" aria-labelledby="pre-registration-title">
			<h2 class="stit" id="pre-registration-title">사전등록 안내</h2>
			<div class="gbox">
				@if ($preRegGuideHtml !== '')
					{!! $preRegGuideHtml !!}
				@else
					<dl class="pre_registration_list">
						<div>
							<dt>사전등록마감</dt>
							<dd>{{ $event->pre_reg_end ? $conference->dateRangeText(null, $event->pre_reg_end) . ' 까지' : '국민은행 287937-00-000083 (예금주: 대한기능의학회)' }}</dd>
						</div>
						<div>
							<dt>등록방법</dt>
							<dd>홈페이지(www.kifm.kr)에 사전등록 신청 배너이용</dd>
						</div>
						<div>
							<dt>입금계좌</dt>
							<dd>KB국민은행 287937-00-000083 대한기능의학회</dd>
						</div>
						<div>
							<dt>문의</dt>
							<dd>대한기능의학회 정세희 실장 HP: 010-8441-4884</dd>
						</div>
						<div>
							<dt>연수평점</dt>
							<dd>대한의사협회 6평점(신분증 지참 필수입니다.)</dd>
						</div>
					</dl>
				@endif
			</div>
		</section>

		<section class="info_section" aria-labelledby="registration-fee-title">
			<h2 class="stit" id="registration-fee-title">등록비 안내</h2>
			@if ($regFeeGuideHtml !== '')
				<div class="gbox">
					{!! $regFeeGuideHtml !!}
				</div>
			@else
				<div class="tbl tac">
					<table>
						<caption>등록비 안내 테이블</caption>
						<thead>
							<th scope="col">구분</th>
							<th scope="col">사전등록</th>
							<th scope="col">현장등록</th>
						</thead>
						<tbody>
							<tr>
								<th scope="row">비회원</th>
								<td>12만원</td>
								<td>20만원</td>
							</tr>
							<tr>
								<th scope="row">정회원</th>
								<td>8만원</td>
								<td>15만원</td>
							</tr>
							<tr>
								<th scope="row">간호사 및 공보의, 전공의</th>
								<td>5만원</td>
								<td>8만원</td>
							</tr>
						</tbody>
					</table>
				</div>
				<p class="etc c_red p_small">※ 사전등록 신청 후 취소시, 2025년 4월 1일(수) 까지는 수수료를 제외한 전액 환불되지만 그 이후부터는 환불이 불가합니다. </p>
			@endif
		</section>
		
		<section class="info_section" aria-labelledby="supporting-documents-title">
			<h2 class="stit" id="supporting-documents-title">증빙서류 발급 안내</h2>
			<div class="gbox">
				@if ($certDocGuideHtml !== '')
					{!! $certDocGuideHtml !!}
				@else
					<ul class="dots_list supporting_documents_list">
						<li>영수증  / 참가증명원 - 온라인 발급</li>
						<li>학술대회 홈페이지에서 로그인 후 [Registration] 메뉴의 [Registraion Check] 페이지에서 확인 및 출력(* 결제를 완료하신 경우 확인이 가능합니다.)</li>
						<li>참가증명원의 경우 행사 종료 후 확인 및 출력 가능</li>
					</ul>
				@endif
			</div>
		</section>
		
		<section class="info_section" aria-labelledby="academic-conference-title">
			<h2 class="stit" id="academic-conference-title">학술대회 참가등록 안내</h2>
			@if ($regInfoGuideHtml !== '')
				<div class="gbox">
					{!! $regInfoGuideHtml !!}
				</div>
			@else
				<div class="tbl tac">
					<table>
						<caption>등록비 안내 테이블</caption>
						<thead>
							<th scope="col">구분</th>
							<th scope="col">사전등록</th>
							<th scope="col">현장등록</th>
						</thead>
						<tbody>
							<tr>
								<th scope="row">신청 기한</th>
								<td>{{ $preRegPeriod }}</td>
								<td>{{ $onsiteRegPeriod }}</td>
							</tr>
							<tr>
								<th scope="row">결제</th>
								<td>
									<ol>
										<li>1. 카드 결제</li>
										<li>2. 무통장입금 (학회 확인 후 등록 완료)</li>
									</ol>
								</td>
								<td>카드 결제만 가능<br/>연회비 납부 시 학회 확인 후 등록 완료</td>
							</tr>
						</tbody>
					</table>
				</div>
			@endif
		</section>
		
		<section class="secretariat_contact mt">
			<h2>대한기능의학회 <br><strong class="c_iden">사무국</strong></h2>
			<div class="con">
				<p>회원가입 혹은 기타 문의사항이 있으실 경우, 대한기능의학회 사무국으로 문의해 주시기 바랍니다.</p>
				<ul class="tel_mail_infobox flex">
					<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
					<li class="i2"><a href="mailto:0182253645@naver.com">0182253645@naver.com</a></li>
				</ul>
			</div>
		</section>
		
		<div class="btns_btm">
			@if ($canPreRegister ?? true)
				<a href="{{ $conferenceBaseUrl }}/registration/reg" class="btn btn_wbb">사전등록 바로가기</a>
			@else
				<button type="button" class="btn btn_wbb" disabled>사전등록 마감</button>
			@endif
			<a href="{{ $conferenceBaseUrl }}/registration/check_member" class="btn btn_kwg">사전등록 조회</a>
		</div>
		
	</div>
</section>

</main>
@endsection

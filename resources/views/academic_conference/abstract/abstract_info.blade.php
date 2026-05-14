@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="submit-information-heading">
	<div class="inner">
		<h1 class="sub_title" id="submit-information-heading">{{ $sName }}</h1>
		
		<section class="info_section" aria-labelledby="pre-submit-title">
			<div class="stit">
				<h2 id="pre-submit-title">제출 기한 및 방식</h2>
				<a href="" class="btn_abso btn_download">초록 양식 다운로드</a>
			</div>
			<div class="gbox">
				<ul class="dots_list">
					<li>제출 마감: 2026년 O월 O일(요일) 24:00까지 (기한 엄수)</li>
					<li>제출 방법: 홈페이지 내 [초록 제출하기] 메뉴를 통해 온라인으로만 접수 가능합니다. (이메일 접수 불가)</li>
					<li>초록 템플릿을 다운로드 받아서 파일을 첨부해 주세요</li>
				</ul>
			</div>
		</section>
		
		<section class="info_section" aria-labelledby="pre-registration-title">
			<h2 class="stit" id="pre-registration-title">사전등록 안내</h2>
			<div class="gbox">
				<h3 class="normal_tit">1. 작성 지침</h3>
				<ul class="dots_list">
					<li>언어: 국문 또는 영문 선택 가능 (단, 발표 부문에 따라 권장 언어가 다를 수 있으니 공지사항을 확인하세요.)</li>
					<li>분량: 제목, 저자명, 소속을 제외한 본문은 [공백 포함 OOO자 이내]로 제한됩니다.</li>
					<li>구성: [연구 배경/목적 – 방법 – 결과 – 결론] 순으로 명확히 구분하여 작성해 주세요.</li>
				</ul>
				<br/>
				<h3 class="normal_tit">2. 수정 및 취소</h3>
				<ul class="dots_list">
					<li>수정: 제출 기간 중에는 ‘나의 제출 내역’에서 언제든 내용 수정이 가능합니다.</li>
					<li>취소: 제출 마감일 이후에는 수정 및 취소가 불가하오니 신중히 제출해 주시기 바랍니다.</li>
					<li>발표 세션은 학술위원회 심사를 거쳐 최종 확정되며, 원하시는 세션과 다르게 배정될 수 있습니다.</li>
				</ul>
			</div>
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
			<a href="/academic_conference/abstract/submission" class="btn btn_wbb">초록 신청</a>
			<a href="/academic_conference/abstract/check_member" class="btn btn_kwg">초록신청 조회</a>
		</div>
		
	</div>
</section>

</main>
@endsection
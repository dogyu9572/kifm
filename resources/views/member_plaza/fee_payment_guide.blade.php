@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="fee-payment-guide-heading">
	<div class="inner">
		<h1 class="sub_title" id="fee-payment-guide-heading">{{ $sName }}</h1>
		
		<div class="fee_payment_guide_area">
			
			<section class="guide_section">
                <h2 class="tit">대한기능의학회 회비 납부 안내</h2>
				<p>질병의 근본 원인을 탐구하고 환자 맞춤형 정밀 의료를 실천하는 대한기능의학회와 함께할 역량 있는 전문가 여러분을 모십니다.<br/>
				본회는 기능의학 연구와 임상 진료에 종사하는 의료인 및 관련 분야 전문가로서, 학회의 목적에 찬동하고 소정의 회비를 납부한 분들을 정중히 환영합니다.</p>
			</section>

            <section class="guide_section">
				<h2 class="tit">회원 여부 확인 및 신규 가입</h2>
				<p>질병의 근본 원인을 탐구하고 환자 맞춤형 정밀 의료를 실천하는 대한기능의학회와 함께할 역량 있는 전문가 여러분을 모십니다.<br/>
				본회는 기능의학 연구와 임상 진료에 종사하는 의료인 및 관련 분야 전문가로서, 학회의 목적에 찬동하고 소정의 회비를 납부한 분들을 정중히 환영합니다.</p>
				<dl class="gbox">
					<div class="i1"><dt>입금 계좌</dt><dd>국민은행 287937-00-000083 (예금주: 대한기능의학회)</dd></div>
					<div class="i2"><dt>안내 사항</dt><dd>회비 입금 후 성함과 의사면허번호를 포함하여 학회 사무국으로 반드시 연락주시기 바랍니다.</dd></div>
					<div class="i3"><dt>승인 절차</dt><dd>회원 가입 승인은 정기적으로 검토 후 진행됩니다.</dd></div>
				</dl>
			</section>

            <section class="guide_section member_type">
				<div class="tit_flex"><h2 class="tit mb0">회원 구분 및 혜택 안내</h2><a href="/member/register" class="btn_abso btn_wkk btn_link">회원가입 바로가기</a></div>
				<div class="tbl tac">
					<table>
						<caption class="blind">회원 구분(준회원, 정회원, 평생회원)에 따른 신청 대상, 회비, 주요 혜택 안내</caption>
						<thead>
							<tr>
								<th scope="col">구분</th>
								<th scope="col">준회원</th>
								<th scope="col">정회원</th>
								<th scope="col">평생회원</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">신청 대상</th>
								<td>전공의, 간호사, 관련 학부생 등</td>
								<td>전문의, 일반의, 한의사, 치과의사 등</td>
								<td>정회원 자격을 갖춘 자 중 평생회비 납부자</td>
							</tr>
							<tr>
								<th scope="row">회비</th>
								<td>입회비 3만원</td>
								<td>입회비 5만원 + 연회비 5만원(매년)</td>
								<td>입회비 5만원 + 평생회비 50만원 (1회)</td>
							</tr>
							<tr>
								<th scope="row">주요 혜택</th>
								<td>
									<div class="flex_colm flex_center dots_list">
										<p>홈페이지 일반 자료실 이용</p>
										<p>학회 뉴스레터 수신</p>
									</div>
								</td>
								<td>
									<div class="flex_colm flex_center dots_list">
										<p>학술대회 VOD 및 강연록 열람</p>
										<p>학회지(KJFMS) 정기 발송</p>
										<p>기능의학 인증의 시험 응사 자격</p>
									</div>
								</td>
								<td>
									<div class="flex_colm flex_center dots_list">
										<p>정회원 혜택 일체 포함</p>
										<p>차기 학술대회 등록비 감면</p>
										<p>학회 임원 선출 및 피선거권</p>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</section>
			
			<article class="subcommittee_top mb0">
				<h2>대한기능의학회 <br><strong class="c_iden">사무국</strong></h2>
				<div class="con">
					<p>회원가입 혹은 기타 문의사항이 있으실 경우, 대한기능의학회 사무국으로 문의해 주시기 바랍니다.</p>
					<ul class="tel_mail_infobox flex">
						<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
						<li class="i2"><a href="mailto:0182253645@naver.com">0182253645@naver.com</a></li>
					</ul>
				</div>
			</article>
			
			<div class="btns_btm">
				<a href="/member/register" class="btn btn_wbb btn_short">회원가입 바로가기</a>
			</div>
		</div>
		
	</div>
</section>
	
</main>
@endsection
@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon register_wrap" aria-labelledby="register-heading">
	<div class="inner">
		<h1 class="sub_title" id="register-heading">{{ $sName }}</h1>
		
		@include('mypage.mypage_tab')
		
		<div class="my_head_half">
			<section class="box left" aria-labelledby="fee-payment-title">
				<h2 class="mytit" id="fee-payment-title">연회비 납부</h2>
				<!-- 납부 전 -->
				<!-- <div class="glbox bg">
					<h3 class="tit">선생님께서는 현재 <strong class="c_red">회비 미납 상태</strong>입니다.</h3>
					<p>선생님께서는 현재 회비 미납 상태입니다.</p>
					<a href="/mypage/annual_fee" class="btn btn_wkk btn_arrow">연회비 납부하기 <span class="sound_only">(페이지 이동)</span></a>
				</div> -->
				<!-- 무통장 입금 확인 중 -->
				<!-- <div class="glbox bg bg_bank">
					<h3 class="tt c_iden">입금계좌 안내</h3>
					<div class="tit"><strong>국민은행  287937-00-000083 (예금주: 대한기능의학회)</strong></div>
					<p>무통장 입금 확인 중입니다. 입금 완료 후 승인됩니다.</p>
					<button type="button" class="btn btn_kwg btn_cancel" onclick="layerShow('pop_cancel');">결제 신청 취소</button>
				</div> -->
				<!-- 납부 후 -->
				<div class="glbox bg">
					<h3 class="tit">선생님께서는 <strong class="c_iden">연회비를 납부하셨습니다.</strong></h3>
					<p>납부완료 일시: 2025년 01년 02일  15:23:15</p>
					<a href="/mypage/print_receipt" class="btn btn_wkk" target="_blank">영수증 출력</a>
				</div>
			</section>

			<section class="box right" aria-labelledby="certification-status-title">
				<h2 class="mytit" id="certification-status-title">인증의 유지</h2>
				
				<div class="glbox participation_area">
					<div class="info">
						<div class="l" id="participation-label">학술대회 참가 <span class="c_iden"><strong>1</strong>/3회</span></div>
						<div class="r"><p class="excl_rev">2회 부족</p></div>
					</div>
					
					<div class="state_line" role="progressbar" aria-labelledby="participation-label" aria-valuenow="1" aria-valuemin="0" aria-valuemax="3">
						<div class="bar"></div>
					</div>
					
					<p class="excl c_black">
						<span class="sound_only">알림: </span>
						인증의 자격 유지를 위해 <strong class="c_red">학술대회 2회 추가 참석이 필요합니다.</strong>
					</p>
				</div>

				<ul class="gbox state_tri">
					<li class="i1">
						<h3>학술대회</h3>
						<p><strong>1회</strong> <span class="sound_only">중</span> / 3회</p>
					</li>
					<li class="i2">
						<h3>온라인 아카데미</h3>
						<p><span class="sound_only">상태: </span><strong>수료</strong></p>
					</li>
					<li class="i3">
						<h3>회비</h3>
						<p><span class="sound_only">상태: </span><strong>납부완료</strong></p>
					</li>
				</ul>
			</section>
		</div>
		
		<div class="register_area">
			<form action="/member/register" method="POST" class="register_form">
				<fieldset class="register_section">
					<legend class="sound_only">기본정보 입력</legend>
					<div class="num_tit_area">
						<div class="mytit" aria-hidden="true">기본정보 입력</div>
						<p class="abso c_red">* 은 필수 입력 항목입니다.</p>
					</div>
					<div class="flex_inputs">
						<ul>
							<li>
								<label for="register-id">아이디<span class="c_iden">*</span></label>
								<input type="text" id="register-id" class="text" value="homepagekorea@naver.com" readonly>
							</li>
							<li>
								<label for="register-level">회원등급<span class="c_iden">*</span></label>
								<input type="text" id="register-level" class="text" value="정회원" readonly>
							</li>
							<li>
								<label for="register-pw">새로운 비밀번호<span class="c_iden">*</span> <p class="abso c_iden" aria-describedby="pw-help">* 비밀번호는 변경을 원하시는 경우에만 입력해 주세요.</p></label>
								<input type="password" id="register-pw" class="text" placeholder="영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요." required>
							</li>
							<li>
								<label for="register-pw-check">새로운 비밀번호 확인*<span class="c_iden">*</span></label>
								<input type="password" id="register-pw-check" class="text" placeholder="영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요." required>
							</li>
							<li>
								<label for="register-name-kor">한글 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-kor" class="text" placeholder="한글 이름을 입력해 주세요." value="홍길동" required>
							</li>
							<li>
								<label for="register-name-eng">영문 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-eng" class="text" placeholder="영문 이름을 입력해 주세요." value="hong gil dong" required>
							</li>
							<li>
								<label for="register-phone">휴대폰 번호<span class="c_iden">*</span></label>
								<input type="text" id="register-phone" class="text" placeholder="휴대폰 번호를 입력해 주세요." value="010-0000-0000" required>
							</li>
							<li>
								<label for="register-email">이메일<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-email" class="text" placeholder="이메일을 모두 입력해주세요." value="email@mail.com" required>
									<button type="button" class="btn btn_wkk">중복 확인</button>
								</div>
							</li>
						</ul>
					</div>
				</fieldset>
				
				<fieldset class="register_section">
					<legend class="sound_only">추가정보 입력</legend>
					<div class="num_tit_area">
						<div class="mytit" aria-hidden="true">추가정보 입력</div>
						<p class="abso c_red">* 은 필수 입력 항목입니다.</p>
					</div>
					<div class="flex_inputs">
						<ul>
							<li>
								<label for="register-type1">구분<span class="c_iden">*</span></label>
								<div class="radios flex">
									<div class="radio"><input type="radio" name="register-type" id="register-type1" checked required><label for="register-type1"><i aria-hidden="true"></i><span>전문의</span></label></div>
									<div class="radio"><input type="radio" name="register-type" id="register-type2"><label for="register-type2"><i aria-hidden="true"></i><span>전공의</span></label></div>
									<div class="radio"><input type="radio" name="register-type" id="register-type3"><label for="register-type3"><i aria-hidden="true"></i><span>공보의</span></label></div>
									<div class="radio"><input type="radio" name="register-type" id="register-type4"><label for="register-type4"><i aria-hidden="true"></i><span>군의관</span></label></div>
									<div class="radio"><input type="radio" name="register-type" id="register-type5"><label for="register-type5"><i aria-hidden="true"></i><span>간호사</span></label></div>
									<div class="radio"><input type="radio" name="register-type" id="register-type6"><label for="register-type6"><i aria-hidden="true"></i><span>기타</span></label></div>
								</div>
							</li>
							<li>
								<label for="register-doctor-num">의사면허번호<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-doctor-num" class="text" placeholder="의사면허번호를 입력해주세요." required>
									<button type="button" class="btn btn_wkk">중복 확인</button>
								</div>
							</li>
							<!-- <li>
								<label for="register-specialist">전문의번호<span class="c_iden">*</span></label>
								<input type="text" id="register-specialist" class="text" placeholder="전문의 번호를 입력해 주세요." required>
							</li> -->
							<li>
								<label for="register-specialty">진료 과목<span class="c_iden">*</span></label>
								<select name="" id="register-specialty" class="text"required>
									<option value="">진료 과목을 입력해 주세요.</option>
								</select>
							</li>
							<li>
								<label for="register-company">직장명<span class="c_iden">*</span></label>
								<input type="text" id="register-company" class="text" placeholder="직장명을 입력해 주세요." required>
							</li>
							<li>
								<label for="register-company-tel">직장전화<span class="c_iden">*</span></label>
								<input type="text" id="register-company-tel" class="text" placeholder="직장 전화번호를 입력해 주세요." required>
							</li>
							<li class="w100p">
								<label for="register-company-address">직장주소</label>
								<div class="half_box flex">
									<div class="inbtn">
										<input type="text" id="register-company-address" class="text" placeholder="직장주소를 입력해 주세요.">
										<button type="button" class="btn btn_wkk">주소검색</button>
									</div>
									<input type="text" class="text half" placeholder="나머지 주소를 입력해 주세요.">
								</div>
							</li>
							<li>
								<label for="register-graduation">학교 졸업년도</label>
								<input type="text" id="register-graduation" class="text" placeholder="최종 학교 졸업년도를 숫자로만 입력해 주세요.">
							</li>
							<li>
								<label for="register-university">출신 대학교</label>
								<input type="text" id="register-university" class="text" placeholder="출신 대학교를 입력해 주세요. ">
							</li>
						</ul>
					</div>
				</fieldset>
				
				<div class="btns_btm">
					<button type="submit" class="btn btn_wbb">회원정보 수정</button>
					<a href="/mypage/secession" class="btn btn_kwg">회원탈퇴</a>
				</div>
			</form>
		</div>
		
	</div>
</section>

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
			<button type="button" class="btn btn_kwg">신청 취소</button>
		</div>
	</div>
</div>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/script_popup.js') }}"></script>
<script>
$(document).ready(function () {
// 로그인 후 인증의 참석 현황 게이지바
	$('.participation_area').each(function() {
        const text = $(this).find('.info .l').text();
        const matches = text.match(/\d+/g);

        if (matches && matches.length >= 2) {
            const current = parseInt(matches[0]);
            const total = parseInt(matches[1]);
            const percentage = (current / total) * 100;

            $(this).find('.state_line .bar').css('width', percentage + '%');
        }
    });
});
</script>
@endpush
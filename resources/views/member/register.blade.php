@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon register_wrap" aria-labelledby="register-heading">
	<div class="inner">
		<h1 class="sub_title" id="register-heading">{{ $sName }}</h1>
		
		<nav class="register_step_area">
			<h2 class="sound_only">회원가입 단계</h2>
			<ul class="register_step">
				<li class="i1 on"aria-current="step"><i class="aria-hidden"></i><p>회원정보 입력</p></li>
				<li class="i2"><i class="aria-hidden"></i><p>회원가입 완료</p></li>
			</ul>
		</nav>
		
		<div class="register_area">
			<form action="/member/register" method="POST" class="register_form">
				<fieldset class="register_section">
					<legend class="sound_only">기본정보 입력</legend>
					<div class="num_tit_area">
						<div class="num_tit radius4" aria-hidden="true"><span>1</span>기본정보 입력</div>
						<p class="abso c_red">* 은 필수 입력 항목입니다.</p>
					</div>
					<div class="flex_inputs">
						<ul>
							<li class="w100p">
								<label for="register-id">아이디<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-id" class="text" placeholder="아이디를 입력해주세요." required>
									<button type="button" class="btn btn_wkk">중복 확인</button>
								</div>
							</li>
							<li>
								<label for="register-pw">비밀번호<span class="c_iden">*</span></label>
								<input type="password" id="register-pw" class="text" placeholder="영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요." required>
							</li>
							<li>
								<label for="register-pw-check">비밀번호 확인<span class="c_iden">*</span></label>
								<input type="password" id="register-pw-check" class="text" placeholder="영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요." required>
							</li>
							<li>
								<label for="register-name-kor">한글 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-kor" class="text" placeholder="한글 이름을 입력해 주세요." required>
							</li>
							<li>
								<label for="register-name-eng">영문 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-eng" class="text" placeholder="영문 이름을 입력해 주세요." required>
							</li>
							<li>
								<label for="register-phone">휴대폰 번호<span class="c_iden">*</span></label>
								<input type="text" id="register-phone" class="text" placeholder="휴대폰 번호를 입력해 주세요." required>
							</li>
							<li>
								<label for="register-email">이메일<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-email" class="text" placeholder="이메일을 모두 입력해주세요." required>
									<button type="button" class="btn btn_wkk">중복 확인</button>
								</div>
							</li>
						</ul>
					</div>
				</fieldset>
				
				<fieldset class="register_section">
					<legend class="sound_only">추가정보 입력</legend>
					<div class="num_tit_area">
						<div class="num_tit radius4" aria-hidden="true"><span>2</span>추가정보 입력</div>
						<p class="abso c_red">* 은 필수 입력 항목입니다.</p>
					</div>
					<div class="flex_inputs">
						<ul>
							<li>
								<label for="register-type1">구분<span class="c_iden">*</span></label>
								<div class="radios flex">
									<div class="radio"><input type="radio" name="register-type" id="register-type1" required><label for="register-type1"><i aria-hidden="true"></i><span>전문의</span></label></div>
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
							<li>
								<label for="register-specialist">전문의번호<span class="c_iden">*</span></label>
								<input type="text" id="register-specialist" class="text" placeholder="전문의 번호를 입력해 주세요." required>
							</li>
							<li>
								<label for="register-specialty">전문과<span class="c_iden">*</span></label>
								<input type="text" id="register-specialty" class="text" placeholder="전문과를 입력해 주세요." required>
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
				
				<fieldset class="register_section">
					<legend class="sound_only">위원회 참가 신청</legend>
					<div class="num_tit_area">
						<div class="num_tit radius4" aria-hidden="true"><span>3</span>위원회 참가 신청</div>
						<p class="abso c_red">*최대 3개 선택 가능</p>
					</div>
					<div class="select_committee">
						<div class="select_check"><input type="checkbox" id="register-committee01" name="register-committee"><label for="register-committee01"><span>학술위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee02" name="register-committee"><label for="register-committee02"><span>편집위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee03" name="register-committee"><label for="register-committee03"><span>기획위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee04" name="register-committee"><label for="register-committee04"><span>정보위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee05" name="register-committee"><label for="register-committee05"><span>교육위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee06" name="register-committee"><label for="register-committee06"><span>보험위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee07" name="register-committee"><label for="register-committee07"><span>법제위원회</span></label></div>
						<div class="select_check"><input type="checkbox" id="register-committee08" name="register-committee"><label for="register-committee08"><span>국제위원회</span></label></div>
					</div>
				</fieldset>
				
				<fieldset class="register_section">
					<legend class="sound_only">개인정보 수집·이용에 관한 안내 사항</legend>
					<div class="num_tit_area">
						<div class="num_tit radius4" aria-hidden="true"><span>4</span>개인정보 수집·이용에 관한 안내 사항</div>
					</div>
					<div class="txt_scroll_area glbox">
						<div class="txt_scroll">
							<strong>[개인정보 수집·이용에 대한 동의]</strong>
							<p>개인정보 수집·이용에 대한 동의내용이 들어가는 공간입니다.<br/>
							개인정보 수집·이용에 대한 동의내용이 들어가는 공간입니다.<br/>
							개인정보 수집·이용에 대한 동의내용이 들어가는 공간입니다.</p>
						</div>
					</div>
					<div class="checkbox"><input type="checkbox" id="register-privacy" name="register-privacy"><label for="register-privacy"><i></i><span><strong class="c_red">(필수)</strong> 개인정보의 수집 및 이용에 동의합니다.</span></label></div>
				</fieldset>
				
				<fieldset class="register_section">
					<legend class="sound_only">이용약관</legend>
					<div class="num_tit_area">
						<div class="num_tit radius4" aria-hidden="true"><span>5</span>이용약관</div>
					</div>
					<div class="txt_scroll_area glbox">
						<div class="txt_scroll">
							<strong>[이용약관에 대한 동의]</strong>
							<p>이용약관에 대한 동의내용이 들어가는 공간입니다.<br/>
							이용약관에 대한 동의내용이 들어가는 공간입니다.<br/>
							이용약관에 대한 동의내용이 들어가는 공간입니다.</p>
						</div>
					</div>
					<div class="checkbox"><input type="checkbox" id="terms_of_use" name="terms_of_use"><label for="terms_of_use"><i></i><span><strong class="c_red">(필수)</strong> 이용약관에 동의합니다.</span></label></div>
				</fieldset>
				
				<div class="btns_btm">
					<button type="button" class="btn btn_kwg" onclick="history.back();">뒤로가기</button>
					<button type="submit" class="btn btn_wbb" onclick="location.href='/member/register_success'">가입하기</button>
				</div>
			</form>
		</div>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
//위원회 참가 신청
    const $committeeChecks = $('input[name="register-committee"]');
    const maxSelection = 3;
    $committeeChecks.on('change', function() {
        const checkedCount = $committeeChecks.filter(':checked').length;
        if (checkedCount > maxSelection) {
            $(this).prop('checked', false);
            alert('위원회 참가 신청은 최대 ' + maxSelection + '개까지만 선택 가능합니다.');
        }
    });
});
</script>
@endpush
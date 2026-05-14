@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="hospital-information-heading">
	<div class="inner">
		<h1 class="sub_title" id="hospital-information-heading">{{ $sName }}</h1>
		
		@include('mypage.mypage_tab')
		
		<div class="hospital_information_area">
			<h2 class="sound_only">병원 정보</h2>
			
			<fieldset>
				<legend class="btit">기본정보</legend>
				<div class="profile_area float">
					<div class="img_area">
						<div class="imgfit"><img src="/images/img_sample_profile_human.jpg" alt=""></div>
						<p class="c_iden">*사진을 클릭하여 이미지를 변경해 주세요.</p>
					</div>
					<dl class="txt_area">
						<div>
							<dt>선생님 성함</dt>
							<dd><input type="text" class="text" value="정회원" readonly></dd>
						</div>
						<div>
							<dt>면허번호</dt>
							<dd><input type="text" class="text" value="1212121232" readonly></dd>
						</div>
						<div>
							<dt>병원소개</dt>
							<dd><textarea name="" id="" cols="30" rows="10" class="text w100p edit_area">이 영역은 더미 에디터입니다.
실제 구현 시에는 CKEditor, TinyMCE, 또는 Quill과 같은 위지윅(WYSIWYG) 에디터 라이브러리가 적용될 예정입니다.
이미지 업로드 및 관리
다양한 텍스트 서식 지정
표 생성 및 편집
HTML 소스 보기 기능</textarea></dd>
						</div>
					</dl>
				</div>
			</fieldset>
			
			<fieldset class="register_wrap">
				<legend class="btit">병원 정보 관리</legend>
				<div class="flex_inputs float">
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
			
			<fieldset>
				<legend class="btit">진료 정보</legend>
				<div class="checkbox_flex float" role="group">
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation01"><label for="medicalInformation01"><i></i>내과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation02"><label for="medicalInformation02"><i></i>소아청소년과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation03"><label for="medicalInformation03"><i></i>이비인후과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation04"><label for="medicalInformation04"><i></i>가정의학과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation05"><label for="medicalInformation05"><i></i>피부과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation06"><label for="medicalInformation06"><i></i>성형외과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation07"><label for="medicalInformation07"><i></i>산부인과</label></div>
					<div class="checkbox"><input type="checkbox" name="medicalInformation" id="medicalInformation08"><label for="medicalInformation08"><i></i>소아청소년과</label></div>
				</div>
			</fieldset>
			
			<fieldset>
				<legend class="btit">시행하고 있는 기능의학 검사</legend>
				<div class="checkbox_flex float" role="group">
					<div class="checkbox"><input type="checkbox" name="functional" id="functional01"><label for="functional01"><i></i>유기산 검사</label></div>
					<div class="checkbox"><input type="checkbox" name="functional" id="functional02"><label for="functional02"><i></i>마이크로바이옴 검사</label></div>
					<div class="checkbox"><input type="checkbox" name="functional" id="functional03"><label for="functional03"><i></i>영양소 결핍 패널</label></div>
					<div class="checkbox"><input type="checkbox" name="functional" id="functional04"><label for="functional04"><i></i>식품 과민반응 검사</label></div>
					<div class="checkbox"><input type="checkbox" name="functional" id="functional05"><label for="functional05"><i></i>호르몬 균형 검사</label></div>
					<div class="checkbox"><input type="checkbox" name="functional" id="functional06"><label for="functional06"><i></i>미토콘드리아</label></div>
					<div class="checkbox"><input type="checkbox" name="functional" id="functional07"><label for="functional07"><i></i>산화 스트레스 검사</label></div>
				</div>
			</fieldset>
			
			<fieldset>
				<legend class="btit">치료 가능 영역</legend>
				<div class="checkbox_flex float" role="group">
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea01"><label for="treatableArea01"><i></i>만성 피로</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea02"><label for="treatableArea02"><i></i>자가면역 질환</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea03"><label for="treatableArea03"><i></i>대사 증후군</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea04"><label for="treatableArea04"><i></i>갑상선 기능 이상</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea05"><label for="treatableArea05"><i></i>호르몬 불균형</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea06"><label for="treatableArea06"><i></i>알레르기/아토피</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea07"><label for="treatableArea07"><i></i>비만/체중관리</label></div>
					<div class="checkbox"><input type="checkbox" name="treatableArea" id="treatableArea08"><label for="treatableArea08"><i></i>소화기 질환</label></div>
				</div>
			</fieldset>
			
			<fieldset class="about_hospital">
				<legend class="sound_only">병원소개</legend>
				
				<h3 class="tit">병원소개</h3>
				<textarea name="" id="" cols="30" rows="10" class="text w100p edit_area">이 영역은 더미 에디터입니다.
실제 구현 시에는 CKEditor, TinyMCE, 또는 Quill과 같은 위지윅(WYSIWYG) 에디터 라이브러리가 적용될 예정입니다.
이미지 업로드 및 관리
다양한 텍스트 서식 지정
표 생성 및 편집
HTML 소스 보기 기능</textarea>
				
				<h3 class="tit">질환 및 증후군</h3>
				<textarea name="" id="" cols="30" rows="2" class="text w100p" placeholder="예: 섬유근육통, 복합부위통증증후군(CRPS), 번아웃 증후군, 과민성 대장 증후군 등
항목은 줄바꿈으로 구분하여 입력하세요."></textarea>
			</fieldset>
			
		</div>
		
	</div>
</section>
	
</main>

@endsection
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
				<li class="i1 on" aria-current="step"><i class="aria-hidden"></i><p>회원정보 입력</p></li>
				<li class="i2"><i class="aria-hidden"></i><p>회원가입 완료</p></li>
			</ul>
		</nav>

		<div
			class="register_area"
			id="member-register-page"
			data-check-login-id="{{ route('member.register.check-login-id') }}"
			data-check-email="{{ route('member.register.check-email') }}"
			data-check-phone="{{ route('member.register.check-phone') }}"
			data-check-license="{{ route('member.register.check-license') }}"
		>
			<form action="{{ route('member.register.store') }}" method="POST" class="register_form" novalidate>
				@csrf
				<input type="hidden" name="join_type" value="email">

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
									<input type="text" id="register-id" name="login_id" class="text" value="{{ old('login_id') }}" placeholder="아이디를 입력해주세요." required maxlength="80" autocomplete="username">
									<button type="button" class="btn btn_wkk js-register-check-login-id">중복 확인</button>
								</div>
								@error('login_id')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-pw">비밀번호<span class="c_iden">*</span></label>
								<input type="password" id="register-pw" name="password" class="text" placeholder="8~10자 (백오피스 회원 등록과 동일)" required minlength="8" maxlength="10" autocomplete="new-password">
								@error('password')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-pw-check">비밀번호 확인<span class="c_iden">*</span></label>
								<input type="password" id="register-pw-check" name="password_confirmation" class="text" placeholder="비밀번호를 한 번 더 입력해주세요." required minlength="8" maxlength="10" autocomplete="new-password">
								@error('password_confirmation')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-name-kor">한글 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-kor" name="name" class="text" value="{{ old('name') }}" placeholder="한글 이름을 입력해 주세요." required maxlength="20">
								@error('name')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-name-eng">영문 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-eng" name="name_en" class="text" value="{{ old('name_en') }}" placeholder="영문 이름을 입력해 주세요." required maxlength="100">
								@error('name_en')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-phone">휴대폰 번호<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-phone" name="phone_number" class="text js-register-phone-input" value="{{ old('phone_number') }}" placeholder="숫자만 입력 (하이픈 자동)" inputmode="numeric" autocomplete="tel" maxlength="13">
									<button type="button" class="btn btn_wkk js-register-check-phone">중복 확인</button>
								</div>
								@error('phone_number')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-email">이메일<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="email" id="register-email" name="email" class="text" value="{{ old('email') }}" placeholder="이메일을 입력해주세요." required autocomplete="email">
									<button type="button" class="btn btn_wkk js-register-check-email">중복 확인</button>
								</div>
								@error('email')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
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
								<span class="sound_only">구분<span class="c_iden">*</span></span>
								<div class="radios flex" role="radiogroup" aria-label="구분">
									<div class="radio"><input type="radio" name="job_type" id="register-type1" value="specialist" @checked(old('job_type', 'specialist') === 'specialist') required><label for="register-type1"><i aria-hidden="true"></i><span>전문의</span></label></div>
									<div class="radio"><input type="radio" name="job_type" id="register-type2" value="resident" @checked(old('job_type') === 'resident')><label for="register-type2"><i aria-hidden="true"></i><span>전공의</span></label></div>
									<div class="radio"><input type="radio" name="job_type" id="register-type3" value="public_doctor" @checked(old('job_type') === 'public_doctor')><label for="register-type3"><i aria-hidden="true"></i><span>공보의</span></label></div>
									<div class="radio"><input type="radio" name="job_type" id="register-type4" value="military_doctor" @checked(old('job_type') === 'military_doctor')><label for="register-type4"><i aria-hidden="true"></i><span>군의관</span></label></div>
									<div class="radio"><input type="radio" name="job_type" id="register-type5" value="nurse" @checked(old('job_type') === 'nurse')><label for="register-type5"><i aria-hidden="true"></i><span>간호사</span></label></div>
									<div class="radio"><input type="radio" name="job_type" id="register-type6" value="other" @checked(old('job_type') === 'other')><label for="register-type6"><i aria-hidden="true"></i><span>기타</span></label></div>
								</div>
								@error('job_type')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-doctor-num">의사면허번호<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-doctor-num" name="license_number" class="text" value="{{ old('license_number') }}" placeholder="의사면허번호를 입력해주세요." required maxlength="80">
									<button type="button" class="btn btn_wkk js-register-check-license">중복 확인</button>
								</div>
								@error('license_number')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-specialist">전문의번호<span class="c_iden">*</span></label>
								<input type="text" id="register-specialist" name="specialist_number" class="text" value="{{ old('specialist_number') }}" placeholder="전문의 번호를 입력해 주세요." required maxlength="80">
								@error('specialist_number')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-specialty">전문과<span class="c_iden">*</span></label>
								<input type="text" id="register-specialty" name="specialty" class="text" value="{{ old('specialty') }}" placeholder="전문과를 입력해 주세요." required maxlength="120">
								@error('specialty')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-company">직장명<span class="c_iden">*</span></label>
								<input type="text" id="register-company" name="workplace_name" class="text" value="{{ old('workplace_name') }}" placeholder="직장명을 입력해 주세요." required maxlength="200">
								@error('workplace_name')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-company-tel">직장전화<span class="c_iden">*</span></label>
								<input type="text" id="register-company-tel" name="workplace_phone" class="text" value="{{ old('workplace_phone') }}" placeholder="직장 전화번호를 입력해 주세요." required maxlength="40">
								@error('workplace_phone')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li class="w100p">
								<label for="register-company-address">직장주소</label>
								<div class="half_box flex">
									<div class="inbtn">
										<input type="text" id="register-company-address" name="workplace_address" class="text" value="{{ old('workplace_address') }}" placeholder="직장 주소(기본)" maxlength="255" readonly>
										<button type="button" class="btn btn_wkk js-register-search-workplace-address">주소검색</button>
									</div>
									<input type="text" id="register-workplace-address-detail" name="workplace_address_detail" class="text half" value="{{ old('workplace_address_detail') }}" placeholder="나머지 주소를 입력해 주세요." maxlength="255">
								</div>
								<input type="hidden" id="register-workplace-zipcode" name="workplace_zipcode" value="{{ old('workplace_zipcode') }}">
								@error('workplace_zipcode')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
								@error('workplace_address')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
								@error('workplace_address_detail')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-graduation">학교 졸업년도</label>
								<input type="number" id="register-graduation" name="graduate_year" class="text" value="{{ old('graduate_year') }}" placeholder="예: 2010" min="1950" max="2100" step="1">
								@error('graduate_year')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-university">출신 대학교</label>
								<input type="text" id="register-university" name="school_name" class="text" value="{{ old('school_name') }}" placeholder="출신 대학교를 입력해 주세요." maxlength="255">
								@error('school_name')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
						</ul>
					</div>
				</fieldset>

				<fieldset class="register_section">
					<legend class="sound_only">위원회 참가 신청</legend>
					<div class="num_tit_area">
						<div class="num_tit radius4" aria-hidden="true"><span>3</span>위원회 참가 신청</div>
						<p class="abso c_red">*최대 3개 선택 가능 (등록된 산하위원회 기준)</p>
					</div>
					<div class="select_committee">
						@php
							$oldCommittees = old('committee_codes', []);
							if (! is_array($oldCommittees)) {
								$oldCommittees = [];
							}
						@endphp
						@forelse ($committeesForRegister ?? [] as $committeeRow)
							<div class="select_check">
								<input
									type="checkbox"
									id="register-committee-{{ $committeeRow->id }}"
									name="committee_codes[]"
									value="{{ (string) $committeeRow->id }}"
									@checked(in_array((string) $committeeRow->id, array_map('strval', $oldCommittees), true))
								>
								<label for="register-committee-{{ $committeeRow->id }}"><span>{{ $committeeRow->name }}</span></label>
							</div>
						@empty
							<p class="tac">등록된 산하위원회가 없습니다. (관리자에게 문의)</p>
						@endforelse
						@foreach ($errors->getMessages() as $committeeErrorKey => $committeeErrorMessages)
							@if (\Illuminate\Support\Str::startsWith($committeeErrorKey, 'committee_codes'))
								@foreach ($committeeErrorMessages as $committeeErrorMessage)
									<p class="c_red" role="alert">{{ $committeeErrorMessage }}</p>
								@endforeach
							@endif
						@endforeach
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
					<div class="checkbox">
						<input type="checkbox" id="register-privacy" name="privacy_agreed" value="1" @checked(old('privacy_agreed'))>
						<label for="register-privacy"><i></i><span><strong class="c_red">(필수)</strong> 개인정보의 수집 및 이용에 동의합니다.</span></label>
					</div>
					@error('privacy_agreed')
						<p class="c_red" role="alert">{{ $message }}</p>
					@enderror
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
					<div class="checkbox">
						<input type="checkbox" id="terms_of_use" name="terms_agreed" value="1" @checked(old('terms_agreed'))>
						<label for="terms_of_use"><i></i><span><strong class="c_red">(필수)</strong> 이용약관에 동의합니다.</span></label>
					</div>
					@error('terms_agreed')
						<p class="c_red" role="alert">{{ $message }}</p>
					@enderror
				</fieldset>

				<div class="btns_btm">
					<button type="button" class="btn btn_kwg js-register-back">뒤로가기</button>
					<button type="submit" class="btn btn_wbb">가입하기</button>
				</div>
			</form>
		</div>

	</div>
</section>

</main>

@endsection

@push('scripts')
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="{{ asset('js/frontend/member-register.js') }}"></script>
@endpush

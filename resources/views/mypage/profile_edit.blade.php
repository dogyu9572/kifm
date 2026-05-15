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
			@include('mypage.partials.annual_fee_card')
			@include('mypage.partials.certification_card')
		</div>

		<div class="register_area" data-mypage-profile-edit>
			<form action="{{ route('mypage.profile_edit.update') }}" method="POST" class="register_form">
				@csrf
				@method('PUT')
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
								<input type="text" id="register-id" class="text" value="{{ $user->login_id }}" readonly>
							</li>
							<li>
								<label for="register-level">회원등급<span class="c_iden">*</span></label>
								<input type="text" id="register-level" class="text" value="{{ $memberLevelLabel }}" readonly>
							</li>
							<li>
								<label for="register-pw">새로운 비밀번호<span class="c_iden">*</span> <p class="abso c_iden" aria-describedby="pw-help">* 비밀번호는 변경을 원하시는 경우에만 입력해 주세요.</p></label>
								<input type="password" id="register-pw" name="password" class="text" placeholder="영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요." autocomplete="new-password">
								@error('password')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-pw-check">새로운 비밀번호 확인*<span class="c_iden">*</span></label>
								<input type="password" id="register-pw-check" name="password_confirmation" class="text" placeholder="영문, 숫자, 특수문자 중 2종류 이상을 조합하여 10자리 이상으로 입력해주세요." autocomplete="new-password">
								@error('password_confirmation')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-name-kor">한글 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-kor" name="name" class="text" placeholder="한글 이름을 입력해 주세요." value="{{ old('name', $user->name) }}" required>
								@error('name')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-name-eng">영문 이름<span class="c_iden">*</span></label>
								<input type="text" id="register-name-eng" name="name_en" class="text" placeholder="영문 이름을 입력해 주세요." value="{{ old('name_en', $user->name_en) }}" required>
								@error('name_en')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-phone">휴대폰 번호<span class="c_iden">*</span></label>
								<input type="text" id="register-phone" name="phone_number" class="text" placeholder="휴대폰 번호를 입력해 주세요." value="{{ old('phone_number', $phoneDisplay) }}" required>
								@error('phone_number')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-email">이메일<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-email" name="email" class="text" placeholder="이메일을 모두 입력해주세요." value="{{ old('email', $user->email) }}" required>
									<button type="button" class="btn btn_wkk" data-check-email data-check-url="{{ route('mypage.profile_edit.check-email') }}">중복 확인</button>
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
						<div class="mytit" aria-hidden="true">추가정보 입력</div>
						<p class="abso c_red">* 은 필수 입력 항목입니다.</p>
					</div>
					<div class="flex_inputs">
						<ul>
							<li>
								<label for="register-type1">구분<span class="c_iden">*</span></label>
								<div class="radios flex">
									@foreach ($jobTypeLabels as $jobValue => $jobLabel)
									<div class="radio">
										<input type="radio" name="job_type" id="register-type-{{ $jobValue }}" value="{{ $jobValue }}" @checked(old('job_type', $user->job_type) === $jobValue) @if($loop->first) required @endif>
										<label for="register-type-{{ $jobValue }}"><i aria-hidden="true"></i><span>{{ $jobLabel }}</span></label>
									</div>
									@endforeach
								</div>
								@error('job_type')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-doctor-num">의사면허번호<span class="c_iden">*</span></label>
								<div class="inbtn">
									<input type="text" id="register-doctor-num" name="license_number" class="text" placeholder="의사면허번호를 입력해주세요." value="{{ old('license_number', $user->license_number) }}" required>
									<button type="button" class="btn btn_wkk" data-check-license data-check-url="{{ route('mypage.profile_edit.check-license') }}">중복 확인</button>
								</div>
								@error('license_number')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-specialty">진료 과목<span class="c_iden">*</span></label>
								<input type="text" id="register-specialty" name="specialty" class="text" value="{{ old('specialty', $user->specialty) }}" placeholder="진료 과목을 입력해 주세요." required>
								@error('specialty')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-company">직장명<span class="c_iden">*</span></label>
								<input type="text" id="register-company" name="workplace_name" class="text" placeholder="직장명을 입력해 주세요." value="{{ old('workplace_name', $user->workplace_name) }}" required>
								@error('workplace_name')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-company-tel">직장전화<span class="c_iden">*</span></label>
								<input type="text" id="register-company-tel" name="workplace_phone" class="text" placeholder="직장 전화번호를 입력해 주세요." value="{{ old('workplace_phone', $user->workplace_phone) }}" required>
								@error('workplace_phone')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li class="w100p">
								<label for="register-company-address">직장주소</label>
								<div class="half_box flex">
									<div class="inbtn">
										<input type="text" id="register-company-address" name="workplace_address" class="text" placeholder="직장주소를 입력해 주세요." value="{{ old('workplace_address', $user->workplace_address) }}">
										<button type="button" class="btn btn_wkk js-register-search-workplace-address">주소검색</button>
									</div>
									<input type="text" id="register-workplace-address-detail" name="workplace_address_detail" class="text half" placeholder="나머지 주소를 입력해 주세요." value="{{ old('workplace_address_detail', $user->workplace_address_detail) }}">
									<input type="hidden" id="register-workplace-zipcode" name="workplace_zipcode" value="{{ old('workplace_zipcode', $user->workplace_zipcode) }}">
								</div>
								@error('workplace_address')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-graduation">학교 졸업년도</label>
								<input type="text" id="register-graduation" name="graduate_year" class="text" placeholder="최종 학교 졸업년도를 숫자로만 입력해 주세요." value="{{ old('graduate_year', $user->graduate_year) }}">
								@error('graduate_year')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="register-university">출신 대학교</label>
								<input type="text" id="register-university" name="school_name" class="text" placeholder="출신 대학교를 입력해 주세요. " value="{{ old('school_name', $user->school_name) }}">
								@error('school_name')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
						</ul>
					</div>
				</fieldset>

				<div class="btns_btm">
					<button type="submit" class="btn btn_wbb">회원정보 수정</button>
					<a href="{{ route('mypage.secession') }}" class="btn btn_kwg">회원탈퇴</a>
				</div>
			</form>
		</div>

	</div>
</section>

@php
    $pendingPayment = ($annualFeeCard['pending_payment'] ?? null);
@endphp
<div class="popup pop_account" id="pop_cancel">
	<div class="dm" data-layer-close="pop_cancel"></div>
	<div class="inbox">
		<button type="button" class="btn_close" data-layer-close="pop_cancel">Close</button>
		<div class="ptit">신청 취소</div>
		<div class="con">
			<div class="gbox">
				신청을 취소하실 경우, 기존 신청 내용은 모두 삭제됩니다.
				<p class="c_iden">*무통장 입금의 경우 영업일 기준 2~3일내로 환불됩니다.</p>
			</div>
			@if ($pendingPayment)
			<div class="payment">
				<dl>
					<div>
						<dt>결제 수단</dt>
						<dd>무통장 입금</dd>
					</div>
					<div>
						<dt>환불 받으실 계좌</dt>
						<dd>
							<p>{{ $pendingPayment->refund_holder_name ?? $user->name }}</p>
							<p>{{ $pendingPayment->refund_bank_name }}</p>
							<p>{{ $pendingPayment->refund_account_no }}</p>
						</dd>
					</div>
				</dl>
			</div>
			@endif
		</div>
		<div class="btns flex_center">
			<form action="{{ route('mypage.membership_payment.cancel') }}" method="POST" class="bo-inline-form">
				@csrf
				<button type="submit" class="btn btn_kwg">신청 취소</button>
			</form>
		</div>
	</div>
</div>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/script_popup.js') }}"></script>
<script src="{{ asset('js/frontend/mypage-profile-edit.js') }}"></script>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
@endpush

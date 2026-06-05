@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="find-id-heading">
	<div class="inner">
		<h1 class="sub_title" id="find-id-heading">{{ $sName }}</h1>

		<form action="{{ route('member.find_id.store') }}" method="POST" class="member_inbox" novalidate>
			@csrf
			<div class="tabs">
				<a href="/member/find_id" class="on">아이디 찾기</a>
				<a href="/member/find_pw">비밀번호 찾기</a>
			</div>
			<div class="gbox tac"><strong>회원가입시 입력하신 입력하신 정보를 입력해주세요</strong></div>
			<div class="inputs_li">
				<h2 class="sound_only">이메일 인증 정보 입력</h2>
				<ul>
					<li>
						<label for="dormant_id" class="tit">이름<span class="c_iden">*</span></label>
						<input type="text" id="dormant_id" name="name" class="text w100p" placeholder="이름을 입력해주세요." value="{{ old('name') }}" required>
					</li>
					<li>
						<label for="dormant_phone" class="tit">휴대폰번호<span class="c_iden">*</span></label>
						<input type="text" id="dormant_phone" name="phone_number" class="text w100p js-account-recovery-phone-input" placeholder="휴대폰번호를 입력해주세요." value="{{ old('phone_number') }}" required inputmode="numeric" autocomplete="tel" maxlength="13">
					</li>
					<li>
						<label for="dormant_email" class="tit">이메일<span class="c_iden">*</span></label>
						<input type="email" id="dormant_email" name="email" class="text w100p" placeholder="이메일을 입력해주세요." value="{{ old('email') }}" required>
					</li>
				</ul>
			</div>
			<button type="submit" class="btn btn_wbb">아이디 찾기</button>
		</form>

	</div>
</section>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/member-account-recovery.js') }}"></script>
@endpush

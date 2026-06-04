@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon register_wrap" aria-labelledby="register-heading">
	<div class="inner">
		<h1 class="sub_title" id="register-heading">{{ $sName }}</h1>
		
		<form action="{{ route('mypage.secession.store') }}" method="POST" class="member_inbox secession_wrap js-mypage-secession-form">
			@csrf
			<div class="top_tit">회원탈퇴 전 아래 내용을<br/>반드시 확인해주세요.</div>
			<div class="gbox">
				회원탈퇴를 신청하시면 아이디는 즉시 탈퇴처리되며 이후 해당 아이디는 영구적으로 사용이 중지되므로 해당 아이디로 재가입이 불가능합니다.<br/>
				회원탈퇴 후, 다른 아이디로 회원가입이 가능합니다.<br/>
				탈퇴와 재가입을 통해 아이디를 교체하면서 선량한 이용자들께 피해를 끼치는 행위를 방지하기 위한 조치이오니 넓은 양해 바랍니다.<br/>
				회원탈퇴 즉시 회원정보는 영구 삭제되며 회원제 서비스와 관련하여 아래의 처리가 완료 됩니다.<br/>
				<div class="dots_list">
					<p>고객님의 개인정보 삭제</p>
				</div>
			</div>
			<div class="checkbox">
				<input type="checkbox" name="secession_agreed" id="save-id" value="1" @checked(old('secession_agreed'))>
				<label for="save-id"><i aria-hidden="true"></i><span><strong class="c_iden">(필수)</strong> 모두 확인하였으며 이에 동의합니다.</span></label>
			</div>
			<div class="inputs_li">
				<h2 class="sound_only">이메일 인증 정보 입력</h2>
				<ul>
					<li>
						<label for="secession_password" class="tit">비밀번호<span class="c_iden">*</span></label>
						<input type="password" name="password" class="text w100p" id="secession_password" placeholder="비밀번호를 입력해 주세요." autocomplete="current-password">
					</li>
					<li>
						<label for="secession_reason" class="tit">탈퇴사유<span class="c_iden">*</span></label>
						<textarea name="withdrawal_reason" id="secession_reason" cols="30" rows="10" class="text w100p" placeholder="탈퇴 사유를 간략하게 입력해 주세요.">{{ old('withdrawal_reason') }}</textarea>
					</li>
				</ul>
			</div>
			<div class="btns_btm flex_colm mt48">
				<button type="submit" class="btn btn_wbb" id="btnWithdraw">회원탈퇴</button>			
				<button type="button" class="btn btn_kwg js-mypage-secession-back">뒤로가기</button>
			</div>
		</form>
		
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/mypage-secession.js') }}"></script>
@endpush

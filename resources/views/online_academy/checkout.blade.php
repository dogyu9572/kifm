@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$price = (int) ($pricing['price'] ?? 0);
@endphp
<main class="sub_area">

<section class="scon academic_event_view_detail training_course_view_wrap" aria-labelledby="online-academy-checkout-heading">
	<h1 id="online-academy-checkout-heading" class="sound_only">온라인 아카데미 결제</h1>
	<div class="inner">
		<div class="inbox">
			<form action="{{ route('online_academy.payment.complete') }}" method="POST" id="online-academy-checkout-form" data-coupon-url="{{ route('online_academy.payment.coupon') }}">
				@csrf
				<input type="hidden" name="course_id" value="{{ $course->id }}">
				<fieldset>
					<legend class="num_tit"><span>1</span>결제 항목 선택</legend>
					<ul class="check_box_line">
						<li>
							<div class="radio">
								<input type="radio" name="course_item" id="online_course_item" value="{{ $course->id }}" data-price="{{ $price }}" checked required>
								<label for="online_course_item"><i aria-hidden="true"></i><span>{{ $course->title }} <strong>{{ number_format($price) }}원</strong></span></label>
							</div>
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<legend class="num_tit"><span>2</span>결제자 정보</legend>
					<ul class="glbox">
						<li>
							<label for="user_name">이름<span class="required">*</span></label>
							<input type="text" id="user_name" name="user_name" class="text" value="{{ $user->name }}" readonly required>
						</li>
						<li>
							<label for="user_email">이메일<span class="required">*</span></label>
							<input type="email" id="user_email" name="user_email" class="text" value="{{ $user->email }}" readonly required>
						</li>
						<li>
							<label for="user_tel">휴대폰번호<span class="required">*</span></label>
							<input type="tel" id="user_tel" name="user_tel" class="text" value="{{ $user->phone_number }}" readonly required>
						</li>
						<li>
							<label for="doctor_license">의사면허번호<span class="required">*</span></label>
							<input type="text" id="doctor_license" name="doctor_license" class="text" value="{{ $user->license_number }}" readonly required>
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<legend class="num_tit"><span>3</span>쿠폰</legend>
					<div class="glbox">
						<div class="inbtn">
							<label for="coupon_num" class="sound_only">쿠폰번호</label>
							<input type="text" id="coupon_num" name="coupon_code" class="text" value="{{ old('coupon_code') }}" placeholder="쿠폰번호를 입력해주세요">
							<button type="button" class="btn btn_wkk" id="online-coupon-apply-btn">등록</button>
						</div>
						@error('coupon_code')
							<p class="c_red" role="alert">{{ $message }}</p>
						@enderror
						<dl class="coupon_sale" id="online-coupon-result">
							<dt>쿠폰 할인 적용</dt>
							<dd>적용된 쿠폰이 없습니다.</dd>
						</dl>
					</div>
				</fieldset>

				<fieldset>
					<legend class="num_tit"><span>4</span>결제수단</legend>
					<div class="glbox">
						<ul class="btns_flex">
							<li class="radio">
								<input type="radio" name="payment_method_display" id="payment_type_card" value="card" checked>
								<label for="payment_type_card"><span>신용카드</span></label>
							</li>
							<li class="radio">
								<input type="radio" name="payment_method_display" id="payment_type_bank" value="bank">
								<label for="payment_type_bank"><span>무통장입금</span></label>
							</li>
						</ul>
						<input type="hidden" name="payment_method" id="online-payment-method" value="card">
						<p class="c_red type_card" role="alert">* 신용카드 결제는 테스트 완료 처리됩니다.</p>
						<div class="type_bank_hide bank_info_area">
							<ul class="long_label">
								<li>
									<label>입금하실 계좌번호</label>
									<div class="text flex">
										<span><strong>국민은행</strong><p>287937-00-000083</p></span>
										<span><strong>예금주</strong><p>대한기능의학회</p></span>
									</div>
									<input type="hidden" name="bank_account_text" value="국민은행 287937-00-000083 / 예금주 대한기능의학회">
								</li>
								<li>
									<label for="bank_depositor">입금자명<span class="required">*</span></label>
									<input type="text" id="bank_depositor" name="bank_depositor" class="text" value="{{ old('bank_depositor', $user->name) }}" placeholder="입금자명을 입력해 주세요">
									@error('bank_depositor')
										<p class="c_red" role="alert">{{ $message }}</p>
									@enderror
								</li>
								<li>
									<label for="bank_deposit_date">입금 예정일<span class="required">*</span></label>
									<input type="date" id="bank_deposit_date" name="bank_deposit_date" class="text" value="{{ old('bank_deposit_date', now()->toDateString()) }}">
									@error('bank_deposit_date')
										<p class="c_red" role="alert">{{ $message }}</p>
									@enderror
								</li>
							</ul>
							<p class="c_red" role="alert">* 온라인 입금의 경우 입금확인 후 승인처리까지 하루에서 이틀정도의 시간이 소요됩니다.</p>
						</div>
					</div>
				</fieldset>

				<fieldset class="type_bank_hide">
					<legend class="num_tit"><span>5</span>현금 영수증 발행</legend>
					<div class="glbox">
						<ul class="btns_flex">
							<li class="radio">
								<input type="radio" name="receipt_issue" id="cash_receipt_non" value="NO" @checked(old('receipt_issue', 'NO') === 'NO')>
								<label for="cash_receipt_non"><span>미발행</span></label>
							</li>
							<li class="radio">
								<input type="radio" name="receipt_issue" id="cash_receipt" value="YES" @checked(old('receipt_issue') === 'YES')>
								<label for="cash_receipt"><span>발행</span></label>
							</li>
						</ul>
						<ul class="bdt long_label cash_receipt_area">
							<li>
								<label for="receipt_type">발급 구분</label>
								<select name="receipt_type" id="receipt_type" class="text">
									<option value="PERSONAL" @selected(old('receipt_type') === 'PERSONAL')>개인소득공제용</option>
									<option value="CARD" @selected(old('receipt_type') === 'CARD')>사업자증빙용</option>
								</select>
							</li>
							<li>
								<label for="receipt_number">현금영수증 번호<span class="required">*</span></label>
								<input type="text" id="receipt_number" name="receipt_number" class="text" value="{{ old('receipt_number', $user->phone_number) }}">
								@error('receipt_number')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
						</ul>
					</div>
				</fieldset>

				<article class="abso_application">
					<h2 class="tit">결제정보</h2>
					<p class="selected_item" id="online-summary-items">{{ $course->title }}</p>
					<dl class="price_info">
						<div>
							<dt>결제금액</dt>
							<dd><strong id="online-summary-subtotal">{{ number_format($price) }}</strong>원</dd>
						</div>
						<div>
							<dt>쿠폰 할인</dt>
							<dd><strong id="online-summary-discount">-0</strong>원</dd>
						</div>
						<div class="total">
							<dt>최종 결제 금액</dt>
							<dd><strong id="online-summary-total">{{ number_format($price) }}</strong>원</dd>
						</div>
					</dl>
					<div class="check_area checkbox">
						<input type="checkbox" name="terms_agree" id="terms_agree" value="1" required @checked(old('terms_agree'))>
						<label for="terms_agree"><i></i><span>[필수] 결제 이용 약관, 개인정보 처리 동의</span></label>
					</div>
					@error('terms_agree')
						<p class="c_red" role="alert">{{ $message }}</p>
					@enderror
					<button type="submit" class="btn_submit btn_wbb"><span class="sound_only" id="online-submit-amount">{{ number_format($price) }}원 </span>결제하기</button>
					<button type="button" class="btn_cancel btn_kwg" data-history-back>결제취소</button>
				</article>
			</form>
		</div>
	</div>
</section>

</main>
@endsection

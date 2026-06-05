@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
    $member = $currentMember ?? null;
    $selectedPlanIds = collect(old('payment_plan_ids', $paymentPlans->pluck('id')->take(1)->all()))
        ->map(fn ($id) => (int) $id)
        ->all();
    $subtotal = $paymentPlans
        ->whereIn('id', $selectedPlanIds)
        ->sum(fn ($plan) => (int) $plan->price_early);
@endphp
<main class="sub_area">

<section class="scon registration_form_wrap" aria-labelledby="registration-form-heading">
	<div class="inner">
		<h1 class="sound_only" id="registration-form-heading">{{ $sName }}</h1>
        <div class="inbox">
            @if ($errors->any())
                <div class="gbox">
                    <p class="c_red" role="alert">입력 내용을 확인해주세요.</p>
                </div>
            @endif
			<form action="{{ route('academic_conference.site.registration.store', $event->folder_name) }}" method="post" id="academic-registration-form" data-coupon-url="{{ route('academic_conference.site.registration.coupon', $event->folder_name) }}">
                @csrf
                <fieldset>
                    <legend class="form_tit mt0">결제자 정보</legend>
                    <ul class="inputs">
                        <li>
                            <label for="user_name">이름(국문)<span class="required">*</span></label>
                            <input type="text" id="user_name" name="name" class="text" value="{{ $member?->name }}" placeholder="이름을 입력해주세요" required title="이름 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="user_email">이메일<span class="required">*</span></label>
                            <input type="email" id="user_email" name="email" class="text" value="{{ old('email', $member?->email) }}" placeholder="이메일을 입력해주세요" required>
                            @error('email')
                                <p class="c_red" role="alert">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="user_tel">휴대폰번호<span class="required">*</span></label>
                            <input type="tel" id="user_tel" name="phone" class="text" value="{{ old('phone', $member?->phone_number) }}" placeholder="휴대폰 번호를 입력해주세요" required>
                            @error('phone')
                                <p class="c_red" role="alert">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="user_name_eng">이름(영문)<span class="required">*</span></label>
                            <input type="text" id="user_name_eng" name="name_en" class="text" value="{{ $member?->name_en }}" placeholder="영문이름을 입력해주세요" required title="영문이름 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="doctor_license">면허번호<span class="required">*</span></label>
							<input type="text" id="doctor_license" name="license_no" class="text" value="{{ $member?->license_number }}" placeholder="면허번호를 입력해주세요" required title="면허번호 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="major_subject">전공과목<span class="required">*</span></label>
							<input type="text" id="major_subject" name="major_subject" class="text" value="{{ $member?->medical_department ?: $member?->specialty }}" placeholder="전공과목을 입력해주세요" required title="전공과목 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="affiliated_hospital">소속병의원명<span class="required">*</span></label>
							<input type="text" id="affiliated_hospital" name="affiliated_hospital" class="text" value="{{ $member?->workplace_name }}" placeholder="소속병의원명을 입력해주세요" required title="소속병의원명 입력 필수" readonly>
                            <input type="hidden" name="workplace_phone" value="{{ $member?->workplace_phone }}">
                        </li>
                        <li>
                            <label for="address">직장 주소<span class="required">*</span></label>
							<div class="inbtn">
								<input type="text" id="address_postcode" name="address_postcode" class="text" value="{{ $member?->address_postcode ?: $member?->workplace_zipcode }}" placeholder="우편번호를 입력해주세요" required title="우편번호 입력 필수" readonly>
								<button type="button" class="btn btn_wkk">주소 확인</button>
							</div>
							<input type="text" id="address_base" name="address_base" class="text" value="{{ $member?->address_base ?: $member?->workplace_address }}" placeholder="주소를 입력해주세요" required title="주소 입력 필수" readonly>
							<input type="text" id="address_detail" name="address_detail" class="text" value="{{ $member?->address_detail ?: $member?->workplace_address_detail }}" placeholder="상세주소를 입력해주세요" required title="상세주소 입력 필수">
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="form_tit">결제 항목 선택</legend>
					<ul class="radios float">
                        @forelse ($paymentPlans as $plan)
                            <li>
                                <div class="checkbox">
                                    <input type="checkbox" name="payment_plan_ids[]" id="payment_plan_{{ $plan->id }}" value="{{ $plan->id }}" data-price="{{ (int) $plan->price_early }}" data-label="{{ $plan->plan_name }}" @checked(in_array((int) $plan->id, $selectedPlanIds, true))>
                                    <label for="payment_plan_{{ $plan->id }}"><i aria-hidden="true"></i><span>{{ $plan->plan_name }} <strong>{{ number_format((int) $plan->price_early) }}원</strong></span></label>
                                </div>
                            </li>
                        @empty
                            <li>
                                <p class="c_red">회원정보와 일치하는 결제 항목이 없습니다. 관리자에게 문의해주세요.</p>
                            </li>
                        @endforelse
					</ul>
                    @error('payment_plan_ids')
                        <p class="c_red" role="alert">{{ $message }}</p>
                    @enderror
                </fieldset>

                <fieldset>
                    <legend class="form_tit">쿠폰 등록</legend>
                    <div class="inputs">
                        <div class="inbtn">
                            <label for="coupon_num" class="sound_only">쿠폰번호</label>
                            <input type="text" id="coupon_num" name="coupon_code" class="text" value="{{ old('coupon_code') }}" placeholder="쿠폰번호를 입력해주세요">
                            <button type="button" class="btn btn_wkk" id="academic-coupon-apply-btn">등록</button>
                        </div>
                        @error('coupon_code')
                            <p class="c_red" role="alert">{{ $message }}</p>
                        @enderror
						<dl class="coupon_sale" id="academic-coupon-result">
							<dt>쿠폰 할인 적용</dt>
							<dd>적용된 쿠폰이 없습니다.</dd>
						</dl>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="form_tit">결제 수단 선택</legend>
                    <div class="inputs">
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
                        <input type="hidden" name="payment_method" id="academic-payment-method" value="">
						<p class="c_red type_card" role="alert">* 신용카드 결제는 토스페이먼츠 테스트 결제창으로 진행됩니다.</p>
                        @error('payment')
                            <p class="c_red" role="alert">{{ $message }}</p>
                        @enderror

						<div class="type_bank_hide bank_info_area">
                            <ul>
								<li>
									<label>입금하실 계좌번호</label>
		                            <div class="text flex">
										<span><strong>국민은행</strong><p>287937-00-000083</p></span>
										<span><strong>예금주</strong><p>대한기능의학회</p></span>
									</div>
                                    <input type="hidden" name="bank_account_text" value="국민은행 287937-00-000083 / 예금주 대한기능의학회">
								</li>
								<li>
									<label for="name">입금자명<span class="required">*</span></label>
		                            <input type="text" id="name" name="bank_depositor" class="text" value="{{ old('bank_depositor', $member?->name) }}" placeholder="입금자명을 입력해 주세요">
                                    @error('bank_depositor')
                                        <p class="c_red" role="alert">{{ $message }}</p>
                                    @enderror
								</li>
								<li>
									<label for="date">입금 예정일<span class="required">*</span></label>
		                            <input type="date" id="date" name="bank_deposit_date" class="text" value="{{ old('bank_deposit_date', now()->toDateString()) }}">
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
                    <legend class="form_tit">현금 영수증 발행</legend>
                    <div class="inputs">
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
								<input type="text" id="receipt_number" name="receipt_number" class="text" value="{{ old('receipt_number', $member?->phone_number) }}">
                                @error('receipt_number')
                                    <p class="c_red" role="alert">{{ $message }}</p>
                                @enderror
							</li>
						</ul>
					</div>
                </fieldset>

                <article class="abso_application">
					<div class="mobile_opcl" aria-hidden="true"></div>
                    <h2 class="tit">결제자 정보</h2>
                    <dl class="price_info">
                        <div>
                            <dt>결제 항목</dt>
                            <dd id="academic-summary-items">-</dd>
                        </div>
                        <div>
                            <dt>결제 금액</dt>
                            <dd><strong id="academic-summary-subtotal">{{ number_format((int) $subtotal) }}</strong>원</dd>
                        </div>
                        <div>
                            <dt>할인 금액</dt>
                            <dd><strong class="c_iden" id="academic-summary-discount">0</strong>원</dd>
                        </div>
                        <div class="total">
                            <dt>최종 결제 금액</dt>
                            <dd><strong id="academic-summary-total">{{ number_format((int) $subtotal) }}</strong>원</dd>
                        </div>
                    </dl>

                    <div class="check_area checkbox">
                        <input type="checkbox" name="terms_agree" id="terms_agree" value="1" required @checked(old('terms_agree'))>
                        <label for="terms_agree"><i></i><span>[필수] 결제 이용 약관, 개인정보 처리 동의</span></label>
						<button type="button" class="view" data-popup-open="pop_terms">보기</button>
                    </div>
                    @error('terms_agree')
                        <p class="c_red" role="alert">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn_submit btn_wbb"><span class="sound_only" id="academic-submit-amount">{{ number_format((int) $subtotal) }}원 </span>등록 신청하기</button>
                </article>
            </form>
        </div>
	</div>
</section>

</main>

<div class="popup pop_terms" id="pop_terms">
	<div class="dm" data-popup-close="pop_terms"></div>
	<div class="inbox">
		<button type="button" class="btn_close" data-popup-close="pop_terms">Close</button>
		<h2 class="ptit">결제 이용 약관, 개인정보 처리</h2>
		<div class="gbox">
			<div class="scroll">
				내용
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-registration-form.js') }}"></script>
@endpush

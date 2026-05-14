@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon registration_form_wrap" aria-labelledby="registration-form-heading">
	<div class="inner">
		<h1 class="sound_only" id="registration-form-heading">{{ $sName }}</h1>
        <div class="inbox">
			<form action="/payment/process" method="post">
                <fieldset>
                    <legend class="form_tit mt0">결제자 정보</legend>
                    <ul class="inputs">
                        <li>
                            <label for="user_name">이름(국문)<span class="required">*</span></label>
                            <input type="text" id="user_name" name="user_name" class="text" placeholder="이름을 입력해주세요" required title="이름 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="user_email">이메일<span class="required">*</span></label>
                            <input type="email" id="user_email" name="user_email" class="text" placeholder="이메일을 입력해주세요" required>
                        </li>
                        <li>
                            <label for="user_tel">휴대폰번호<span class="required">*</span></label>
                            <input type="tel" id="user_tel" name="user_tel" class="text" placeholder="휴대폰 번호를 입력해주세요" required>
                        </li>
                        <li>
                            <label for="user_name_eng">이름(영문)<span class="required">*</span></label>
                            <input type="text" id="user_name_eng" name="user_name" class="text" placeholder="영문이름을 입력해주세요" required title="영문이름 입력 필수">
                        </li>
                        <li>
                            <label for="doctor_license">면허번호<span class="required">*</span></label>
							<input type="text" id="doctor_license" name="doctor_license" class="text" value="123456" placeholder="면허번호를 입력해주세요" required title="면허번호 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="major_subject">전공과목<span class="required">*</span></label>
							<input type="text" id="major_subject" name="major_subject" class="text" placeholder="전공과목을 입력해주세요" required title="전공과목 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="affiliated_hospital">소속병의원명<span class="required">*</span></label>
							<input type="text" id="affiliated_hospital" name="affiliated_hospital" class="text" placeholder="소속병의원명을 입력해주세요" required title="소속병의원명 입력 필수" readonly>
                        </li>
                        <li>
                            <label for="address">주소<span class="required">*</span></label>
							<div class="inbtn">
								<input type="text" id="address" name="address" class="text" placeholder="우편번호를 입력해주세요" required title="우편번호 입력 필수" readonly>
								<button type="button" class="btn btn_wkk">주소 확인</button>
							</div>
							<input type="text" id="address" name="address" class="text" placeholder="주소를 입력해주세요" required title="주소 입력 필수" readonly>
							<input type="text" id="address" name="address" class="text" placeholder="상세주소를 입력해주세요" required title="상세주소 입력 필수">
                        </li>
                        <li>
                            <label for="member_type1">회원 구분</label>
							<div class="member_type_select">
								<div class="radio"><input type="radio" name="member_type" id="member_type1"><label for="member_type1"><span>미선택</span></label></div>
								<div class="radio"><input type="radio" name="member_type" id="member_type2"><label for="member_type2"><span>전공의</span></label></div>
								<div class="radio"><input type="radio" name="member_type" id="member_type3"><label for="member_type3"><span>공보의</span></label></div>
								<div class="radio"><input type="radio" name="member_type" id="member_type4"><label for="member_type4"><span>군의관</span></label></div>
								<div class="radio"><input type="radio" name="member_type" id="member_type5"><label for="member_type5"><span>간호사</span></label></div>
							</div>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="form_tit">결제 항목 선택</legend>
					<ul class="radios float">
						<li>
							<div class="radio">
								<input type="radio" name="payment_select" id="payment_select12" checked>
								<label for="payment_select12"><i aria-hidden="true"></i><span>정회원 연회비 <strong>120,000원</strong></span></label>
							</div>
						</li>
					</ul>
                </fieldset>

                <fieldset>
                    <legend class="form_tit">쿠폰 등록</legend>
                    <div class="inputs">
                        <div class="inbtn">
                            <label for="coupon_num" class="sound_only">쿠폰번호</label>
                            <input type="text" id="coupon_num" name="coupon_num" class="text" placeholder="쿠폰번호를 입력해주세요">
                            <button type="button" class="btn btn_wkk">등록</button>
                        </div>
						<dl class="coupon_sale">
							<dt>쿠폰 할인 적용</dt>
							<dd>‘후원사 전용 50% 할인’</dd>
						</dl>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="form_tit">결제 수단 선택</legend>
                    <div class="inputs">
                        <ul class="btns_flex">
                            <li class="radio">
                                <input type="radio" name="payment_type" id="payment_type_card" value="card" checked>
                                <label for="payment_type_card"><span>신용카드</span></label>
                            </li>
                            <li class="radio">
                                <input type="radio" name="payment_type" id="payment_type_bank" value="bank">
                                <label for="payment_type_bank"><span>무통장입금</span></label>
                            </li>
                        </ul>
						<p class="c_red type_card" role="alert">* 카드전표는 등록하신 이메일로 자동발송됩니다.</p>
                        
						<div class="type_bank_hide bank_info_area">
							<ul>
								<li>
									<label>입금하실 계좌번호</label>
		                            <div class="text flex">
										<span><strong>국민은행</strong><p>287937-00-000083</p></span>
										<span><strong>예금주</strong><p>대한기능의학회</p></span>
									</div>
								</li>
								<li>
									<label for="name">입금자명<span class="required">*</span></label>
		                            <input type="text" id="name" name="name" class="text" placeholder="입금자명을 입력해 주세요">
								</li>
								<li>
									<label for="date">입금 예정일<span class="required">*</span></label>
		                            <input type="text" id="date" name="date" class="text" placeholder="2026-02-15">
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
                                <input type="radio" name="cash_receipt" id="cash_receipt_non" value="미발행" checked>
                                <label for="cash_receipt_non"><span>미발행</span></label>
                            </li>
                            <li class="radio">
                                <input type="radio" name="cash_receipt" id="cash_receipt" value="발행">
                                <label for="cash_receipt"><span>발행</span></label>
                            </li>
                        </ul>
                        
						<ul class="bdt long_label cash_receipt_area">
							<li>
								<label for="receipt_type">발급 구분</label>
								<select name="receipt_type" id="receipt_type" class="text">
									<option value="">개인소득공제용</option>
									<option value="">사업자증빙용</option>
									<option value="">미발급</option>
								</select>
							</li>
							<li>
								<label for="receipt_phone">휴대폰번호<span class="required">*</span></label>
								<input type="text" id="receipt_phone" name="receipt_phone" class="text" value="010-1234-5678" required>
							</li>
							<li>
								<label for="receipt_card">현금영수증 카드번호<span class="required">*</span></label>
								<input type="text" id="receipt_card" name="receipt_card" class="text" value="123456" required>
							</li>
						</ul>
					</div>
                </fieldset>

                <article class="abso_application">
                    <h2 class="tit">결제자 정보</h2>
                    <p class="selected_item">대한기능의학회 통계 심화 연수강좌 - 1차시</p>
                    <dl class="price_info">
                        <div>
                            <dt>결제 항목</dt>
                            <dd>정회원 연회비 사전등록비 (정회원)</dd>
                        </div>
                        <div>
                            <dt>결제 금액</dt>
                            <dd><strong>250,000</strong>원</dd>
                        </div>
                        <div>
                            <dt>할인 금액</dt>
                            <dd><strong class="c_iden">-10,000</strong>원</dd>
                        </div>
                        <div class="total">
                            <dt>최종 결제 금액</dt>
                            <dd><strong>240,000</strong>원</dd>
                        </div>
                    </dl>
                    
                    <div class="check_area checkbox">
                        <input type="checkbox" name="terms_agree" id="terms_agree" required>
                        <label for="terms_agree"><i></i><span>[필수] 결제 이용 약관, 개인정보 처리 동의</span></label>
						<button type="button" class="view" onclick="layerShow('pop_terms');">보기</button>
                    </div>
                    <button type="submit" class="btn_submit btn_wbb" onclick="location.href='/academic_conference/registration/end'"><span class="sound_only">240,000원 </span>결제하기</button>
                </article>
            </form>
        </div>
	</div>
</section>

</main>

<div class="popup pop_terms" id="pop_terms">
	<div class="dm" onclick="layerHide('pop_terms');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_terms');">Close</button>
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
<script src="{{ asset('js/script_popup.js') }}"></script>
<script>
// abso_application 고정
    const $window = $(window);
    const $header = $('header');
    const $viewTop = $('.academic_event_view_top');
    const $detail = $('.registration_form_wrap');
    const $absoApp = $('.abso_application');
    $window.on('scroll.stickyApp', function() {
        const scrollTop = $window.scrollTop();
        const headerHeight = $header.outerHeight();
        const topMargin = parseInt($viewTop.css('margin-bottom')) || 0;
        const detailOffsetTop = $detail.offset().top;
        const detailHeight = $detail.outerHeight();
        const appHeight = $absoApp.outerHeight();
        const fixStartPoint = detailOffsetTop - 120; 
        const unfixPoint = (detailOffsetTop + detailHeight) - appHeight - 120;
        if (scrollTop >= fixStartPoint) {
            if (scrollTop >= unfixPoint) {
                $detail.addClass('unfixed').removeClass('fixed');
            } else {
                $detail.addClass('fixed').removeClass('unfixed');
            }
        } else {
            $detail.removeClass('fixed unfixed');
        }
    });
    $window.trigger('scroll.stickyApp');

//무통장입금
	const paymentRadios = document.querySelectorAll('input[name="payment_type"]');
	const cashRadios = document.querySelectorAll('input[name="cash_receipt"]');
	const bankElements = document.querySelectorAll('.type_bank_hide');
	const cardElements = document.querySelectorAll('.type_card');
	const cashArea = document.querySelector('.cash_receipt_area');
	function handlePaymentChange() {
		const isBank = document.getElementById('payment_type_bank').checked;
		bankElements.forEach(el => el.style.display = isBank ? 'block' : 'none');
		cardElements.forEach(el => el.style.display = isBank ? 'none' : 'block');
		if (!isBank) {
			document.getElementById('cash_receipt_non').checked = true;
			handleCashReceiptChange(); 
		}
		$(window).trigger('scroll.stickyApp');
	}
	function handleCashReceiptChange() {
		const isReceipt = document.getElementById('cash_receipt').checked;
		cashArea.style.display = isReceipt ? 'block' : 'none';
		$(window).trigger('scroll.stickyApp');
	}
	paymentRadios.forEach(radio => radio.addEventListener('change', handlePaymentChange));
	cashRadios.forEach(radio => radio.addEventListener('change', handleCashReceiptChange));
	handlePaymentChange();
	handleCashReceiptChange();
</script>
@endpush@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>
	</div>
</section>

</main>
@endsection
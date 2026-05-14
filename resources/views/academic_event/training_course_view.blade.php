@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_detail training_course_view_wrap" aria-labelledby="training-course-detail-heading">
    <h1 id="training-course-detail-heading" class="sound_only">2026년 2월 8일 - 심화 연수강좌</h1>
    <div class="inner">
        <div class="inbox">
        	<div class="con_area">
        		<a href="/member/login" class="login_prompt gbox">
					<h2>학회 회원이신가요?</h2>
					<p>로그인하시면 더 많은 혜택을 받으실 수 있습니다.</p>
				</a>
        	</div>
			
			<form action="/payment/process" method="post">
                <fieldset>
                    <legend class="num_tit"><span>1</span>결제 항목 선택</legend>
                    <ul class="check_box_line">
                        <li class="end">
                            <div class="checkbox">
                            	<input type="checkbox" name="payment_select" id="payment_select1" disabled>
                            	<label for="payment_select1"><i aria-hidden="true"></i><span>대한기능의학회 통계 심화 연수강좌 - 전체 차시<em class="end_tag">정원 마감</em><strong>200,000원</strong></span></label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                            	<input type="checkbox" name="payment_select" id="payment_select2">
                            	<label for="payment_select2"><i aria-hidden="true"></i><span>대한기능의학회 통계 심화 연수강좌 - 개별 차시 (기초반) <strong>100,000원</strong></span></label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                            	<input type="checkbox" name="payment_select" id="payment_select3">
                            	<label for="payment_select3"><i aria-hidden="true"></i><span>대한기능의학회 통계 심화 연수강좌 - 개별 차시 (심화반) <strong>100,000원</strong></span></label>
                            </div>
                        </li>
                        <li class="end">
                            <div class="checkbox">
                            	<input type="checkbox" name="payment_select" id="payment_select4" disabled>
                            	<label for="payment_select4"><i aria-hidden="true"></i><span>대한기능의학회 통계 심화 연수강좌 - 오프라인 실습권<em class="end_tag">정원 마감</em><strong>100,000원</strong></span></label>
                            </div>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>2</span>결제자 정보</legend>
                    <ul class="glbox">
                        <li>
                            <label for="user_name">이름<span class="required">*</span></label>
                            <input type="text" id="user_name" name="user_name" class="text" placeholder="이름을 입력해주세요" required title="이름 입력 필수">
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
                            <label for="doctor_license">의사면허번호<span class="required">*</span></label>
                            <div class="inbtn">
                                <input type="text" id="doctor_license" name="doctor_license" class="text" placeholder="의사면허번호를 입력해주세요" required title="의사면허번호 입력 필수">
                                <button type="button" class="btn btn_wkk">중복 확인</button>
                            </div>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>3</span>쿠폰 등록</legend>
                    <div class="glbox">
                        <div class="inbtn">
                            <label for="coupon_num" class="sound_only">쿠폰번호</label>
                            <input type="text" id="coupon_num" name="coupon_num" class="text" placeholder="쿠폰번호를 입력해주세요">
                            <button type="button" class="btn btn_wkk">쿠폰등록</button>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>4</span>결제수단</legend>
                    <div class="glbox">
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
                        <p class="c_red type_bank_hide" role="alert">* 온라인 입금의 경우 입금확인 후 승인처리까지 하루에서 이틀정도의 시간이 소요됩니다.</p>
                        
						<div class="type_bank_hide">
							<ul class="long_label">
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
						</div>
                    </div>
                </fieldset>

                <fieldset class="type_bank_hide">
                    <legend class="num_tit"><span>5</span>환불정보</legend>
                    <ul class="glbox long_label">
                        <li>
                            <label for="bank_type">은행명/계좌번호</label>
                            <div class="flex bank text">
								<select name="" id="bank_type" class="text">
									<option value="">국민은행</option>
								</select>
                            	<input type="text" id="bank_number" name="bank_number" class="text" placeholder="111111-22-333333">
                            </div>
                        </li>
                        <li>
                            <label for="bank_name">예금주명</label>
                            <input type="email" id="bank_name" name="bank_name" class="text" placeholder="이메일을 입력해주세요" required>
                        </li>
                    </ul>
					<!-- <div class="float mt">
						<p class="excl"><strong>환불 규정 안내</strong><br/>
							사전등록 마감일 전 환불 신청 시: 사전등록비 전액 환불 가능<br/>
							사전등록 마감 후 환불 신청 시: 환불 불가
						</p>
						<p class="excl"><strong>불참 시 안내</strong><br/>
							부득이하게 학술대회 당일 참석이 어려우신 경우, 강의록을 우편으로 발송해 드립니다.
						</p>
					</div> -->
                </fieldset>

                <fieldset class="type_bank_hide">
                    <legend class="num_tit"><span>6</span>현금 영수증 발행</legend>
                    <div class="glbox">
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
                    <h2 class="tit">최종 결제 확인</h2>
                    <p class="selected_item">대한기능의학회 통계 심화 연수강좌 - 1차시</p>
                    <dl class="price_info">
                        <div>
                            <dt>결제금액</dt>
                            <dd><strong>100,000</strong>원</dd>
                        </div>
                        <div>
                            <dt>쿠폰 할인</dt>
                            <dd><strong class="c_iden">- 10,000</strong>원</dd>
                        </div>
                        <div class="total">
                            <dt>최종 결제 금액</dt>
                            <dd><strong>90,000</strong>원</dd>
                        </div>
                    </dl>
                    
                    <div class="check_area checkbox">
                        <input type="checkbox" name="terms_agree" id="terms_agree" required>
                        <label for="terms_agree"><i></i><span>[필수] 결제 이용 약관, 개인정보 처리 동의</span></label>
                    </div>
                    <button type="submit" class="btn_submit btn_wbb" onclick="location.href='/academic_event/training_course/end'"><span class="sound_only">90,000원 </span>결제하기</button>
                </article>
            </form>
        </div>
	</div>
</section>

</main>

@endsection

@push('scripts')
<script>
// abso_application 고정
    const $window = $(window);
    const $header = $('header');
    const $viewTop = $('.academic_event_view_top');
    const $detail = $('.academic_event_view_detail');
    const $absoApp = $('.abso_application');
    $window.on('scroll.stickyApp', function() {
        const scrollTop = $window.scrollTop();
        const headerHeight = $header.outerHeight();
        const topMargin = parseInt($viewTop.css('margin-bottom')) || 0;
        const detailOffsetTop = $detail.offset().top;
        const detailHeight = $detail.outerHeight();
        const appHeight = $absoApp.outerHeight();
        const fixStartPoint = detailOffsetTop - 190; 
        const unfixPoint = (detailOffsetTop + detailHeight) - appHeight - 190;
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
@endpush
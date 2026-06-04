@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_detail training_course_view_wrap" aria-labelledby="annual_fee-heading">
    <h1 id="annual_fee-heading" class="sound_only">연회비 납부</h1>
    <div class="inner">
        <div class="inbox">
			
			<form action="{{ route('mypage.annual_fee.store') }}" method="POST">
                @csrf
                <fieldset>
                    <legend class="num_tit"><span>1</span>결제 항목 선택</legend>
                    <ul class="check_box_line">
                        <li>
                            <div class="radio">
                            	<input type="radio" name="membership_plan_id" id="payment_select1" value="{{ $plans[0]['id'] ?? '' }}" @checked(true) required>
                            	<label for="payment_select1"><i aria-hidden="true"></i><span>{{ $plans[0]['label'] ?? '연회비' }} <strong>{{ number_format($plans[0]['amount'] ?? 0) }}원</strong></span></label>
                            </div>
                        </li>
                        <li>
                            <div class="radio">
                            	@if (isset($plans[1]))
                            	<input type="radio" name="membership_plan_id" id="payment_select2" value="{{ $plans[1]['id'] }}">
                            	<label for="payment_select2"><i aria-hidden="true"></i><span>{{ $plans[1]['label'] }} <strong>{{ number_format($plans[1]['amount']) }}원</strong></span></label>
                            	@endif
                            </div>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>2</span>결제자 정보</legend>
                    <ul class="glbox">
                        <li>
                            <label for="user_name">이름<span class="required">*</span></label>
                            <input type="text" id="user_name" name="user_name" class="text" placeholder="이름을 입력해주세요" value="{{ $user->name }}" readonly required title="이름 입력 필수">
                        </li>
                        <li>
                            <label for="user_email">이메일<span class="required">*</span></label>
                            <input type="email" id="user_email" name="user_email" class="text" placeholder="이메일을 입력해주세요" required>
                        </li>
                        <li>
                            <label for="user_tel">휴대폰번호<span class="required">*</span></label>
                            <input type="tel" id="user_tel" name="user_tel" class="text" placeholder="휴대폰 번호를 입력해주세요" value="{{ $user->phone_number }}" readonly required>
                        </li>
                        <li>
                            <label for="doctor_license">의사면허번호<span class="required">*</span></label>
                            <input type="text" id="doctor_license" name="doctor_license" class="text" placeholder="의사면허번호를 입력해주세요" value="{{ $user->license_number }}" readonly required title="의사면허번호 입력 필수">
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>3</span>결제수단</legend>
                    <div class="glbox">
                        <ul class="btns_flex">
                            <li class="radio">
                                <input type="radio" name="payment_method" id="payment_type_card" value="card" checked>
                                <label for="payment_type_card"><span>신용카드</span></label>
                            </li>
                            <li class="radio">
                                <input type="radio" name="payment_method" id="payment_type_bank" value="bank_transfer">
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
                    <legend class="num_tit"><span>4</span>환불정보</legend>
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
                            <label for="user_email">예금주명</label>
                            <input type="email" id="user_email" name="user_email" class="text" placeholder="이메일을 입력해주세요" required>
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
                    <legend class="num_tit"><span>5</span>현금 영수증 발행</legend>
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
								<label for="name">발급 구분</label>
								<input type="text" id="name" name="name" class="text" value="개인소득공제용">
							</li>
							<li>
								<label for="name">휴대폰번호<span class="required">*</span></label>
								<input type="text" id="name" name="name" class="text" value="010-1234-5678" required>
							</li>
							<li>
								<label for="date">현금영수증 카드번호<span class="required">*</span></label>
								<input type="text" id="date" name="date" class="text" value="123456" required>
							</li>
						</ul>
					</div>
                </fieldset>

                <article class="abso_application">
					<div class="mobile_opcl" aria-hidden="true"></div>
                    <h2 class="tit">결제정보</h2>
                    <p class="selected_item">정회원 연회비</p>
                    <dl class="price_info">
                        <div>
                            <dt>결제금액</dt>
                            <dd><strong>250,000</strong>원</dd>
                        </div>
                        <div class="total">
                            <dt>최종 결제 금액</dt>
                            <dd><strong>250,000</strong>원</dd>
                        </div>
                    </dl>
                    
                    <div class="check_area checkbox">
                        <input type="checkbox" name="terms_agree" id="terms_agree" required>
                        <label for="terms_agree"><i></i><span>[필수] 결제 이용 약관, 개인정보 처리 동의</span></label>
                    </div>
                    <button type="submit" class="btn_submit btn_wgg"><span class="sound_only" id="training-submit-amount">250,000원 </span>결제 약관에 동의해주세요.</button>
					<button type="button" class="btn_cancel btn_kwg" data-history-back>뒤로가기</button>
				</article>
			</form>
		</div>

	</div>

</section>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/mypage-annual-fee.js') }}"></script>
@endpush

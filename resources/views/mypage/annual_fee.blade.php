@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@php
    $selectedPlanId = (int) old('membership_plan_id', $plans[0]['id'] ?? 0);
    $selectedPlan = collect($plans)->firstWhere('id', $selectedPlanId) ?? ($plans[0] ?? ['id' => '', 'label' => '연회비', 'amount' => 0]);
    $selectedPlanLabel = $selectedPlan['label'] ?? '연회비';
    $selectedPlanAmount = (int) ($selectedPlan['amount'] ?? 0);
    $bankName = (string) config('mypage.membership_bank_display_name');
    $bankAccount = (string) config('mypage.membership_bank_account_no');
    $bankHolder = (string) config('mypage.membership_bank_holder');
	$refundBankOptions = ['국민은행', '신한은행', '우리은행', '하나은행', '농협은행', '기업은행', '카카오뱅크', '토스뱅크', '케이뱅크', 'SC제일은행', '씨티은행', '새마을금고', '신협', '우체국'];
@endphp
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_detail training_course_view_wrap" aria-labelledby="annual_fee-heading">
    <h1 id="annual_fee-heading" class="sound_only">연회비 납부</h1>
    <div class="inner">
        <div class="inbox">

			<form action="{{ route('mypage.annual_fee.store') }}" method="POST" data-annual-fee-form>
                @csrf
                <fieldset>
                    <legend class="num_tit"><span>1</span>결제 항목 선택</legend>
                    <ul class="check_box_line">
                        @forelse ($plans as $idx => $plan)
                            <li>
                                <div class="radio">
                                    <input type="radio" name="membership_plan_id" id="payment_select{{ $idx + 1 }}" value="{{ $plan['id'] }}" data-plan-label="{{ $plan['label'] }}" data-plan-amount="{{ (int) $plan['amount'] }}" @checked((int) old('membership_plan_id', $selectedPlanId) === (int) $plan['id']) @if($loop->first) required @endif>
                                    <label for="payment_select{{ $idx + 1 }}"><i aria-hidden="true"></i><span>{{ $plan['label'] }} <strong>{{ number_format($plan['amount']) }}원</strong></span></label>
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="radio">
                                    <input type="radio" name="membership_plan_id" id="payment_select1" value="" required>
                                    <label for="payment_select1"><i aria-hidden="true"></i><span>등록된 연회비 항목이 없습니다. <strong>0원</strong></span></label>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>2</span>결제자 정보</legend>
                    <ul class="glbox">
                        <li>
                            <label for="user_name">이름<span class="required c_iden">*</span></label>
                            <input type="text" id="user_name" name="user_name" class="text" placeholder="이름을 입력해주세요" value="{{ $user->name }}" readonly required title="이름 입력 필수">
                        </li>
                        <li>
                            <label for="user_email">이메일<span class="required c_iden">*</span></label>
                            <input type="email" id="user_email" name="user_email" class="text" placeholder="이메일을 입력해주세요" value="{{ old('user_email', $user->email) }}" readonly required>
                        </li>
                        <li>
                            <label for="user_tel">휴대폰번호<span class="required c_iden">*</span></label>
                            <input type="tel" id="user_tel" name="user_tel" class="text" placeholder="휴대폰 번호를 입력해주세요" value="{{ $user->phone_number }}" readonly required>
                        </li>
                        <li>
                            <label for="doctor_license">의사면허번호<span class="required c_iden">*</span></label>
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
										<span><strong>{{ $bankName }}</strong><p>{{ $bankAccount }}</p></span>
										<span><strong>예금주</strong><p>{{ $bankHolder }}</p></span>
									</div>
								</li>
								<li>
									<label for="depositor_name">입금자명<span class="required c_iden">*</span></label>
		                            <input type="text" id="depositor_name" name="depositor_name" class="text" placeholder="입금자명을 입력해 주세요" value="{{ old('depositor_name', $user->name) }}">
								</li>
								<li>
									<label for="deposit_expected_date">입금 예정일<span class="required c_iden">*</span></label>
		                            <input type="date" id="deposit_expected_date" name="deposit_expected_date" class="text" value="{{ old('deposit_expected_date') }}">
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
							<select name="refund_bank_name" id="bank_type" class="text">
								<option value="">-- 은행 선택 --</option>
								@foreach ($refundBankOptions as $bankName)
									<option value="{{ $bankName }}" @selected(old('refund_bank_name') === $bankName)>{{ $bankName }}</option>
								@endforeach
							</select>
                            <input type="text" id="bank_number" name="refund_account_no" class="text" placeholder="111111-22-333333" value="{{ old('refund_account_no') }}">
                            </div>
                        </li>
                        <li>
                            <label for="refund_holder_name">예금주명</label>
                            <input type="text" id="refund_holder_name" name="refund_holder_name" class="text" placeholder="예금주명" value="{{ old('refund_holder_name', $user->name) }}">
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
                                <input type="radio" name="receipt_issue" id="cash_receipt_non" value="NO" @checked(old('receipt_issue', 'NO') !== 'YES')>
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
								<select id="receipt_type" name="receipt_type" class="text">
									<option value="PERSONAL" @selected(old('receipt_type', 'PERSONAL') === 'PERSONAL')>개인소득공제용</option>
									<option value="CARD" @selected(old('receipt_type') === 'CARD')>현금영수증 카드번호</option>
								</select>
							</li>
							<li>
								<label for="receipt_number">현금영수증 번호<span class="required c_iden">*</span></label>
								<input type="text" id="receipt_number" name="receipt_number" class="text" value="{{ old('receipt_number', $user->phone_number) }}">
							</li>
						</ul>
					</div>
                </fieldset>

                <article class="abso_application">
                    <div class="mobile_opcl" aria-hidden="true"></div>
                    <h2 class="tit">결제정보</h2>
                    <p class="selected_item" data-selected-plan-label>{{ $selectedPlanLabel }}</p>
                    <dl class="price_info">
                        <div>
                            <dt>결제금액</dt>
                            <dd><strong data-selected-plan-amount>{{ number_format($selectedPlanAmount) }}</strong>원</dd>
                        </div>
                        <div class="total">
                            <dt>최종 결제 금액</dt>
                            <dd><strong data-selected-plan-total>{{ number_format($selectedPlanAmount) }}</strong>원</dd>
                        </div>
                    </dl>

                    <div class="check_area checkbox">
                        <input type="checkbox" name="terms_agree" id="terms_agree" required>
                        <label for="terms_agree"><i></i><span><strong class="c_iden">(필수)</strong> 결제 이용 약관, 개인정보 처리 동의</span></label>
                    </div>
                    <button type="submit" class="btn_submit btn_wgg"><span class="sound_only" id="training-submit-amount">{{ number_format($selectedPlanAmount) }}원 </span>결제 약관에 동의해주세요.</button>
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

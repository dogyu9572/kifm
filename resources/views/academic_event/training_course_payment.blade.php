@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@inject('trainingCourse', 'App\Services\Frontend\PublicTrainingCourseService')
<main class="sub_area">

<section class="scon academic_event_view_detail training_course_view_wrap" aria-labelledby="training-course-payment-heading">
    <h1 id="training-course-payment-heading" class="sound_only">{{ $training->title }} 사전등록</h1>
    <div class="inner">
        <div class="inbox">
            @if (! $user)
                <a href="{{ route('member.login', ['intended' => route('academic_event.training_course_payment', ['training' => $training->id], false)]) }}" class="login_prompt gbox">
                    <h2>학회 회원이신가요?</h2>
                    <p>로그인하시면 더 많은 혜택을 받을 수 있습니다.</p>
                </a>
            @endif

            <form action="{{ route('academic_event.training_course_payment.store') }}" method="post" id="training-course-payment-form" data-coupon-url="{{ route('academic_event.training_course_payment.coupon') }}">
                @csrf
                <input type="hidden" name="training_id" value="{{ $training->id }}">
                <input type="hidden" name="coupon_code" id="training-coupon-code-hidden" value="{{ old('coupon_code') }}">

                <fieldset>
                    <legend class="num_tit"><span>1</span>결제 항목 선택</legend>
                    @error('round_ids')
                        <p class="c_red">{{ $message }}</p>
                    @enderror
                    <ul class="check_box_line">
                        @foreach ($rounds as $round)
                            @php
                                $status = $trainingCourse->roundStatus($round);
                                $pricing = $trainingCourse->priceForRound($round, $user);
                                $isFull = $trainingCourse->isRoundFull($round);
                                $canApply = $trainingCourse->canApplyRound($round, $user);
                                $disabledReason = $isFull ? '정원 마감' : ($status['code'] !== 'ongoing' ? $status['label'] : $pricing['message']);
                            @endphp
                            <li @class(['end' => ! $canApply])>
                                <div class="checkbox">
                                    <input type="checkbox" name="round_ids[]" id="training_round_{{ $round->id }}" value="{{ $round->id }}" data-price="{{ $pricing['price'] }}" data-label="{{ $training->title }} - {{ $round->round_label }}" @checked(in_array((string) $round->id, old('round_ids', []), true)) @disabled(! $canApply)>
                                    <label for="training_round_{{ $round->id }}">
                                        <i aria-hidden="true"></i>
                                        <span>
                                            {{ $training->title }} - {{ $round->round_label }}
                                            @if (! $canApply)
                                                <em class="end_tag">{{ $disabledReason ?: '신청 불가' }}</em>
                                            @endif
                                            <strong>{{ number_format((int) $pricing['price']) }}원</strong>
                                        </span>
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @if ($rounds->isEmpty())
                        <p class="c_red">신청 가능한 차수가 없습니다.</p>
                    @endif
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>2</span>결제자 정보</legend>
                    <ul class="glbox">
                        <li>
                            <label for="training-user-name">이름<span class="required c_iden">*</span></label>
                            <input type="text" id="training-user-name" name="name" class="text" value="{{ old('name', $user->name ?? '') }}" placeholder="이름을 입력해주세요" required @readonly($user)>
                            @error('name')<span class="c_red">{{ $message }}</span>@enderror
                        </li>
                        <li>
                            <label for="training-user-email">이메일<span class="required c_iden">*</span></label>
                            <input type="email" id="training-user-email" name="email" class="text" value="{{ old('email', $user->email ?? '') }}" placeholder="이메일을 입력해주세요" required @readonly($user)>
                            @error('email')<span class="c_red">{{ $message }}</span>@enderror
                        </li>
                        <li>
                            <label for="training-user-phone">휴대폰번호<span class="required c_iden">*</span></label>
                            <input type="tel" id="training-user-phone" name="phone" class="text" value="{{ old('phone', $user->phone_number ?? '') }}" placeholder="휴대폰 번호를 입력해주세요" required @readonly($user)>
                            @error('phone')<span class="c_red">{{ $message }}</span>@enderror
                        </li>
                        <li>
                            <label for="training-license-no">의사면허번호</label>
                            <input type="text" id="training-license-no" name="license_no" class="text" value="{{ old('license_no', $user->license_number ?? '') }}" placeholder="의사면허번호를 입력해주세요" @readonly($user)>
                            @error('license_no')<span class="c_red">{{ $message }}</span>@enderror
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>3</span>쿠폰 등록</legend>
                    <div class="glbox">
                        <div class="inbtn">
                            <label for="training-coupon-num" class="sound_only">쿠폰번호</label>
                            <input type="text" id="training-coupon-num" class="text" value="{{ old('coupon_code') }}" placeholder="쿠폰번호를 입력해주세요">
                            <button type="button" class="btn btn_wkk" id="training-coupon-apply-btn">쿠폰등록</button>
                        </div>
                        @error('coupon_code')<span class="c_red">{{ $message }}</span>@enderror
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="num_tit"><span>4</span>결제수단</legend>
                    <div class="glbox">
                        <ul class="btns_flex">
                            <li class="radio">
                                <input type="radio" name="payment_method" id="training-payment-card" value="card" @checked(old('payment_method', 'card') === 'card')>
                                <label for="training-payment-card"><span>신용카드</span></label>
                            </li>
                            <li class="radio">
                                <input type="radio" name="payment_method" id="training-payment-bank" value="bank_transfer" @checked(old('payment_method') === 'bank_transfer')>
                                <label for="training-payment-bank"><span>무통장입금</span></label>
                            </li>
                        </ul>
						<p class="c_red type_card" role="alert">* 카드전표는 등록하신 이메일로 자동발송됩니다.</p>
                        @error('payment')<span class="c_red">{{ $message }}</span>@enderror
                        <p class="c_red type_bank_hide" role="alert c_iden">* 온라인 입금의 경우 입금확인 후 승인처리까지 하루에서 이틀정도의 시간이 소요됩니다.</p>
                        <div class="type_bank_hide">
                            <ul class="long_label">
                                <li>
                                    <label>입금하실 계좌번호</label>
                                    <div class="text flex bank_number">
                                        <span><strong>국민은행</strong><p>287937-00-000083</p></span>
                                        <span><strong>예금주</strong><p>대한기능의학회</p></span>
                                    </div>
                                </li>
                                <li>
                                    <label for="training-bank-depositor">입금자명<span class="required c_iden">*</span></label>
                                    <input type="text" id="training-bank-depositor" name="bank_depositor" class="text" value="{{ old('bank_depositor') }}" placeholder="입금자명을 입력해 주세요">
                                    @error('bank_depositor')<span class="c_red">{{ $message }}</span>@enderror
                                </li>
                                <li>
                                    <label for="training-bank-date">입금 예정일<span class="required c_iden">*</span></label>
                                    <input type="date" id="training-bank-date" name="bank_deposit_date" class="text" value="{{ old('bank_deposit_date', now()->toDateString()) }}">
                                    @error('bank_deposit_date')<span class="c_red">{{ $message }}</span>@enderror
                                </li>
                            </ul>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="type_bank_hide">
                    <legend class="num_tit"><span>5</span>환불정보</legend>
                    <ul class="glbox long_label">
                        <li>
                            <label for="training-refund-bank">은행명/계좌번호</label>
                            <div class="flex bank text">
                                <select name="refund_bank" id="training-refund-bank" class="text">
                                    <option value="국민은행" @selected(old('refund_bank') === '국민은행')>국민은행</option>
                                </select>
                                <input type="text" id="training-refund-account" name="refund_account" class="text" value="{{ old('refund_account') }}" placeholder="111111-22-333333">
                            </div>
                        </li>
                        <li>
                            <label for="training-refund-holder">예금주명</label>
                            <input type="text" id="training-refund-holder" name="refund_holder" class="text" value="{{ old('refund_holder') }}" placeholder="이메일을 입력해주세요">
                        </li>
                    </ul>
                </fieldset>

                <fieldset class="type_bank_hide">
                    <legend class="num_tit"><span>6</span>현금 영수증 발행</legend>
                    <div class="glbox">
                        <ul class="btns_flex">
                            <li class="radio">
                                <input type="radio" name="receipt_issue" id="training-receipt-no" value="NO" @checked(old('receipt_issue', 'NO') === 'NO')>
                                <label for="training-receipt-no"><span>미발행</span></label>
                            </li>
                            <li class="radio">
                                <input type="radio" name="receipt_issue" id="training-receipt-yes" value="YES" @checked(old('receipt_issue') === 'YES')>
                                <label for="training-receipt-yes"><span>발행</span></label>
                            </li>
                        </ul>
                        <ul class="bdt long_label cash_receipt_area">
                            <li>
                                <label for="training-receipt-type">발급 구분</label>
                                <select name="receipt_type" id="training-receipt-type" class="text">
                                    <option value="PERSONAL" @selected(old('receipt_type') === 'PERSONAL')>개인소득공제용</option>
                                    <option value="CARD" @selected(old('receipt_type') === 'CARD')>사업자증빙용</option>
                                    <option value="" @selected(old('receipt_type') === '')>미발급</option>
                                </select>
                            </li>
                            <li>
                                <label for="training-receipt-number">휴대폰번호<span class="required">*</span></label>
                                <input type="text" id="training-receipt-number" name="receipt_number" class="text" value="{{ old('receipt_number') }}" placeholder="010-1234-5678">
                                @error('receipt_number')<span class="c_red">{{ $message }}</span>@enderror
                            </li>
                        </ul>
                    </div>
                </fieldset>

                <article class="abso_application">
					<div class="mobile_opcl" aria-hidden="true"></div>
                    <h2 class="tit">최종 결제 확인</h2>
                    <p class="selected_item" id="training-summary-items">결제 항목을 선택해주세요.</p>
                    <dl class="price_info">
                        <div>
                            <dt>결제금액</dt>
                            <dd><strong id="training-summary-subtotal">0</strong>원</dd>
                        </div>
                        <div>
                            <dt>쿠폰 할인</dt>
                            <dd><strong class="c_iden" id="training-summary-discount">0</strong>원</dd>
                        </div>
                        <div class="total">
                            <dt>최종 결제 금액</dt>
                            <dd><strong id="training-summary-total">0</strong>원</dd>
                        </div>
                    </dl>

                    <div class="check_area checkbox">
                        <input type="checkbox" name="terms_agree" id="training-terms-agree" required>
                        <label for="training-terms-agree"><i></i><span><strong class="c_iden">(필수)</strong> 결제 이용 약관, 개인정보 처리 동의</span></label>
                    </div>
                    @error('terms_agree')<span class="c_red">{{ $message }}</span>@enderror
                    <button type="submit" class="btn_submit btn_wgg"><span class="sound_only" id="training-submit-amount">0원 </span>결제 약관에 동의해주세요.</button>
                </article>
            </form>
        </div>
    </div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/training-course.js') }}"></script>
@endpush

@php
    $card = $annualFeeCard ?? ['mode' => 'unpaid'];
    $mode = $card['mode'] ?? 'unpaid';
@endphp
<section class="box left" aria-labelledby="fee-payment-title">
    <h2 class="mytit" id="fee-payment-title">연회비 납부</h2>
    @if ($mode === 'unpaid')
        <div class="glbox bg">
            <h3 class="tit">선생님께서는 현재 <strong class="c_red">회비 미납 상태</strong>입니다.</h3>
            <p>선생님께서는 현재 회비 미납 상태입니다.</p>
            <a href="{{ route('mypage.annual_fee') }}" class="btn btn_wkk btn_arrow">연회비 납부하기 <span class="sound_only">(페이지 이동)</span></a>
        </div>
    @elseif ($mode === 'pending_bank')
        <div class="glbox bg bg_bank">
            <h3 class="tt c_iden">입금계좌 안내</h3>
            <div class="tit"><strong>{{ $card['bank_name'] }}  {{ $card['bank_account_no'] }} (예금주: {{ $card['bank_holder'] }})</strong></div>
            <p>무통장 입금 확인 중입니다. 입금 완료 후 승인됩니다.</p>
            <button type="button" class="btn btn_kwg btn_cancel" data-layer-open="pop_cancel">결제 신청 취소</button>
        </div>
    @else
        <div class="glbox bg">
            <h3 class="tit">선생님께서는 <strong class="c_iden">연회비를 납부하셨습니다.</strong></h3>
            @if (! empty($card['paid_at_formatted']))
                <p>납부완료 일시: {{ $card['paid_at_formatted'] }}</p>
            @endif
            <a href="{{ route('mypage.print_receipt') }}" class="btn btn_wkk" target="_blank">영수증 출력</a>
        </div>
    @endif
</section>

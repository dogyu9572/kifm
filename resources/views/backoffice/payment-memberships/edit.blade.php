@extends('backoffice.layouts.app')

@section('title', '연회비 납부 상세')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/payment-memberships.css') }}">
@endsection

@section('content')
    @if ($errors->any())
        <div class="board-alert board-alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="board-alert board-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-header">
            <a href="{{ route('backoffice.payment-memberships.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <form action="{{ route('backoffice.payment-memberships.update', $payment) }}" method="POST" id="paymentForm" class="bo-member-form">
                    @csrf
                    @method('PUT')

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">회원 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-form-row">
                                <label class="bo-form-label">회원 등급</label>
                                <div class="bo-form-field">
                                    <input type="text" class="board-form-control bo-readonly-box" value="{{ $memberLevelLabels[$payment->member->member_level ?? ''] ?? ($payment->member->member_level ?? '-') }}" readonly>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">아이디</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control bo-readonly-box" value="{{ $payment->member->login_id ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">이름</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control bo-readonly-box" value="{{ $payment->member->name ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">소속</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control bo-readonly-box" value="{{ $payment->member->workplace_name ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">의사면허번호</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control bo-readonly-box" value="{{ $payment->member->license_number ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label class="bo-form-label">핸드폰번호</label>
                                <div class="bo-form-field">
                                    <input type="text" class="board-form-control bo-readonly-box" value="{{ $payment->member->phone_number ?? '-' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section mt-5">
                        <h3 class="bo-section-title">납부 항목</h3>
                        <div class="bo-form-list">
                            <div class="bo-form-row">
                                <label class="bo-form-label">연회비 항목 <span class="required">*</span></label>
                                <div class="bo-form-field">
                                    <select name="membership_plan_id" class="board-form-control board-form-control--max-md">
                                        <option value="">선택</option>
                                        @foreach ($membershipPlanOptions as $id => $name)
                                            <option value="{{ $id }}" @selected((string) old('membership_plan_id', (string) $payment->membership_plan_id) === (string) $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 금액</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control bo-readonly-box" value="{{ number_format((int) $payment->amount) }} 원" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 신청일</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control bo-readonly-box" value="{{ optional($payment->requested_at)->format('Y-m-d H:i:s') ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section mt-5">
                        <h3 class="bo-section-title">결제 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-form-row">
                                <label class="bo-form-label">결제 번호</label>
                                <div class="bo-form-field">
                                    <input type="text" class="board-form-control bo-readonly-box" value="{{ $payment->payment_no }}" readonly>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">납부 상태 <span class="required">*</span></label>
                                        <div class="bo-form-field">
                                            <select name="payment_status" class="board-form-control board-form-control--max-md">
                                                @foreach ($paymentStatusLabels as $code => $label)
                                                    <option value="{{ $code }}" @selected(old('payment_status', $payment->payment_status) === $code)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 수단 <span class="required">*</span></label>
                                        <div class="bo-form-field">
                                            <div class="board-radio-group bo-radio-group-wrap">
                                                @foreach ($paymentMethodLabels as $code => $label)
                                                    <div class="board-radio-item">
                                                        <input type="radio" id="payment_method_{{ $code }}" name="payment_method" value="{{ $code }}" class="board-radio-input" @checked(old('payment_method', $payment->payment_method) === $code)>
                                                        <label for="payment_method_{{ $code }}">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">입금자명</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" name="depositor_name" value="{{ old('depositor_name', $payment->depositor_name) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 완료일</label>
                                        <div class="bo-form-field">
                                            <input type="date" class="board-form-control board-form-control--max-md" name="paid_at" value="{{ old('paid_at', optional($payment->paid_at)->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label class="bo-form-label">입금 확인</label>
                                <div class="bo-form-field">
                                    <button type="submit" formaction="{{ route('backoffice.payment-memberships.confirm-deposit', $payment) }}" formmethod="POST" class="btn btn-warning btn-sm">입금 확인</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section mt-5">
                        <h3 class="bo-section-title">현금영수증</h3>
                        <div class="bo-form-list">
                            <div class="bo-form-row">
                                <label class="bo-form-label">발행 여부 <span class="required">*</span></label>
                                <div class="bo-form-field">
                                    <div class="board-radio-group bo-radio-group-wrap">
                                        <div class="board-radio-item">
                                            <input type="radio" id="receipt_issue_no" name="receipt_issue" value="NO" class="board-radio-input" @checked(old('receipt_issue', $payment->receipt_issue) === 'NO')>
                                            <label for="receipt_issue_no">미발행</label>
                                        </div>
                                        <div class="board-radio-item">
                                            <input type="radio" id="receipt_issue_yes" name="receipt_issue" value="YES" class="board-radio-input" @checked(old('receipt_issue', $payment->receipt_issue) === 'YES')>
                                            <label for="receipt_issue_yes">발행</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">발급 유형</label>
                                        <div class="bo-form-field">
                                            <select name="receipt_type" class="board-form-control board-form-control--max-md">
                                                <option value="">선택</option>
                                                <option value="PERSONAL" @selected(old('receipt_type', $payment->receipt_type) === 'PERSONAL')>개인소득공제용</option>
                                                <option value="CARD" @selected(old('receipt_type', $payment->receipt_type) === 'CARD')>현금영수증 카드번호</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">발급 번호</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" name="receipt_number" value="{{ old('receipt_number', $payment->receipt_number) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section mt-5">
                        <h3 class="bo-section-title">환불 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">은행명</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" name="refund_bank_name" value="{{ old('refund_bank_name', $payment->refund_bank_name) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">계좌번호</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" name="refund_account_no" value="{{ old('refund_account_no', $payment->refund_account_no) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label class="bo-form-label">예금주명</label>
                                <div class="bo-form-field">
                                    <input type="text" class="board-form-control" name="refund_holder_name" value="{{ old('refund_holder_name', $payment->refund_holder_name) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="board-form-actions board-form-actions--member-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ route('backoffice.payment-memberships.index') }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection



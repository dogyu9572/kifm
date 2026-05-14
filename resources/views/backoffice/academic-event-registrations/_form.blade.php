@php
    $isEdit = $registration->exists;
    $payMethod = old('payment_method', $registration->payment_method ?? 'bank_transfer');
    $showBankPayment = $payMethod === 'bank_transfer';
    $showReceiptDetail = old('receipt_issue', $registration->receipt_issue ?? 'NO') === 'YES';
@endphp

<div class="bo-form-section">
    <h3 class="bo-section-title">참가 등록</h3>
    <div class="bo-form-list">
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">행사 선택 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <select name="academic_event_id" class="board-form-control" required>
                            <option value="">-- 행사를 선택하세요 --</option>
                            @foreach ($events as $ev)
                                <option value="{{ $ev->id }}" @selected((string) old('academic_event_id', $registration->academic_event_id) === (string) $ev->id)>
                                    {{ $ev->year }} {{ $ev->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">등록 구분 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <div class="board-radio-group bo-radio-group-wrap">
                            @foreach ($regTypeLabels as $code => $label)
                                <div class="board-radio-item">
                                    <input type="radio" id="reg_type_{{ $code }}" name="reg_type" value="{{ $code }}" class="board-radio-input" @checked(old('reg_type', $registration->reg_type) === $code)>
                                    <label for="reg_type_{{ $code }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">등록자 조회</label>
            <div class="bo-form-field">
                @php
                    $selectedMember = $registration->member;
                    $selectedMemberId = old('member_id', $registration->member_id);
                    $selectedMemberLabel = old('member_label');
                    if ($selectedMemberLabel === null) {
                        $selectedMemberLabel = old('member_display');
                    }
                    if ($selectedMemberLabel === null && $selectedMember) {
                        $selectedMemberLabel = $selectedMember->name . ' (' . ($selectedMember->login_id ?? '-') . ' / ' . ($selectedMember->email ?? '-') . ')';
                    }
                @endphp
                <div class="input-with-button bo-member-select-inline js-member-selector" data-search-url="{{ route('backoffice.academic-event-registrations.search-members') }}">
                    <input type="hidden" name="member_id" class="js-member-id" value="{{ $selectedMemberId }}">
                    <input type="hidden" name="member_label" class="js-member-label" value="{{ $selectedMemberLabel }}">
                    <input type="text" class="board-form-control js-member-display" value="{{ $selectedMemberLabel }}" placeholder="회원 검색 버튼을 눌러 선택하세요." readonly>
                    <button type="button" class="btn btn-secondary btn-sm js-open-member-modal">회원 검색</button>
                </div>
            </div>
        </div>

        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">이름 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="text" name="name" id="name" class="board-form-control" value="{{ old('name', $registration->name) }}" placeholder="이름 입력" required>
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">면허번호</label>
                    <div class="bo-form-field">
                        <input type="text" name="license_no" id="license_no" class="board-form-control" value="{{ old('license_no', $registration->license_no) }}" placeholder="면허번호 입력">
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">휴대폰번호</label>
                    <div class="bo-form-field">
                        <input type="text" name="phone" id="phone" class="board-form-control" value="{{ old('phone', $registration->phone) }}" placeholder="010-0000-0000">
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">이메일</label>
                    <div class="bo-form-field">
                        <input type="email" name="email" id="email" class="board-form-control" value="{{ old('email', $registration->email) }}" placeholder="example@email.com">
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">등록 일시</label>
                    <div class="bo-form-field">
                        <input type="datetime-local" name="registered_at" class="board-form-control board-form-control--max-md" value="{{ old('registered_at', optional($registration->registered_at)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col" aria-hidden="true"></div>
        </div>
    </div>
</div>

<div class="modal js-member-modal bo-member-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">회원 검색</h5>
            <button type="button" class="close js-close-member-modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="board-filter member-search-modal-filter">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">검색 구분</label>
                        <select class="filter-select js-member-search-field">
                            <option value="all">전체</option>
                            <option value="id">ID</option>
                            <option value="name">이름</option>
                            <option value="phone">휴대폰</option>
                            <option value="email">이메일</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">검색어</label>
                        <input type="text" class="filter-input js-member-keyword" placeholder="검색어를 입력하세요">
                    </div>
                    <div class="filter-group">
                        <div class="filter-buttons">
                            <button type="button" class="btn btn-primary js-search-member"><i class="fas fa-search"></i> 검색</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>이름</th>
                            <th>아이디</th>
                            <th>연락처</th>
                            <th>이메일</th>
                            <th>선택</th>
                        </tr>
                    </thead>
                    <tbody class="js-member-results">
                        <tr>
                            <td colspan="6" class="text-center">검색 버튼을 눌러 회원을 조회하세요.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 js-member-pagination"></div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-5">
    <h3 class="bo-section-title">결제 정보</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label">참가 번호</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control bo-readonly-box board-form-control--max-md" value="{{ $registration->registration_no ?: ($registration->exists ? '-' : '저장 시 자동 생성') }}" readonly>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">납부 상태 <span class="required">*</span></label>
            <div class="bo-form-field">
                <select name="payment_status" id="payment_status" class="board-form-control board-form-control--max-md" required>
                    @foreach ($paymentStatusLabels as $code => $label)
                        <option value="{{ $code }}" @selected(old('payment_status', $registration->payment_status) === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">신청일</label>
                    <div class="bo-form-field">
                        <input type="text" class="board-form-control bo-readonly-box" value="{{ optional($registration->applied_at)->format('Y-m-d H:i:s') ?? '-' }}" readonly>
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">결제일시</label>
                    <div class="bo-form-field">
                        <input type="text" class="board-form-control bo-readonly-box" value="{{ optional($registration->paid_at)->format('Y-m-d H:i:s') ?? '-' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">결제 항목 <span class="required">*</span></label>
            <div class="bo-form-field">
                <button type="button" class="btn btn-secondary btn-sm" id="open-plan-modal">결제 항목 추가</button>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">선택된 결제 항목</label>
            <div class="bo-form-field">
                <div class="table-responsive">
                    <table class="board-table" id="selected-items-table">
                        <thead>
                            <tr>
                                <th>결제항목명</th>
                                <th class="w15">구분</th>
                                <th class="w15">금액</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="bo-selected-items-tfoot-row">
                                <td colspan="2"></td>
                                <td class="bo-selected-items-total-cell">
                                    <span class="bo-selected-items-total-label">합계</span>
                                    <span id="selected-items-total">0원</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="payment_items_payload" id="payment_items_payload" value="{{ old('payment_items_payload', $selectedItemsJson) }}">
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">결제 수단 <span class="required">*</span></label>
            <div class="bo-form-field">
                <div class="board-radio-group bo-radio-group-wrap">
                    @foreach ($paymentMethodLabels as $code => $label)
                        <div class="board-radio-item">
                            <input type="radio" id="payment_method_{{ $code }}" name="payment_method" value="{{ $code }}" class="board-radio-input js-payment-method" @checked($payMethod === $code)>
                            <label for="payment_method_{{ $code }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                <p id="bo-ae-reg-card-guide" class="bo-member-exec-note mt-2 @if ($showBankPayment || $payMethod === 'onsite') bo-hidden @endif">카드전표는 등록된 이메일로 자동 발송됩니다.</p>
            </div>
        </div>

        <div id="bo-ae-reg-bank-block" class="@if (! $showBankPayment) bo-hidden @endif">
            <div class="bo-form-row">
                <label class="bo-form-label">입금 계좌 안내</label>
                <div class="bo-form-field">
                    <textarea name="bank_account_text" class="board-form-control board-form-textarea" rows="2" placeholder="은행·계좌 안내를 입력하세요.">{{ old('bank_account_text', $registration->bank_account_text) }}</textarea>
                </div>
            </div>
            <div class="bo-member-dual-row">
                <div class="bo-member-dual-col">
                    <div class="bo-form-row">
                        <label class="bo-form-label">입금자명 <span class="required">*</span></label>
                        <div class="bo-form-field">
                            <input type="text" name="bank_depositor" id="bank_depositor" class="board-form-control" value="{{ old('bank_depositor', $registration->bank_depositor) }}" placeholder="입금자명을 입력하세요." autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="bo-member-dual-col">
                    <div class="bo-form-row">
                        <label class="bo-form-label">입금일 <span class="required">*</span></label>
                        <div class="bo-form-field">
                            <input type="date" name="bank_deposit_date" id="bank_deposit_date" class="board-form-control" value="{{ old('bank_deposit_date', optional($registration->bank_deposit_date)->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">관리자 메모</label>
            <div class="bo-form-field">
                <textarea name="admin_memo" class="board-form-control" rows="3" placeholder="메모를 입력하세요.">{{ old('admin_memo', $registration->admin_memo) }}</textarea>
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
                        <input type="radio" id="receipt_issue_no" name="receipt_issue" value="NO" class="board-radio-input js-receipt-issue" @checked(old('receipt_issue', $registration->receipt_issue ?? 'NO') === 'NO')>
                        <label for="receipt_issue_no">미발행</label>
                    </div>
                    <div class="board-radio-item">
                        <input type="radio" id="receipt_issue_yes" name="receipt_issue" value="YES" class="board-radio-input js-receipt-issue" @checked(old('receipt_issue', $registration->receipt_issue ?? 'NO') === 'YES')>
                        <label for="receipt_issue_yes">발행</label>
                    </div>
                </div>
            </div>
        </div>
        <div id="bo-ae-reg-receipt-detail" class="bo-member-dual-row @if (! $showReceiptDetail) bo-hidden @endif">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">발급 유형 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <select name="receipt_type" id="receipt_type" class="board-form-control">
                            <option value="">선택</option>
                            <option value="PERSONAL" @selected(old('receipt_type', $registration->receipt_type) === 'PERSONAL')>개인소득공제용 (휴대폰번호)</option>
                            <option value="CARD" @selected(old('receipt_type', $registration->receipt_type) === 'CARD')>현금영수증 카드번호</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">발급 번호 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="text" name="receipt_number" id="receipt_number" class="board-form-control" value="{{ old('receipt_number', $registration->receipt_number) }}" placeholder="휴대폰번호 또는 카드번호를 입력하세요." autocomplete="off">
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
                        <input type="text" name="refund_bank" class="board-form-control" value="{{ old('refund_bank', $registration->refund_bank) }}" placeholder="은행명을 입력하세요." autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">계좌번호</label>
                    <div class="bo-form-field">
                        <input type="text" name="refund_account" class="board-form-control" value="{{ old('refund_account', $registration->refund_account) }}" placeholder="계좌번호를 입력하세요." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
        <div class="bo-form-row">
            <label class="bo-form-label">예금주명</label>
            <div class="bo-form-field">
                <input type="text" name="refund_holder" class="board-form-control" value="{{ old('refund_holder', $registration->refund_holder) }}" placeholder="예금주명을 입력하세요." autocomplete="off">
            </div>
        </div>
    </div>
</div>

<div class="board-form-actions">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
    <a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a>
</div>

<div class="modal js-plan-modal bo-member-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">결제 항목 선택</h5>
            <button type="button" class="close js-close-plan-modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="board-filter member-search-modal-filter">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">검색 구분</label>
                        <select class="filter-select js-plan-search-field">
                            <option value="name">결제항목명</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">검색어</label>
                        <input type="text" class="filter-input js-plan-keyword" placeholder="검색어를 입력하세요">
                    </div>
                    <div class="filter-group">
                        <div class="filter-buttons">
                            <button type="button" class="btn btn-primary js-search-plan"><i class="fas fa-search"></i> 검색</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>결제 항목</th>
                            <th>결제항목명</th>
                            <th>회원 여부</th>
                            <th>금액</th>
                            <th>선택</th>
                        </tr>
                    </thead>
                    <tbody class="js-plan-results">
                        <tr>
                            <td colspan="6" class="text-center">검색 버튼을 눌러 결제 항목을 조회하세요.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 js-plan-pagination" aria-label="결제 항목 목록 페이지"></div>
        </div>
    </div>
</div>

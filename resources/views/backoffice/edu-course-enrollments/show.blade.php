@extends('backoffice.layouts.app')

@section('title', '수강 신청 상세')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-trainings.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>

        <div class="board-card">
            <div class="board-card-body">
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
                    <div class="board-alert board-alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('backoffice.edu-course-enrollments.update', $enrollment) }}" id="bo-edu-course-enrollment-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="return_url" value="{{ request('return_url') }}">
                    @php
                        $displayEnrollmentStatus = in_array($enrollment->payment_status, ['paid', 'completed'], true)
                            && $enrollment->enrollment_status === 'payment_pending'
                                ? 'in_progress'
                                : $enrollment->enrollment_status;
                    @endphp

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">강좌 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">강좌명</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->course->title ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">카테고리</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $categoryLabels[$enrollment->course->course_type ?? ''] ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">수강 완료 평점</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control board-form-control--max-xs" value="{{ $enrollment->course->completion_score ? $enrollment->course->completion_score . '점' : '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">수강자 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">수강자 이름</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->member_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">수강자 아이디</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->member->login_id ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">회원 등급 (수강 당시)</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $memberLevelLabels[$enrollment->member_grade_at] ?? ($enrollment->member_grade_at ?: '-') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">수강 진행 현황</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">상태</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $statusLabels[$displayEnrollmentStatus] ?? $displayEnrollmentStatus }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">수강률</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->progress_rate }}%" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">시험 응시 상태</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $examStatusLabels[$enrollment->exam_status ?? 'not_attempted'] ?? '미응시' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">시험 점수</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->exam_score !== null ? $enrollment->exam_score . '점' : '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">총 수강 시간</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->total_study_min }}분" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">마지막 수강일</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ optional($enrollment->last_studied_at)->format('Y.m.d H:i') ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">수료증 관리</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">수료증 발급 여부</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->certificate_status === 'issued' ? '발급완료' : '미발급' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">발급일</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ optional($enrollment->certificate_issued_at)->format('Y.m.d H:i') ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">결제 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 번호</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control board-form-control--max-md" value="{{ $enrollment->payment_no ?: '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">납부 상태</label>
                                        <div class="bo-form-field">
                                            <select name="payment_status" class="board-form-control board-form-control--max-md">
                                                @foreach ($paymentStatusLabels as $code => $label)
                                                    <option value="{{ $code }}" @selected($enrollment->payment_status === $code)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">신청일</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ optional($enrollment->applied_at)->format('Y.m.d') ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제일시</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ optional($enrollment->paid_at)->format('Y.m.d H:i') ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 항목</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ $enrollment->payment_item_name ?: '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 금액</label>
                                        <div class="bo-form-field">
                                            <input type="text" class="board-form-control" value="{{ number_format($enrollment->payment_amount) }}원" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">결제 수단</label>
                                        <div class="bo-form-field">
                                            <div class="board-radio-group bo-radio-group-wrap">
                                                <div class="board-radio-item">
                                                    <input type="radio" id="payment_method_card" name="payment_method" value="card" class="board-radio-input" @checked($enrollment->payment_method === 'card')>
                                                    <label for="payment_method_card">신용카드</label>
                                                </div>
                                                <div class="board-radio-item">
                                                    <input type="radio" id="payment_method_bank_transfer" name="payment_method" value="bank_transfer" class="board-radio-input" @checked($enrollment->payment_method === 'bank_transfer')>
                                                    <label for="payment_method_bank_transfer">무통장 입금</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col" aria-hidden="true"></div>
                            </div>
                            <div class="bo-member-dual-row bo-bank-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">입금자명</label>
                                        <div class="bo-form-field">
                                            <input type="text" name="bank_depositor" class="board-form-control" value="{{ old('bank_depositor', $enrollment->bank_depositor) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">입금일</label>
                                        <div class="bo-form-field">
                                            <input type="date" name="bank_deposit_date" class="board-form-control board-form-control--max-md" value="{{ old('bank_deposit_date', optional($enrollment->bank_deposit_date)->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label class="bo-form-label">관리자 메모</label>
                                <div class="bo-form-field">
                                    <textarea name="admin_memo" class="board-form-control board-form-textarea" rows="4">{{ old('admin_memo', $enrollment->admin_memo) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section bo-form-section-no-row-border">
                        <h3 class="bo-section-title">현금영수증</h3>
                        <div class="bo-form-list">
                            <div class="bo-form-row">
                                <label class="bo-form-label">발행 여부</label>
                                <div class="bo-form-field">
                                    <div class="board-radio-group bo-radio-group-wrap">
                                        <div class="board-radio-item">
                                            <input type="radio" id="receipt_issue_no" name="receipt_issue" value="NO" class="board-radio-input" @checked(($enrollment->receipt_issue ?? 'NO') === 'NO')>
                                            <label for="receipt_issue_no">미발행</label>
                                        </div>
                                        <div class="board-radio-item">
                                            <input type="radio" id="receipt_issue_yes" name="receipt_issue" value="YES" class="board-radio-input" @checked($enrollment->receipt_issue === 'YES')>
                                            <label for="receipt_issue_yes">발행</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row bo-receipt-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">발급 유형</label>
                                        <div class="bo-form-field">
                                            <select name="receipt_type" class="board-form-control">
                                                <option value="">선택</option>
                                                <option value="personal" @selected($enrollment->receipt_type === 'personal')>개인소득공제용 (휴대폰번호)</option>
                                                <option value="card" @selected($enrollment->receipt_type === 'card')>현금영수증 카드번호</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">발급 번호</label>
                                        <div class="bo-form-field">
                                            <input type="text" name="receipt_number" class="board-form-control" value="{{ old('receipt_number', $enrollment->receipt_number) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">환불 정보</h3>
                        <div class="bo-form-list">
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">은행명</label>
                                        <div class="bo-form-field">
                                            <input type="text" name="refund_bank" class="board-form-control" value="{{ old('refund_bank', $enrollment->refund_bank) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">계좌번호</label>
                                        <div class="bo-form-field">
                                            <input type="text" name="refund_account" class="board-form-control" value="{{ old('refund_account', $enrollment->refund_account) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-row">
                                <div class="bo-member-dual-col">
                                    <div class="bo-form-row">
                                        <label class="bo-form-label">예금주명</label>
                                        <div class="bo-form-field">
                                            <input type="text" name="refund_holder" class="board-form-control" value="{{ old('refund_holder', $enrollment->refund_holder) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="bo-member-dual-col" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/edu-course-enrollments-show.js') }}"></script>
@endsection

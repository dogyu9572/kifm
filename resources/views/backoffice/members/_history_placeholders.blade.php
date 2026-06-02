{{-- 회원 상세: 학술/연수/온라인/인정의/임원/회비 6개 이력 (읽기 전용)
     - 평점 섹션: 명세상 member_credits 신설 필요 → 1차 제외
     - 연수 차수별 이수·인정의 조건 컬럼: 회원-차수 출석 매핑 미존재 → 1차 제외 --}}

@php
    $histories = $histories ?? [];
    $historyLabels = $historyLabels ?? [];
    $memberDetailReturnUrl = request()->getRequestUri();
    $certifiedCreateUrl = route('backoffice.certified-members.create', [
        'member_id' => $member->id,
        'return_url' => $memberDetailReturnUrl,
    ]);
    $executiveCreateUrl = route('backoffice.member-executives.create', [
        'member_id' => $member->id,
        'return_url' => $memberDetailReturnUrl,
    ]);
    $academicRegistrations = $histories['academicRegistrations'] ?? collect();
    $eduTrainingPayments = $histories['eduTrainingPayments'] ?? collect();
    $eduCourseEnrollments = $histories['eduCourseEnrollments'] ?? collect();
    $certifiedMembers = $histories['certifiedMembers'] ?? collect();
    $memberExecutives = $histories['memberExecutives'] ?? collect();
    $membershipPayments = $histories['membershipPayments'] ?? collect();
@endphp

{{-- 1. 학술대회 참가 이력 --}}
<div class="bo-form-section bo-member-history-section">
    <h3 class="bo-section-title">학술대회 참가 이력</h3>
    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>참가 번호</th>
                    <th>시즌</th>
                    <th>행사명</th>
                    <th>등록 구분</th>
                    <th>결제항목</th>
                    <th>결제수단</th>
                    <th>결제 상태</th>
                    <th>등록일</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($academicRegistrations as $registration)
                    <tr>
                        <td>{{ $registration->registration_no }}</td>
                        <td>{{ $historyLabels['academicSeason'][$registration->event->season ?? ''] ?? '-' }}</td>
                        <td>{{ $registration->event->title ?? '-' }}</td>
                        <td>{{ $historyLabels['academicRegType'][$registration->reg_type] ?? $registration->reg_type }}</td>
                        <td>{{ $registration->items->pluck('item_name')->filter()->implode(', ') ?: '-' }}</td>
                        <td>{{ $historyLabels['academicPaymentMethod'][$registration->payment_method] ?? $registration->payment_method }}</td>
                        <td>{{ $historyLabels['academicPaymentStatus'][$registration->payment_status] ?? $registration->payment_status }}</td>
                        <td>{{ optional($registration->registered_at)->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">데이터가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 2. 연수 교육 참가 이력 (차수별 이수 ✓, 인정의 조건 컬럼은 1차 제외) --}}
<div class="bo-form-section bo-member-history-section">
    <h3 class="bo-section-title">연수 교육 참가 이력</h3>
    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>주문번호</th>
                    <th>시즌</th>
                    <th>년도</th>
                    <th>연수명</th>
                    <th>등록 구분</th>
                    <th>결제수단</th>
                    <th>결제 상태</th>
                    <th>등록일</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($eduTrainingPayments as $payment)
                    <tr>
                        <td>{{ $payment->order_no }}</td>
                        <td>{{ $historyLabels['eduTrainingSeason'][$payment->training->season ?? ''] ?? '-' }}</td>
                        <td>{{ $payment->training->year ?? '-' }}</td>
                        <td>{{ $payment->training->title ?? '-' }}</td>
                        <td>{{ $historyLabels['eduTrainingRegType'][$payment->reg_type] ?? $payment->reg_type }}</td>
                        <td>{{ $historyLabels['eduTrainingPaymentMethod'][$payment->payment_method] ?? $payment->payment_method }}</td>
                        <td>{{ $historyLabels['eduTrainingPaymentStatus'][$payment->payment_status] ?? $payment->payment_status }}</td>
                        <td>{{ optional($payment->registered_at)->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">데이터가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 3. 온라인 교육 참가 이력 --}}
<div class="bo-form-section bo-member-history-section">
    <h3 class="bo-section-title">온라인 교육 참가 이력</h3>
    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>분류</th>
                    <th>개설연도</th>
                    <th>강의명</th>
                    <th>신청일</th>
                    <th>수강만료일</th>
                    <th>수강률</th>
                    <th>상태</th>
                    <th>수료증</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($eduCourseEnrollments as $enrollment)
                    <tr>
                        <td>{{ $historyLabels['eduCourseCategory'][$enrollment->course->course_type ?? ''] ?? '-' }}</td>
                        <td>{{ $enrollment->course->open_year ?? '-' }}</td>
                        <td>{{ $enrollment->course->title ?? '-' }}</td>
                        <td>{{ optional($enrollment->applied_at)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ optional($enrollment->expire_at)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $enrollment->progress_rate }}%</td>
                        <td>{{ $historyLabels['eduCourseStatus'][$enrollment->enrollment_status] ?? $enrollment->enrollment_status }}</td>
                        <td>
                            @if ($enrollment->certificate_status === 'issued')
                                <a href="{{ route('backoffice.edu-course-enrollments.certificate', $enrollment) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">발급완료</a>
                            @else
                                {{ $historyLabels['eduCourseCertificate'][$enrollment->certificate_status] ?? $enrollment->certificate_status }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">데이터가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 4. 인정의 정보 (추가/수정/삭제는 별도 메뉴 /backoffice/certified-members 에서 처리) --}}
<div class="bo-form-section bo-member-history-section">
    <div class="bo-section-heading-row">
        <h3 class="bo-section-title">인정의 정보</h3>
        <a href="{{ $certifiedCreateUrl }}" class="btn btn-success btn-sm">이력 추가</a>
    </div>
    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>취득일</th>
                    <th>만료일</th>
                    <th>잔여 기간</th>
                    <th>상태</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certifiedMembers as $cert)
                    <tr>
                        <td>{{ optional($cert->acquired_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ optional($cert->validity_end_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            @php($remainingDays = $cert->remainingDays())
                            {{ $remainingDays >= 0 ? $remainingDays.'일' : '만료' }}
                        </td>
                        <td>{{ $cert->statusLabel() }}</td>
                        <td>
                            <div class="board-btn-group">
                                <a href="{{ route('backoffice.certified-members.edit', [$cert, 'return_url' => $memberDetailReturnUrl]) }}" class="btn btn-primary btn-sm">수정</a>
                                <form action="{{ route('backoffice.certified-members.destroy', $cert) }}" method="POST" class="d-inline bo-member-delete-form" data-confirm="인정의 이력을 삭제하시겠습니까?">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_url" value="{{ $memberDetailReturnUrl }}">
                                    <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">데이터가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 5. 임원 이력 (추가/수정/삭제는 별도 메뉴 /backoffice/member-executives 에서 처리) --}}
<div class="bo-form-section bo-member-history-section">
    <div class="bo-section-heading-row">
        <h3 class="bo-section-title">임원 이력</h3>
        <a href="{{ $executiveCreateUrl }}" class="btn btn-success btn-sm">이력 추가</a>
    </div>
    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>직책</th>
                    <th>임기 시작일</th>
                    <th>임기 종료일</th>
                    <th>상태</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($memberExecutives as $executive)
                    <tr>
                        <td>{{ $historyLabels['executiveRole'][$executive->executive_role] ?? $executive->executive_role }}</td>
                        <td>{{ optional($executive->term_start_date)->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            @if ($executive->is_indefinite)
                                무기한
                            @else
                                {{ optional($executive->term_end_date)->format('Y-m-d') ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $executive->termStatusLabel() }}</td>
                        <td>
                            <div class="board-btn-group">
                                <a href="{{ route('backoffice.member-executives.edit', [$executive, 'return_url' => $memberDetailReturnUrl]) }}" class="btn btn-primary btn-sm">수정</a>
                                <form action="{{ route('backoffice.member-executives.destroy', $executive) }}" method="POST" class="d-inline bo-member-delete-form" data-confirm="임원 이력을 삭제하시겠습니까?">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_url" value="{{ $memberDetailReturnUrl }}">
                                    <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">데이터가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 6. 회비 납부 내역 --}}
<div class="bo-form-section bo-member-history-section">
    <h3 class="bo-section-title">회비 납부 내역</h3>
    <div class="board-filter bo-history-filter" data-history-filter="membership-payments">
        <div class="filter-row">
            <div class="filter-group">
                <label class="filter-label" for="membership_payment_date_start">검색기간</label>
                <input type="date" id="membership_payment_date_start" class="filter-input bo-history-date-start">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="membership_payment_date_end">종료일</label>
                <input type="date" id="membership_payment_date_end" class="filter-input bo-history-date-end">
            </div>
            <div class="filter-group">
                <div class="filter-buttons">
                    <button type="button" class="btn btn-outline-secondary btn-sm bo-history-preset" data-months="3">3개월</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm bo-history-preset" data-months="6">6개월</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm bo-history-preset" data-months="12">1년</button>
                    <button type="button" class="btn btn-primary btn-sm bo-history-filter-apply">조회</button>
                    <button type="button" class="btn btn-secondary btn-sm bo-history-filter-reset">초기화</button>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>연도/항목</th>
                    <th>금액</th>
                    <th>결제수단</th>
                    <th>결제일</th>
                    <th>상태</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($membershipPayments as $payment)
                    <tr data-membership-payment-row data-history-date="{{ optional($payment->paid_at ?? $payment->requested_at)->format('Y-m-d') }}">
                        <td>{{ $payment->plan->plan_name ?? '-' }}</td>
                        <td>{{ number_format((int) $payment->amount) }}원</td>
                        <td>{{ $historyLabels['membershipPaymentMethod'][$payment->payment_method] ?? $payment->payment_method }}</td>
                        <td>{{ optional($payment->paid_at)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $historyLabels['membershipPaymentStatus'][$payment->payment_status] ?? $payment->payment_status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">데이터가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php
    $selectedMember = $certifiedMember->member ?? null;
    $selectedMemberId = old('member_id', $certifiedMember->member_id ?? '');
    $selectedMemberLabel = old('member_label');

    if ($selectedMemberLabel === null && $selectedMember) {
        $selectedMemberLabel = $selectedMember->name . ' (' . ($selectedMember->login_id ?? '-') . ' / ' . ($selectedMember->email ?? '-') . ')';
    }

    $initialPeriod = trim(
        (optional($certifiedMember->acquired_validity_start ?? null)->format('Y-m-d') ?? '-')
        . ' ~ '
        . (optional($certifiedMember->acquired_validity_end ?? null)->format('Y-m-d') ?? '-')
    );
    $acquisitionSummary = $qualificationSummary['acquisition'] ?? null;
    $renewalSummary = $qualificationSummary['renewal'] ?? null;
@endphp

<div class="bo-form-section">
    <h3 class="bo-section-title">1. 회원 선택</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label">회원 <span class="required">*</span></label>
            <div class="bo-form-field">
                <div class="js-member-selector" data-search-url="{{ route('backoffice.certified-members.search-members') }}" data-readonly="{{ $certifiedMember->exists ? '1' : '0' }}">
                    <input type="hidden" name="member_id" class="js-member-id" value="{{ $selectedMemberId }}">
                    <input type="hidden" name="member_label" class="js-member-label" value="{{ $selectedMemberLabel }}">
                    @if ($selectedMember)
                    <div class="bo-certified-member-summary">
                        <div class="bo-certified-member-summary-row">
                            <div class="bo-certified-member-summary-label">아이디</div>
                            <div class="bo-certified-member-summary-value">{{ $selectedMember->login_id ?? '-' }}</div>
                            <div class="bo-certified-member-summary-label">이름</div>
                            <div class="bo-certified-member-summary-value">{{ $selectedMember->name ?? '-' }}</div>
                        </div>
                        <div class="bo-certified-member-summary-row">
                            <div class="bo-certified-member-summary-label">이메일</div>
                            <div class="bo-certified-member-summary-value">{{ $selectedMember->email ?? '-' }}</div>
                            <div class="bo-certified-member-summary-label">연락처</div>
                            <div class="bo-certified-member-summary-value">{{ $selectedMember->phone_number ?? '-' }}</div>
                        </div>
                        <div class="bo-certified-member-summary-row">
                            <div class="bo-certified-member-summary-label">의사면허번호</div>
                            <div class="bo-certified-member-summary-value bo-certified-member-summary-value--wide">{{ $selectedMember->license_number ?? '-' }}</div>
                        </div>
                    </div>
                    @unless ($certifiedMember->exists)
                        <div class="mt-3 input-with-button bo-member-select-inline">
                            <input type="text" class="board-form-control js-member-display" value="{{ $selectedMemberLabel }}" placeholder="회원 검색 버튼을 눌러 선택하세요." readonly>
                            <button type="button" class="btn btn-secondary btn-sm js-open-member-modal">다시 선택</button>
                        </div>
                    @endunless
                @else
                    <div class="input-with-button bo-member-select-inline">
                        <input type="text" class="board-form-control js-member-display" value="{{ $selectedMemberLabel }}" placeholder="회원 검색 버튼을 눌러 선택하세요." readonly>
                        <button type="button" class="btn btn-secondary btn-sm js-open-member-modal">회원 검색</button>
                    </div>
                @endif
                </div>

                @error('member_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
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
                            <option value="license">면허번호</option>
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
    <h3 class="bo-section-title">2. 인정의 현황</h3>
    <div class="bo-form-list">
        @if ($certifiedMember->exists)
            <div class="bo-form-row">
                <label class="bo-form-label">인증 상태</label>
                <div class="bo-form-field">
                    <input type="text" class="board-form-control bo-readonly-box board-form-control--max-md" value="{{ $certifiedMember->statusLabel() }} / {{ $certifiedMember->remainingDays() >= 0 ? 'D-' . $certifiedMember->remainingDays() : '만료' }}" readonly>
                </div>
            </div>
        @endif
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">유효기간 시작일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="validity_start_date" class="board-form-control board-form-control--max-md" value="{{ old('validity_start_date', optional($certifiedMember->validity_start_date ?? null)->format('Y-m-d')) }}">
                        @error('validity_start_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">유효기간 종료일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="validity_end_date" class="board-form-control board-form-control--max-md" value="{{ old('validity_end_date', optional($certifiedMember->validity_end_date ?? null)->format('Y-m-d')) }}">
                        @error('validity_end_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-5">
    <h3 class="bo-section-title">3. 취득·갱신 이력</h3>
    <div class="bo-form-list">
        @unless ($certifiedMember->exists)
            <div class="board-alert board-alert-info">
                저장 시 최초 취득 이력이 자동으로 기록됩니다.
            </div>
        @endunless

        <h4 class="bo-certified-subtitle">최초 취득</h4>
        @if ($acquisitionSummary)
            <div class="bo-certified-condition-grid">
                <div class="bo-certified-condition-card">
                    <div class="bo-certified-condition-head">
                        <strong>정기 연수강좌</strong>
                        <span class="bo-certified-status {{ $acquisitionSummary['regular_completed'] ? 'is-complete' : 'is-incomplete' }}">
                            {{ $acquisitionSummary['regular_completed'] ? '충족' : '미충족' }}
                        </span>
                    </div>
                    <div class="bo-certified-condition-count">
                        {{ $acquisitionSummary['regular_count'] }} / {{ $acquisitionSummary['regular_required'] }}회
                    </div>
                    <div class="bo-certified-condition-tags">
                        <span class="bo-certified-mini-status {{ $acquisitionSummary['regular_even_completed'] ? 'is-complete' : 'is-incomplete' }}">짝수년도</span>
                        <span class="bo-certified-mini-status {{ $acquisitionSummary['regular_odd_completed'] ? 'is-complete' : 'is-incomplete' }}">홀수년도</span>
                    </div>
                </div>
                <div class="bo-certified-condition-card">
                    <div class="bo-certified-condition-head">
                        <strong>동계 연수강좌</strong>
                        <span class="bo-certified-status {{ $acquisitionSummary['winter_completed'] ? 'is-complete' : 'is-incomplete' }}">
                            {{ $acquisitionSummary['winter_completed'] ? '충족' : '미충족' }}
                        </span>
                    </div>
                    <div class="bo-certified-condition-count">
                        {{ $acquisitionSummary['winter_count'] }} / 1회
                    </div>
                    <p class="bo-certified-condition-note">1차·2차·3차 모두 수료 시 1회 인정</p>
                </div>
                <div class="bo-certified-condition-card">
                    <div class="bo-certified-condition-head">
                        <strong>취득 조건</strong>
                        <span class="bo-certified-status {{ $acquisitionSummary['completed'] ? 'is-complete' : 'is-incomplete' }}">
                            {{ $acquisitionSummary['completed'] ? '충족' : '미충족' }}
                        </span>
                    </div>
                    <div class="bo-certified-condition-count">
                        {{ optional($acquisitionSummary['completed_at'])->format('Y-m-d') ?? '-' }}
                    </div>
                    <p class="bo-certified-condition-note">정기 연수강좌와 동계 연수강좌를 모두 충족해야 합니다.</p>
                </div>
            </div>
        @endif
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">취득일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="acquired_date" class="board-form-control board-form-control--max-md" value="{{ old('acquired_date', optional($certifiedMember->acquired_date ?? null)->format('Y-m-d')) }}" @readonly($certifiedMember->exists)>
                        @error('acquired_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                @if ($certifiedMember->exists)
                    <div class="bo-form-row">
                        <label class="bo-form-label">유효기간</label>
                        <div class="bo-form-field">
                            <input type="text" class="board-form-control bo-readonly-box board-form-control--max-md" value="{{ $initialPeriod }}" readonly>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">최초 유효 시작일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="acquired_validity_start" class="board-form-control board-form-control--max-md" value="{{ old('acquired_validity_start', optional($certifiedMember->acquired_validity_start ?? null)->format('Y-m-d')) }}" @readonly($certifiedMember->exists)>
                        @error('acquired_validity_start')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">최초 유효 종료일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="acquired_validity_end" class="board-form-control board-form-control--max-md" value="{{ old('acquired_validity_end', optional($certifiedMember->acquired_validity_end ?? null)->format('Y-m-d')) }}" @readonly($certifiedMember->exists)>
                        @error('acquired_validity_end')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @if ($certifiedMember->exists)
            <h4 class="bo-certified-subtitle mt-4">갱신 이력</h4>
            @if ($renewalSummary)
                <div class="bo-certified-condition-grid">
                    <div class="bo-certified-condition-card">
                        <div class="bo-certified-condition-head">
                            <strong>춘·추계·하계 참석</strong>
                            <span class="bo-certified-status {{ $renewalSummary['general_count'] >= $renewalSummary['general_required'] ? 'is-complete' : 'is-incomplete' }}">
                                {{ $renewalSummary['general_count'] >= $renewalSummary['general_required'] ? '충족' : '미충족' }}
                            </span>
                        </div>
                        <div class="bo-certified-condition-count">
                            {{ $renewalSummary['general_count'] }} / {{ $renewalSummary['general_required'] }}회
                        </div>
                        <p class="bo-certified-condition-note">춘계·추계 학술대회와 하계 연수강좌 합산</p>
                    </div>
                    <div class="bo-certified-condition-card">
                        <div class="bo-certified-condition-head">
                            <strong>동계 연수강좌</strong>
                            <span class="bo-certified-status {{ $renewalSummary['winter_count'] >= $renewalSummary['winter_required'] ? 'is-complete' : 'is-incomplete' }}">
                                {{ $renewalSummary['winter_count'] >= $renewalSummary['winter_required'] ? '충족' : '미충족' }}
                            </span>
                        </div>
                        <div class="bo-certified-condition-count">
                            {{ $renewalSummary['winter_count'] }} / {{ $renewalSummary['winter_required'] }}회
                        </div>
                        <p class="bo-certified-condition-note">차시 3개 수강 시 1회 인정</p>
                    </div>
                    <div class="bo-certified-condition-card">
                        <div class="bo-certified-condition-head">
                            <strong>갱신 조건</strong>
                            <span class="bo-certified-status {{ $renewalSummary['completed'] ? 'is-complete' : 'is-incomplete' }}">
                                {{ $renewalSummary['completed'] ? '충족' : '미충족' }}
                            </span>
                        </div>
                        <div class="bo-certified-condition-count">
                            {{ optional($renewalSummary['completed_at'])->format('Y-m-d') ?? '-' }}
                        </div>
                        <p class="bo-certified-condition-note">현재 유효기간 {{ $renewalSummary['validity_period'] ?: '-' }} 기준</p>
                    </div>
                </div>
            @endif
            <div class="bo-form-row">
                <label class="bo-form-label">이력 목록</label>
                <div class="bo-form-field">
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th>갱신일</th>
                                    <th>유효기간</th>
                                    <th>춘·추계·하계 참석</th>
                                    <th>동계연수 참석</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($certifiedMember->renewals as $history)
                                    <tr>
                                        <td>{{ optional($history->renewal_date)->format('Y-m-d') }}</td>
                                        <td>{{ optional($history->renewal_validity_start)->format('Y-m-d') }} ~ {{ optional($history->renewal_validity_end)->format('Y-m-d') }}</td>
                                        <td>{{ $history->attendance_general }}회</td>
                                        <td>{{ $history->attendance_winter }}회</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">갱신 이력이 없습니다.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

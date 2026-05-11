<div class="bo-form-section">
    <h3 class="bo-section-title">1. 회원 선택</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label">회원 <span class="required">*</span></label>
            <div class="bo-form-field">
                @php
                    $selectedMember = $executive->member;
                    $selectedMemberId = old('member_id', $executive->member_id);
                    $selectedMemberLabel = old('member_label');

                    if ($selectedMemberLabel === null && $selectedMember) {
                        $selectedMemberLabel = $selectedMember->name . ' (' . ($selectedMember->login_id ?? '-') . ' / ' . ($selectedMember->email ?? '-') . ')';
                    }
                @endphp
                <div class="input-with-button bo-member-select-inline js-member-selector" data-search-url="{{ route('backoffice.member-executives.search-members') }}">
                    <input type="hidden" name="member_id" class="js-member-id" value="{{ $selectedMemberId }}">
                    <input type="hidden" name="member_label" class="js-member-label" value="{{ $selectedMemberLabel }}">
                    <input type="text" class="board-form-control js-member-display" value="{{ $selectedMemberLabel }}" placeholder="회원 검색 버튼을 눌러 선택하세요." readonly>
                    <button type="button" class="btn btn-secondary btn-sm js-open-member-modal">회원 검색</button>
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
    <h3 class="bo-section-title">2. 직책 및 임기 설정</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label">직책 부여 <span class="required">*</span></label>
            <div class="bo-form-field">
                <div class="board-radio-group bo-radio-group-wrap">
                    @foreach ($roleLabels as $code => $label)
                        <div class="board-radio-item">
                            <input type="radio" id="executive_role_{{ $code }}" name="executive_role" value="{{ $code }}" class="board-radio-input"
                                @checked(old('executive_role', $executive->executive_role) === $code)>
                            <label for="executive_role_{{ $code }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                @error('executive_role')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">임기 시작일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="term_start_date" class="board-form-control board-form-control--max-md" value="{{ old('term_start_date', optional($executive->term_start_date)->format('Y-m-d')) }}">
                        @error('term_start_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">임기 종료일</label>
                    <div class="bo-form-field">
                        <input type="date" name="term_end_date" id="term_end_date" class="board-form-control board-form-control--max-md" value="{{ old('term_end_date', optional($executive->term_end_date)->format('Y-m-d')) }}">
                        @error('term_end_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">임기 종료일 없음</label>
            <div class="bo-form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_indefinite" value="1" @checked(old('is_indefinite', $executive->is_indefinite))>
                    <span>무기한</span>
                </label>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">사용 여부</label>
            <div class="bo-form-field">
                <div class="board-radio-group bo-radio-group-wrap">
                    <div class="board-radio-item">
                        <input type="radio" id="is_active_1" name="is_active" value="1" class="board-radio-input" @checked((string) old('is_active', (int) $executive->is_active) === '1')>
                        <label for="is_active_1">사용</label>
                    </div>
                    <div class="board-radio-item">
                        <input type="radio" id="is_active_0" name="is_active" value="0" class="board-radio-input" @checked((string) old('is_active', (int) $executive->is_active) === '0')>
                        <label for="is_active_0">미사용</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">비고</label>
            <div class="bo-form-field">
                <textarea name="note" rows="3" class="board-form-control">{{ old('note', $executive->note) }}</textarea>
            </div>
        </div>
    </div>
</div>



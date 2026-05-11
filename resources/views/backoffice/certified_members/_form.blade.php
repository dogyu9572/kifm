<div class="bo-form-section">
    <h3 class="bo-section-title">1. 회원 선택</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label">회원 <span class="required">*</span></label>
            <div class="bo-form-field">
                @php
                    $selectedMember = $certifiedMember->member ?? null;
                    $selectedMemberId = old('member_id', $certifiedMember->member_id ?? '');
                    $selectedMemberLabel = old('member_label');

                    if ($selectedMemberLabel === null && $selectedMember) {
                        $selectedMemberLabel = $selectedMember->name . ' (' . ($selectedMember->login_id ?? '-') . ' / ' . ($selectedMember->email ?? '-') . ')';
                    }
                @endphp
                <div class="input-with-button bo-member-select-inline js-member-selector" data-search-url="{{ route('backoffice.certified-members.search-members') }}">
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
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">최초 취득일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="acquired_date" class="board-form-control board-form-control--max-md" value="{{ old('acquired_date', optional($certifiedMember->acquired_date ?? null)->format('Y-m-d')) }}">
                        @error('acquired_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col"></div>
        </div>
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label">최초 유효 시작일 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="date" name="acquired_validity_start" class="board-form-control board-form-control--max-md" value="{{ old('acquired_validity_start', optional($certifiedMember->acquired_validity_start ?? null)->format('Y-m-d')) }}">
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
                        <input type="date" name="acquired_validity_end" class="board-form-control board-form-control--max-md" value="{{ old('acquired_validity_end', optional($certifiedMember->acquired_validity_end ?? null)->format('Y-m-d')) }}">
                        @error('acquired_validity_end')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="bo-form-row">
            <label class="bo-form-label">취득 조건</label>
            <div class="bo-form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="winter_course_completed" value="1" @checked((bool) old('winter_course_completed', $certifiedMember->winter_course_completed ?? false))>
                    <span>동계 연수강좌 조건 충족</span>
                </label>
                <label class="checkbox-label ml-3">
                    <input type="checkbox" name="exam_passed" value="1" @checked((bool) old('exam_passed', $certifiedMember->exam_passed ?? false))>
                    <span>시험 합격</span>
                </label>
            </div>
        </div>

        @if (isset($certifiedMember))
            <div class="bo-form-row">
                <label class="bo-form-label">갱신 이력</label>
                <div class="bo-form-field">
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th>차수</th>
                                    <th>갱신일</th>
                                    <th>유효기간</th>
                                    <th>춘/추/하계 참석</th>
                                    <th>동계 참석</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($certifiedMember->renewals as $renewal)
                                    <tr>
                                        <td>{{ $renewal->renewal_seq }}차</td>
                                        <td>{{ optional($renewal->renewal_date)->format('Y-m-d') }}</td>
                                        <td>{{ optional($renewal->renewal_validity_start)->format('Y-m-d') }} ~ {{ optional($renewal->renewal_validity_end)->format('Y-m-d') }}</td>
                                        <td>{{ $renewal->attendance_general }}회</td>
                                        <td>{{ $renewal->attendance_winter }}회</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">갱신 이력이 없습니다.</td>
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


@php
    $defaultMembers = collect($addressBook?->members ?? [])->map(fn($member) => [
        'member_id' => $member->member_id,
        'name' => $member->name,
        'login_id' => $member->login_id,
        'email' => $member->email,
        'phone' => $member->phone,
        'source_type' => $member->source_type,
    ])->values()->all();
    $membersValue = old('members', $defaultMembers);
    if (is_string($membersValue)) {
        $decodedMembersValue = json_decode($membersValue, true);
        $membersValue = is_array($decodedMembersValue) ? $decodedMembersValue : [];
    }
    $membersSourceValue = ($membersSource ?? collect())->map(function ($member) {
        return [
            'member_id' => $member->id,
            'name' => $member->name,
            'login_id' => $member->login_id,
            'email' => $member->email,
            'phone' => $member->phone_number,
            'source_type' => 'SEARCH',
        ];
    })->values();
@endphp

<div class="board-form-group">
    <label for="name" class="board-form-label">주소록명 <span class="required">*</span></label>
    <input type="text" id="name" name="name" class="board-form-control" value="{{ old('name', $addressBook?->name ?? '') }}" required>
</div>

<div class="board-form-group">
    <label class="board-form-label">회원 검색/추가</label>
    <div class="input-with-button bo-member-select-inline js-address-member-selector" data-search-url="{{ route('backoffice.address-books.search-members') }}">
        <button type="button" id="address_member_search_btn" class="btn btn-secondary btn-sm">
            <i class="fas fa-search"></i> 회원 검색
        </button>
    </div>
</div>

<div class="board-form-group">
    <label class="board-form-label">추가 대상 목록</label>
    <div id="address_selected_members" class="board-attachment-list"></div>
</div>

<input type="hidden" id="address_members_input" name="members" value='@json($membersValue)'>
<input type="hidden" id="address_members_source" value='@json($membersSourceValue)'>

<div class="modal js-address-member-modal bo-member-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">회원 검색</h5>
            <button type="button" class="close js-close-address-member-modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="board-filter member-search-modal-filter">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="address_member_search_field" class="filter-label">검색 구분</label>
                        <select id="address_member_search_field" class="filter-select js-address-member-search-field">
                            <option value="all">전체</option>
                            <option value="id">ID</option>
                            <option value="name">이름</option>
                            <option value="phone">휴대폰</option>
                            <option value="email">이메일</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="address_member_keyword" class="filter-label">검색어</label>
                        <input type="text" id="address_member_keyword" class="filter-input js-address-member-keyword" placeholder="검색어를 입력하세요">
                    </div>
                    <div class="filter-group">
                        <div class="filter-buttons">
                            <button type="button" class="btn btn-primary js-search-address-member">
                                <i class="fas fa-search"></i> 검색
                            </button>
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
                    <tbody class="js-address-member-results">
                        <tr>
                            <td colspan="6" class="text-center">검색 버튼을 눌러 회원을 조회하세요.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 js-address-member-pagination"></div>
        </div>
    </div>
</div>

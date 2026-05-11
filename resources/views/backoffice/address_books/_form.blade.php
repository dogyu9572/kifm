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
    <div class="board-options-list board-options-horizontal">
        <input type="text" id="address_member_keyword" class="board-form-control" placeholder="이름/아이디/이메일/연락처">
        <button type="button" id="address_member_search_btn" class="btn btn-secondary">검색</button>
    </div>
    <div id="address_member_search_result" class="board-attachment-list mt-2"></div>
</div>

<div class="board-form-group">
    <label class="board-form-label">추가 대상 목록</label>
    <div id="address_selected_members" class="board-attachment-list"></div>
</div>

<input type="hidden" id="address_members_input" name="members" value='@json($membersValue)'>
<input type="hidden" id="address_members_source" value='@json($membersSourceValue)'>

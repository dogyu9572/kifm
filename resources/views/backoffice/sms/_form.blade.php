@php
    $selectedAddressBooks = old('selected_address_books', $selectedAddressBooks ?? []);
    $selectedMemberIds = old('selected_member_ids', $selectedMemberIds ?? []);
@endphp

<div class="board-form-group">
    <label for="sender_number" class="board-form-label">발신 번호</label>
    <input type="text" id="sender_number" name="sender_number" class="board-form-control" value="{{ old('sender_number', $smsMessage?->sender_number ?? '') }}">
</div>

<div class="board-form-group">
    <label class="board-form-label">수신대상</label>
    <div class="board-options-list board-options-horizontal">
        @foreach (['all' => '전체 회원', 'addressbook' => '주소록 선택', 'specific' => '특정 회원 선택'] as $value => $label)
            <div class="board-option-item">
                <input type="radio" id="sms_recipient_type_{{ $value }}" name="recipient_type" value="{{ $value }}" @checked(old('recipient_type', $smsMessage?->recipient_type ?? 'all') === $value)>
                <label for="sms_recipient_type_{{ $value }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="board-form-group js-sms-recipient-panel" data-panel="addressbook">
    <label for="sms_address_book_select" class="board-form-label">주소록 선택</label>
    <div class="board-options-list board-options-horizontal">
        <select id="sms_address_book_select" class="board-form-control">
            <option value="">주소록을 선택하세요</option>
            @foreach(($addressBooks ?? collect()) as $book)
                <option value="{{ $book->id }}">{{ $book->name }} ({{ $book->member_count }}명)</option>
            @endforeach
        </select>
        <button type="button" id="sms_add_address_book_btn" class="btn btn-secondary">추가</button>
    </div>
    <div id="sms_selected_address_books" class="board-attachment-list mt-2"></div>
</div>

<div class="board-form-group js-sms-recipient-panel" data-panel="specific">
    <label class="board-form-label">특정 회원 선택</label>
    <div class="board-options-list board-options-horizontal">
        <input type="text" id="sms_member_search_keyword" class="board-form-control" placeholder="이름/연락처 검색">
        <button type="button" id="sms_member_search_btn" class="btn btn-secondary">검색</button>
    </div>
    <div id="sms_member_search_result" class="board-attachment-list mt-2"></div>
</div>

<input type="hidden" id="sms_selected_address_books_input" name="selected_address_books" value='@json($selectedAddressBooks)'>
<input type="hidden" id="sms_selected_member_ids_input" name="selected_member_ids" value='@json($selectedMemberIds)'>
<input type="hidden" id="sms_members_source" value='@json(($members ?? collect())->map(fn($m) => ["id" => $m->id, "name" => $m->name, "phone" => $m->phone_number])->values())'>
<input type="hidden" id="sms_address_books_source" value='@json(($addressBooks ?? collect())->map(fn($b) => ["id" => $b->id, "name" => $b->name, "member_count" => $b->member_count])->values())'>

<div class="board-form-group">
    <label for="member_grade" class="board-form-label">회원등급</label>
    <select id="member_grade" name="member_grade" class="board-form-control">
        <option value="">전체</option>
        <option value="junior" @selected(old('member_grade', $smsMessage?->member_grade ?? '') === 'junior')>준회원</option>
        <option value="regular" @selected(old('member_grade', $smsMessage?->member_grade ?? '') === 'regular')>정회원</option>
        <option value="lifetime" @selected(old('member_grade', $smsMessage?->member_grade ?? '') === 'lifetime')>평생회원</option>
        <option value="senior" @selected(old('member_grade', $smsMessage?->member_grade ?? '') === 'senior')>시니어회원</option>
    </select>
</div>

<div class="board-form-group">
    <label for="exclude_phones" class="board-form-label">발송 제외 연락처</label>
    <input type="text" id="exclude_phones" name="exclude_phones" class="board-form-control" value="{{ old('exclude_phones', $smsMessage?->exclude_phones ?? '') }}">
</div>

<div class="board-form-group">
    <div class="board-checkbox-item">
        <input type="checkbox" id="sms_schedule_enabled" name="schedule_enabled" value="1" @checked((bool) old('schedule_enabled', $smsMessage?->schedule_enabled ?? false))>
        <label for="sms_schedule_enabled" class="board-form-label">예약 발송</label>
    </div>
</div>

<div class="board-form-group js-sms-scheduled-at-wrap">
    <label for="sms_scheduled_at" class="board-form-label">예약 일시</label>
    <input type="datetime-local" id="sms_scheduled_at" name="scheduled_at" class="board-form-control" value="{{ old('scheduled_at', isset($smsMessage?->scheduled_at) ? $smsMessage->scheduled_at->format('Y-m-d\TH:i') : '') }}">
</div>

<div class="board-form-group">
    <label for="subject" class="board-form-label">제목</label>
    <input type="text" id="subject" name="subject" class="board-form-control" maxlength="200" value="{{ old('subject', $smsMessage?->subject ?? '') }}">
</div>

<div class="board-form-group">
    <label for="body" class="board-form-label">발송 내용</label>
    <textarea id="body" name="body" rows="8" maxlength="2000" class="board-form-control board-form-textarea">{{ old('body', $smsMessage?->body ?? '') }}</textarea>
    <small class="board-form-text"><span id="sms_byte_count">0</span> / 2000 Byte</small>
</div>

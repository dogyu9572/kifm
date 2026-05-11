@php
    $selectedAddressBooks = old('selected_address_books', $selectedAddressBooks ?? []);
    $selectedMemberIds = old('selected_member_ids', $selectedMemberIds ?? []);
    $selectedExecutiveIds = old('selected_executive_ids', $selectedExecutiveIds ?? []);
@endphp

<div class="board-form-group">
    <label for="sender_name" class="board-form-label">발신자 이름</label>
    <input type="text" id="sender_name" name="sender_name" class="board-form-control" value="{{ old('sender_name', $mail?->sender_name ?? '') }}">
</div>

<div class="board-form-group">
    <label for="sender_email" class="board-form-label">발신자 메일주소</label>
    <input type="email" id="sender_email" name="sender_email" class="board-form-control" value="{{ old('sender_email', $mail?->sender_email ?? '') }}">
</div>

<div class="board-form-group">
    <label class="board-form-label">수신대상</label>
    <div class="board-options-list board-options-horizontal">
        @foreach (['all' => '전체 회원', 'addressbook' => '주소록 선택', 'specific' => '특정 회원 선택', 'executive' => '임원'] as $value => $label)
            <div class="board-option-item">
                <input type="radio" id="recipient_type_{{ $value }}" name="recipient_type" value="{{ $value }}" @checked(old('recipient_type', $mail?->recipient_type ?? 'all') === $value)>
                <label for="recipient_type_{{ $value }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="board-form-group js-recipient-panel" data-panel="addressbook">
    <label for="address_book_select" class="board-form-label">주소록 선택</label>
    <div class="board-options-list board-options-horizontal">
        <select id="address_book_select" class="board-form-control">
            <option value="">주소록을 선택하세요</option>
            @foreach(($addressBooks ?? collect()) as $book)
                <option value="{{ $book->id }}">{{ $book->name }} ({{ $book->member_count }}명)</option>
            @endforeach
        </select>
        <button type="button" id="add_address_book_btn" class="btn btn-secondary">추가</button>
    </div>
    <div id="selected_address_books" class="board-attachment-list mt-2"></div>
</div>

<div class="board-form-group js-recipient-panel" data-panel="specific">
    <label class="board-form-label">특정 회원 선택</label>
    <div class="board-options-list board-options-horizontal">
        <input type="text" id="member_search_keyword" class="board-form-control" placeholder="이름/이메일 검색">
        <button type="button" id="member_search_btn" class="btn btn-secondary">검색</button>
    </div>
    <div id="member_search_result" class="board-attachment-list mt-2"></div>
</div>

<div class="board-form-group js-recipient-panel" data-panel="executive">
    <label class="board-form-label">임원 선택</label>
    <div class="board-options-list board-options-horizontal">
        <input type="text" id="executive_search_keyword" class="board-form-control" placeholder="이름/이메일 검색">
        <button type="button" id="executive_search_btn" class="btn btn-secondary">검색</button>
    </div>
    <div id="executive_search_result" class="board-attachment-list mt-2"></div>
</div>

<input type="hidden" id="selected_address_books_input" name="selected_address_books" value='@json($selectedAddressBooks)'>
<input type="hidden" id="selected_member_ids_input" name="selected_member_ids" value='@json($selectedMemberIds)'>
<input type="hidden" id="selected_executive_ids_input" name="selected_executive_ids" value='@json($selectedExecutiveIds)'>
<input type="hidden" id="mail_members_source" value='@json(($members ?? collect())->map(fn($m) => ["id" => $m->id, "name" => $m->name, "email" => $m->email])->values())'>
<input type="hidden" id="mail_executives_source" value='@json(($executives ?? collect())->map(fn($e) => ["id" => $e->id, "name" => $e->name, "email" => $e->email])->values())'>
<input type="hidden" id="mail_address_books_source" value='@json(($addressBooks ?? collect())->map(fn($b) => ["id" => $b->id, "name" => $b->name, "member_count" => $b->member_count])->values())'>

<div class="board-form-group">
    <label for="member_grade" class="board-form-label">회원등급</label>
    <select id="member_grade" name="member_grade" class="board-form-control">
        <option value="">전체</option>
        <option value="junior" @selected(old('member_grade', $mail?->member_grade ?? '') === 'junior')>준회원</option>
        <option value="regular" @selected(old('member_grade', $mail?->member_grade ?? '') === 'regular')>정회원</option>
        <option value="lifetime" @selected(old('member_grade', $mail?->member_grade ?? '') === 'lifetime')>평생회원</option>
        <option value="senior" @selected(old('member_grade', $mail?->member_grade ?? '') === 'senior')>시니어회원</option>
    </select>
</div>

<div class="board-form-group">
    <label for="exclude_emails" class="board-form-label">발송 제외 이메일</label>
    <input type="text" id="exclude_emails" name="exclude_emails" class="board-form-control" value="{{ old('exclude_emails', $mail?->exclude_emails ?? '') }}" placeholder="쉼표(,)로 구분하여 입력">
</div>

<div class="board-form-group">
    <div class="board-checkbox-item">
        <input type="checkbox" id="schedule_enabled" name="schedule_enabled" value="1" @checked((bool) old('schedule_enabled', $mail?->schedule_enabled ?? false))>
        <label for="schedule_enabled" class="board-form-label">예약 발송</label>
    </div>
</div>

<div class="board-form-group js-scheduled-at-wrap">
    <label for="scheduled_at" class="board-form-label">예약 일시</label>
    <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="board-form-control" value="{{ old('scheduled_at', isset($mail?->scheduled_at) ? $mail->scheduled_at->format('Y-m-d\TH:i') : '') }}">
</div>

<div class="board-form-group">
    <label class="board-form-label">메일 유형</label>
    <div class="board-options-list board-options-horizontal">
        <div class="board-option-item">
            <input type="radio" id="mail_type_bulk" name="mail_type" value="bulk" @checked(old('mail_type', $mail?->mail_type ?? 'bulk') === 'bulk')>
            <label for="mail_type_bulk">일반 단체 메일</label>
        </div>
        <div class="board-option-item">
            <input type="radio" id="mail_type_newsletter" name="mail_type" value="newsletter" @checked(old('mail_type', $mail?->mail_type ?? 'bulk') === 'newsletter')>
            <label for="mail_type_newsletter">뉴스레터</label>
        </div>
    </div>
</div>

<div class="board-form-group">
    <label for="subject" class="board-form-label">제목</label>
    <input type="text" id="subject" name="subject" class="board-form-control" maxlength="200" value="{{ old('subject', $mail?->subject ?? '') }}">
    <small class="board-form-text"><span id="subject_count">0</span> / 200</small>
</div>

<div class="board-form-group">
    <label for="body" class="board-form-label">내용</label>
    <textarea id="body" name="body" rows="12" class="board-form-control board-form-textarea">{{ old('body', $mail?->body ?? '') }}</textarea>
</div>

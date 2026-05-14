@php
    $isEdit = $abstract->exists;
    $regBy = old('registered_by', $abstract->registered_by ?? 'user');
    $selectedEventId = old('academic_event_id', $abstract->academic_event_id);
    $selectedMember = $abstract->member ?? null;
    $selectedMemberId = old('member_id', $abstract->member_id);
    $selectedMemberLabel = old('member_label');
    if ($selectedMemberLabel === null) {
        $selectedMemberLabel = old('member_display');
    }
    if ($selectedMemberLabel === null && $selectedMember) {
        $selectedMemberLabel = $selectedMember->name . ' (' . ($selectedMember->login_id ?? '-') . ' / ' . ($selectedMember->email ?? '-') . ')';
    }
    $memberBlockHidden = $regBy === 'user' ? ' d-none' : '';
@endphp

<div class="board-form-group">
    <label class="board-form-label" for="academic_event_id">행사 <span class="required">*</span></label>
    <select name="academic_event_id" id="academic_event_id" class="board-form-control bo-ae-abs-event-select" required>
        <option value="">-- 행사를 선택하세요 --</option>
        @foreach ($events as $ev)
            <option value="{{ $ev->id }}" @selected((string) $selectedEventId === (string) $ev->id)>
                {{ $ev->year }} {{ $ev->title }}
            </option>
        @endforeach
    </select>
</div>

<div class="board-form-group">
    <span class="board-form-label">상태 <span class="required">*</span></span>
    <div class="board-radio-group bo-radio-group-wrap">
        @foreach ($statusLabels as $code => $label)
            <div class="board-radio-item">
                <input type="radio" id="status_{{ $code }}" name="status" value="{{ $code }}" class="board-radio-input" @checked(old('status', $abstract->status) === $code)>
                <label for="status_{{ $code }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="board-form-group">
    <span class="board-form-label">파일 수령 여부 <span class="required">*</span></span>
    <div class="board-radio-group bo-radio-group-wrap">
        @foreach ($fileReceiptLabels as $code => $label)
            <div class="board-radio-item">
                <input type="radio" id="file_receipt_{{ $code }}" name="file_receipt_status" value="{{ $code }}" class="board-radio-input" @checked(old('file_receipt_status', $abstract->file_receipt_status) === $code)>
                <label for="file_receipt_{{ $code }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="board-form-group">
    <span class="board-form-label">등록 구분 <span class="required">*</span></span>
    <div class="board-radio-group bo-radio-group-wrap bo-ae-abs-reg-inline">
        @foreach ($registeredByLabels as $code => $label)
            <div class="board-radio-item">
                <input type="radio" id="registered_by_{{ $code }}" name="registered_by" value="{{ $code }}" class="board-radio-input bo-ae-abs-registered-by" @checked($regBy === $code)>
                <label for="registered_by_{{ $code }}">{{ $label }}</label>
            </div>
        @endforeach
        <button type="button" id="bo-ae-abs-db-btn" class="btn btn-outline-secondary btn-sm @if ($regBy === 'user') d-none @endif">
            회원 DB 연동
        </button>
    </div>
</div>

<div class="board-form-group">
    <span class="board-form-label">등록자 <span class="required">*</span></span>
    <div class="js-ae-abstract-member-wrap{{ $memberBlockHidden }}">
        <div class="input-with-button bo-member-select-inline js-member-selector" data-search-url="{{ route('backoffice.academic-event-abstracts.search-members') }}">
            <input type="hidden" name="member_id" class="js-member-id" value="{{ $selectedMemberId }}">
            <input type="hidden" name="member_label" class="js-member-label" value="{{ $selectedMemberLabel }}">
            <input type="text" class="board-form-control js-member-display" value="{{ $selectedMemberLabel }}" placeholder="회원을 검색하세요" readonly>
            <button type="button" class="btn btn-outline-secondary js-open-member-modal">회원 조회</button>
        </div>
    </div>
    <p id="bo-ae-abs-reg-guide" class="board-form-text">
        @if ($regBy === 'admin')
            관리자 등록 시 &lsquo;회원 DB 연동&rsquo; 또는 &lsquo;회원 조회&rsquo; 버튼을 클릭하여 회원 계정을 선택하세요.
        @else
            사용자(본인) 정보가 입력됩니다.
        @endif
    </p>
</div>

<div class="modal js-member-modal bo-member-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">회원 검색 (회원 DB)</h5>
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

<div class="board-form-row">
    <div class="board-form-col">
        <div class="board-form-group mb-0">
            <label class="board-form-label" for="author_name">이름(국문) <span class="required">*</span></label>
            <input type="text" name="author_name" id="author_name" class="board-form-control" value="{{ old('author_name', $abstract->author_name) }}" required>
        </div>
    </div>
    <div class="board-form-col">
        <div class="board-form-group mb-0">
            <label class="board-form-label" for="author_name_en">이름(영문)</label>
            <input type="text" name="author_name_en" id="author_name_en" class="board-form-control" value="{{ old('author_name_en', $abstract->author_name_en) }}">
        </div>
    </div>
</div>

<div class="board-form-row">
    <div class="board-form-col">
        <div class="board-form-group mb-0">
            <label class="board-form-label" for="author_phone">전화번호</label>
            <input type="text" name="author_phone" id="author_phone" class="board-form-control" value="{{ old('author_phone', $abstract->author_phone) }}" placeholder="02-0000-0000">
        </div>
    </div>
    <div class="board-form-col">
        <div class="board-form-group mb-0">
            <label class="board-form-label" for="author_mobile">휴대폰 번호</label>
            <input type="text" name="author_mobile" id="author_mobile" class="board-form-control" value="{{ old('author_mobile', $abstract->author_mobile) }}">
        </div>
    </div>
</div>

<div class="board-form-group">
    <label class="board-form-label" for="author_email">이메일</label>
    <input type="email" name="author_email" id="author_email" class="board-form-control" value="{{ old('author_email', $abstract->author_email) }}">
</div>

<hr class="bo-ae-abs-section-hr">

<div class="board-form-group">
    <label class="board-form-label" for="abstract_title">제목 <span class="required">*</span></label>
    <input type="text" name="title" id="abstract_title" class="board-form-control" value="{{ old('title', $abstract->title) }}" required maxlength="500">
</div>

<div class="board-form-row">
    <div class="board-form-col">
        <div class="board-form-group mb-0">
            <label class="board-form-label" for="presentation_type">발표 구분 <span class="required">*</span></label>
            <select name="presentation_type" id="presentation_type" class="board-form-control" required>
                @foreach ($presentationTypeLabels as $code => $label)
                    <option value="{{ $code }}" @selected(old('presentation_type', $abstract->presentation_type) === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="board-form-col">
        <div class="board-form-group mb-0">
            <label class="board-form-label" for="academic_event_field_id">발표 분야</label>
            <select name="academic_event_field_id" id="academic_event_field_id" class="board-form-control">
                <option value="">-- 선택 --</option>
                @foreach ($events as $ev)
                    @if ($selectedEventId && (int) $selectedEventId === (int) $ev->id)
                        @foreach ($ev->fields as $f)
                            <option value="{{ $f->id }}" @selected((string) old('academic_event_field_id', $abstract->academic_event_field_id) === (string) $f->id)>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    @endif
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="board-form-group">
    <label class="board-form-label" for="note">비고</label>
    <textarea name="note" id="note" class="board-form-control bo-ae-abs-note" rows="5">{{ old('note', $abstract->note) }}</textarea>
</div>

<div class="board-form-group">
    <label class="board-form-label" for="submitted_at">접수일시 <span class="required">*</span></label>
    <input type="datetime-local" name="submitted_at" id="submitted_at" class="board-form-control board-form-control--max-md" value="{{ old('submitted_at', optional($abstract->submitted_at)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i')) }}" required>
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <span class="board-form-label">첨부파일</span>
        <div class="bo-attachment-row mb-2">
            <div class="board-file-upload bo-ae-abs-attachment-block">
                <div class="board-file-input-wrapper">
                    <input type="file" name="attachments[]" id="bo-ae-abs-attachments" class="board-file-input bo-attachment-file-input" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.hwp,.jpg,.jpeg,.png,.gif" data-max-file-size-mb="10">
                    <div class="board-file-input-content">
                        <i class="fas fa-paperclip"></i>
                        <span class="board-file-input-text">첨부파일을 선택하거나 여기로 드래그하세요</span>
                        <span class="board-file-input-subtext">PDF, DOC, 이미지 등 (최대 5개, 각 10MB 이하)</span>
                    </div>
                </div>
                @if ($isEdit && $abstract->files->isNotEmpty())
                    <div class="board-existing-files mt-2">
                        <div class="board-attachment-list">
                            @foreach ($abstract->files as $file)
                                <div class="board-attachment-item existing-file" data-index="{{ $file->id }}">
                                    <i class="fas fa-file"></i>
                                    <a href="{{ asset('storage/' . $file->stored_path) }}" target="_blank" rel="noopener">
                                        <span class="board-attachment-name">{{ $file->original_name }}</span>
                                    </a>
                                    <button type="button" class="board-attachment-remove" data-existing-abstract-file-id="{{ $file->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="board-file-preview" id="bo-ae-abs-attachment-preview"></div>
            </div>
        </div>
        <div id="bo-ae-abs-removed-files"></div>
        <small class="board-form-text d-block mt-1">※ PDF, DOC, PPT, ZIP, HWP, 이미지 등 업로드 가능 (최대 5개, 각 10MB)</small>
        @error('attachments')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
        @error('attachments.*')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

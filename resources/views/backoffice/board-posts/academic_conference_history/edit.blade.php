@extends('backoffice.layouts.app')

@section('title', $board->name ?? '학술대회 연혁')

@section('content')
@php
    $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
    if (! is_array($customFields)) {
        $customFields = [];
    }
    $existingAttachments = $post->attachments ? json_decode($post->attachments, true) : [];
    if (! is_array($existingAttachments)) {
        $existingAttachments = [];
    }
@endphp

<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_conference_history') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            @if ($errors->any())
                <div class="board-alert board-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('backoffice.board-posts.update', [$board->slug ?? 'academic_conference_history', $post->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="board-form-group">
                    <label for="custom_field_event_start_date" class="board-form-label">행사 시작일 <span class="required">*</span></label>
                    <input type="date" class="board-form-control" id="custom_field_event_start_date" name="custom_field_event_start_date" value="{{ old('custom_field_event_start_date', $customFields['event_start_date'] ?? '') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="custom_field_event_end_date" class="board-form-label">행사 종료일 <span class="required">*</span></label>
                    <input type="date" class="board-form-control" id="custom_field_event_end_date" name="custom_field_event_end_date" value="{{ old('custom_field_event_end_date', $customFields['event_end_date'] ?? '') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="title" class="board-form-label">행사명 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                </div>

                @if($board->enable_sorting)
                    <div class="board-form-group">
                        <label for="sort_order" class="board-form-label">정렬 순서</label>
                        <input type="number" class="board-form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $post->sort_order ?? 0) }}" min="0">
                        <small class="board-form-text">숫자가 클수록 위에 표시됩니다.</small>
                    </div>
                @endif

                <div class="board-form-group">
                    <label class="board-form-label">행사자료</label>
                    @if(count($existingAttachments) > 0)
                        <div class="board-attachment-list">
                            @foreach($existingAttachments as $index => $attachment)
                                @php
                                    $attachmentName = $attachment['name'] ?? '';
                                    $attachmentPath = $attachment['path'] ?? null;
                                    $attachmentSize = isset($attachment['size']) ? (int) $attachment['size'] : 0;
                                @endphp
                                <div class="board-attachment-item existing-file" data-index="{{ $index }}">
                                    @if(is_string($attachmentPath) && $attachmentPath !== '')
                                        <a href="{{ asset('storage/'.$attachmentPath) }}" download="{{ $attachmentName }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $attachmentName }}</a>
                                    @else
                                        <span class="board-attachment-name">{{ $attachmentName }}</span>
                                    @endif
                                    <span class="board-attachment-size">({{ number_format($attachmentSize / 1024 / 1024, 2) }}MB)</span>
                                    <button type="button" class="board-attachment-remove" data-existing-file-index="{{ $index }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <input type="hidden" name="existing_attachments[]" value="{{ json_encode($attachment) }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">최대 5개, 각 파일 10MB 이하</span>
                            </div>
                        </div>
                        <div class="board-file-preview" id="filePreview"></div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">공개여부 <span class="required">*</span></label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="is_active_public" name="is_active" value="1" @checked((string) old('is_active', (int) $post->is_active) === '1') required>
                            <label for="is_active_public">공개</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="is_active_private" name="is_active" value="0" @checked((string) old('is_active', (int) $post->is_active) === '0') required>
                            <label for="is_active_private">비공개</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_conference_history') }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/board-post-form.js') }}"></script>
@endsection

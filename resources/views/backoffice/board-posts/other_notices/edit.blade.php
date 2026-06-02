@extends('backoffice.layouts.app')

@section('title', $board->name ?? '기타 공지')

@section('content')
@php
    $returnUrl = request('return_url', route('backoffice.board-posts.index', $board->slug ?? 'other_notices'));
@endphp
<div class="board-container">
    <div class="board-header">
        <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('backoffice.board-posts.update', [$board->slug ?? 'other_notices', $post->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_url" value="{{ $returnUrl }}">

                <div class="board-form-group">
                    <div class="board-checkbox-item">
                        <input type="checkbox" class="board-checkbox-input" id="is_notice" name="is_notice" value="1" @checked(old('is_notice', $post->is_notice))>
                        <label for="is_notice" class="board-form-label">공지글</label>
                    </div>
                    <small class="board-form-text">체크 시 목록 최상단에 고정 표시됩니다.</small>
                </div>

                <div class="board-form-group">
                    <label for="title" class="board-form-label">제목 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="board-form-group">
                    <label for="content" class="board-form-label">내용 <span class="required">*</span></label>
                    <textarea class="board-form-control board-form-textarea" id="content" name="content" rows="15" data-backoffice-ckeditor data-source-editing="true" required>{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">첨부파일</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="attachments" name="attachments[]" data-max-file-size-mb="10" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">최대 5개, 각 파일 10MB 이하</span>
                            </div>
                        </div>

                        @if($post->attachments)
                            @php
                                $existingAttachments = json_decode($post->attachments, true);
                            @endphp
                            @if($existingAttachments && is_array($existingAttachments) && count($existingAttachments) > 0)
                                <div class="board-existing-files">
                                    <div class="board-attachment-list">
                                        @foreach($existingAttachments as $index => $attachment)
                                            @php
                                                $attachmentName = $attachment['name'] ?? '';
                                                $attachmentPath = $attachment['path'] ?? null;
                                                $attachmentSize = isset($attachment['size']) ? (int) $attachment['size'] : 0;
                                                $hasDownload = is_string($attachmentPath) && $attachmentPath !== '';
                                            @endphp
                                            <div class="board-attachment-item existing-file" data-index="{{ $index }}">
                                                <i class="fas fa-file"></i>
                                                @if ($hasDownload)
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
                                </div>
                            @endif
                        @endif
                        <div class="board-file-preview" id="filePreview"></div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label for="created_at" class="board-form-label">등록일</label>
                    <input type="datetime-local" class="board-form-control" id="created_at" name="created_at" value="{{ old('created_at', ($post->created_at ? \Illuminate\Support\Carbon::parse($post->created_at)->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i'))) }}">
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">공개 여부</label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="visibility_public" name="is_active" value="1" @checked((string) old('is_active', (string) ($post->is_active ?? 1)) === '1')>
                            <label for="visibility_public">공개</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="visibility_private" name="is_active" value="0" @checked((string) old('is_active', (string) ($post->is_active ?? 1)) === '0')>
                            <label for="visibility_private">비공개</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label for="author_name" class="board-form-label">작성자</label>
                    <input type="text" class="board-form-control" id="author_name" name="author_name" value="{{ old('author_name', $post->author_name ?? auth()->user()->name) }}">
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                                    <a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <x-backoffice-ckeditor-assets />
    <script src="{{ asset('js/backoffice/board-post-form.js') }}"></script>
@endsection

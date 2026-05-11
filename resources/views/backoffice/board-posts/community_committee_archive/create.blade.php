@extends('backoffice.layouts.app')

@section('title', ($board->name ?? '위원회 자료실').' 작성')

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'community_committee_archive') }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('backoffice.board-posts.store', $board->slug ?? 'community_committee_archive') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if($board->isNoticeEnabled())
                <div class="board-form-group">
                    <div class="board-checkbox-item">
                        <input type="checkbox" class="board-checkbox-input" id="is_notice" name="is_notice" value="1" @checked(old('is_notice') == '1')>
                        <label for="is_notice" class="board-form-label">공지글</label>
                    </div>
                    <small class="board-form-text">체크 시 목록 최상단에 고정 표시됩니다.</small>
                </div>
                @endif

                @if($board->isFieldEnabled('category') && $categoryOptions && $categoryOptions->count() > 0)
                <div class="board-form-group">
                    <label for="category" class="board-form-label">
                        위원회
                        @if($board->isFieldRequired('category'))
                            <span class="required">*</span>
                        @endif
                    </label>
                    <select class="board-form-control" id="category" name="category" @if($board->isFieldRequired('category')) required @endif>
                        <option value="">위원회를 선택하세요</option>
                        @foreach($categoryOptions as $category)
                            <option value="{{ $category->name }}" @selected(old('category') == $category->name)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="board-form-group">
                    <label for="title" class="board-form-label">제목 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="content" class="board-form-label">내용 <span class="required">*</span></label>
                    <textarea class="board-form-control board-form-textarea" id="content" name="content" rows="15" data-backoffice-ckeditor data-source-editing="true" required>{{ old('content') }}</textarea>
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
                        <div class="board-file-preview" id="filePreview"></div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">공개 여부</label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="visibility_public" name="is_active" value="1" @checked((string) old('is_active', '1') === '1')>
                            <label for="visibility_public">공개</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="visibility_private" name="is_active" value="0" @checked((string) old('is_active') === '0')>
                            <label for="visibility_private">비공개</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label for="author_name" class="board-form-label">작성자</label>
                    <input type="text" class="board-form-control" id="author_name" name="author_name" value="{{ old('author_name', auth()->user()->name ?? '') }}">
                </div>

                <div class="board-form-group">
                    <label for="created_at" class="board-form-label">등록일</label>
                    <input type="datetime-local" class="board-form-control" id="created_at" name="created_at" value="{{ old('created_at', now()->format('Y-m-d\\TH:i')) }}">
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'community_committee_archive') }}" class="btn btn-secondary">취소</a>
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

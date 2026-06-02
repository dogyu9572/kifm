@extends('backoffice.layouts.app')

@section('title', $board->name ?? '회원 자료실')

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'member_archive') }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('backoffice.board-posts.store', $board->slug ?? 'member_archive') }}" method="POST" enctype="multipart/form-data">
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

                <div class="board-form-group">
                    <label class="board-form-label">자료 유형 <span class="required">*</span></label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="archive_type_ebook" name="custom_field_archive_type" value="ebook" @checked(old('custom_field_archive_type', 'ebook') === 'ebook') required>
                            <label for="archive_type_ebook">E-book (초록집)</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="archive_type_video" name="custom_field_archive_type" value="video" @checked(old('custom_field_archive_type') === 'video') required>
                            <label for="archive_type_video">강의 영상</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="archive_type_paper" name="custom_field_archive_type" value="paper" @checked(old('custom_field_archive_type') === 'paper') required>
                            <label for="archive_type_paper">논문/학술지</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="archive_type_guide" name="custom_field_archive_type" value="guide" @checked(old('custom_field_archive_type') === 'guide') required>
                            <label for="archive_type_guide">가이드라인</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">회원 설정 <span class="required">*</span></label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="member_grade_all" name="custom_field_member_grade" value="all" @checked(old('custom_field_member_grade', 'all') === 'all') required>
                            <label for="member_grade_all">전체 회원</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="member_grade_associate" name="custom_field_member_grade" value="associate" @checked(old('custom_field_member_grade') === 'associate') required>
                            <label for="member_grade_associate">준회원 이상</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="member_grade_regular" name="custom_field_member_grade" value="regular" @checked(old('custom_field_member_grade') === 'regular') required>
                            <label for="member_grade_regular">정회원 이상</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="member_grade_lifetime" name="custom_field_member_grade" value="lifetime" @checked(old('custom_field_member_grade') === 'lifetime') required>
                            <label for="member_grade_lifetime">평생회원</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="member_grade_senior" name="custom_field_member_grade" value="senior" @checked(old('custom_field_member_grade') === 'senior') required>
                            <label for="member_grade_senior">시니어회원</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label for="title" class="board-form-label">제목 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="content" class="board-form-label">내용 <span class="required">*</span></label>
                    <textarea class="board-form-control board-form-textarea" id="content" name="content" rows="15" data-backoffice-ckeditor data-source-editing="true" required>{{ old('content') }}</textarea>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">썸네일</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png,.gif">
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">1개, 파일 10MB 이하</span>
                            </div>
                        </div>
                        <div class="board-file-preview" id="thumbnailPreview"></div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">첨부파일</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="attachments" name="attachments[]" data-max-file-size-mb="20" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">최대 5개, 각 파일 20MB 이하</span>
                            </div>
                        </div>
                        <div class="board-file-preview" id="filePreview"></div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">공개여부 <span class="required">*</span></label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="visibility_public" name="is_active" value="1" @checked((string) old('is_active', '1') === '1') required>
                            <label for="visibility_public">공개</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="visibility_private" name="is_active" value="0" @checked((string) old('is_active') === '0') required>
                            <label for="visibility_private">비공개</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label for="author_name" class="board-form-label">작성자 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="author_name" name="author_name" value="{{ old('author_name', auth()->user()->name ?? '') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="created_at" class="board-form-label">등록일시</label>
                    <input type="datetime-local" class="board-form-control" id="created_at" name="created_at" value="{{ old('created_at', now()->format('Y-m-d\\TH:i')) }}">
                </div>

                <div class="board-form-group">
                    <label for="view_count" class="board-form-label">조회수</label>
                    <input type="number" class="board-form-control" id="view_count" name="view_count" value="{{ old('view_count', 0) }}" min="0">
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'member_archive') }}" class="btn btn-secondary">취소</a>
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

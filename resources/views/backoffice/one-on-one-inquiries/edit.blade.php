@extends('backoffice.layouts.app')

@section('title', '1:1 문의 상세')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>

        @if (session('success'))
            <div class="board-alert board-alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="board-alert board-alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="board-card">
            <div class="board-card-body">
                <div class="bo-form-section">
                    <h3 class="bo-section-title">문의 내용</h3>
                    <div class="bo-form-list">
                        <div class="bo-member-dual-row">
                            <div class="bo-member-dual-col">
                                <div class="bo-form-row">
                                    <label class="bo-form-label">회원명</label>
                                    <div class="bo-form-field">
                                        <input type="text" class="board-form-control" value="{{ $inquiry->displayMemberName() ?: '-' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="bo-member-dual-col">
                                <div class="bo-form-row">
                                    <label class="bo-form-label">이메일</label>
                                    <div class="bo-form-field">
                                        <input type="text" class="board-form-control" value="{{ $inquiry->displayMemberEmail() ?: '-' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bo-form-row">
                            <label class="bo-form-label">문의일시</label>
                            <div class="bo-form-field">
                                <input type="text" class="board-form-control" value="{{ optional($inquiry->created_at)->format('Y-m-d H:i') ?: '-' }}" readonly>
                            </div>
                        </div>
                        <div class="bo-form-row">
                            <label class="bo-form-label">제목</label>
                            <div class="bo-form-field">
                                <input type="text" class="board-form-control" value="{{ $inquiry->title }}" readonly>
                            </div>
                        </div>
                        <div class="bo-form-row">
                            <label class="bo-form-label">내용</label>
                            <div class="bo-form-field">
                                <div class="bo-readonly-box">
                                    @if (($inquiry->content_format ?? 'html') === 'html' && ! empty($inquiry->content))
                                        {!! $inquiry->content !!}
                                    @elseif (! empty($inquiry->content))
                                        <pre>{{ $inquiry->content }}</pre>
                                    @else
                                        <span class="board-form-help">내용 없음</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bo-form-row">
                            <label class="bo-form-label">첨부파일</label>
                            <div class="bo-form-field">
                                @php
                                    $inquiryAttachments = is_array($inquiry->attachments) ? $inquiry->attachments : [];
                                @endphp
                                @if (count($inquiryAttachments) > 0)
                                    <ul>
                                        @foreach ($inquiryAttachments as $att)
                                            @php
                                                $name = is_array($att) ? ($att['original_name'] ?? ($att['path'] ?? '')) : (string) $att;
                                                $path = is_array($att) ? ($att['path'] ?? null) : null;
                                            @endphp
                                            <li>
                                                @if ($path)
                                                    <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener">{{ $name }}</a>
                                                @else
                                                    {{ $name }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="board-form-help">첨부파일 없음</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('backoffice.one-on-one-inquiries.update', $inquiry) }}" id="bo-one-on-one-inquiry-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="return_url" value="{{ request('return_url') }}">

                    <div class="bo-form-section">
                        <h3 class="bo-section-title">답변 작성</h3>
                        <div class="bo-form-list">
                            <div class="bo-form-row">
                                <span class="bo-form-label">답변 상태 <span class="required">*</span></span>
                                <div class="bo-form-field">
                                    <div class="board-radio-group">
                                        @foreach ($statusLabels as $code => $label)
                                            <label class="board-radio-item">
                                                <input type="radio" name="answer_status" value="{{ $code }}" class="js-answer-status"
                                                    @checked(old('answer_status', $inquiry->answer_status) === $code)>
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label for="answered_at" class="bo-form-label">답변 일시</label>
                                <div class="bo-form-field">
                                    <input type="datetime-local" name="answered_at" id="answered_at" class="board-form-control board-form-control--max-sm js-answered-at"
                                        value="{{ old('answered_at', optional($inquiry->answered_at)->format('Y-m-d\TH:i')) }}">
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label for="answer_content" class="bo-form-label">답변 내용 <span class="required">*</span></label>
                                <div class="bo-form-field">
                                    <textarea name="answer_content" id="answer_content" class="board-form-control board-form-textarea" rows="10" data-backoffice-ckeditor="true">{{ old('answer_content', $inquiry->answer_content) }}</textarea>
                                </div>
                            </div>
                            <div class="bo-form-row">
                                <label class="bo-form-label">답변 첨부</label>
                                <div class="bo-form-field">
                                    @php
                                        $answerAttachments = is_array($inquiry->answer_attachments) ? $inquiry->answer_attachments : [];
                                    @endphp
                                    <div class="bo-attachment-row mb-2">
                                        <div class="board-file-upload">
                                            <div class="board-file-input-wrapper">
                                                <input type="file" name="answer_attachments[]" class="board-file-input bo-attachment-file-input"
                                                    id="answer_attachments" multiple data-max-file-size-mb="10">
                                                <div class="board-file-input-content">
                                                    <i class="fas fa-paperclip"></i>
                                                    <span class="board-file-input-text">첨부파일을 선택하거나 여기로 드래그하세요</span>
                                                    <span class="board-file-input-subtext">최대 5개, 각 파일 10MB 이하</span>
                                                </div>
                                            </div>
                                            @if (count($answerAttachments) > 0)
                                                <div class="board-existing-files mt-2">
                                                    <div class="board-attachment-list">
                                                        @foreach ($answerAttachments as $index => $att)
                                                            @php
                                                                $name = is_array($att) ? ($att['original_name'] ?? ($att['path'] ?? '')) : (string) $att;
                                                                $path = is_array($att) ? ($att['path'] ?? null) : null;
                                                            @endphp
                                                            <div class="board-attachment-item existing-file" data-index="{{ $index }}">
                                                                <i class="fas fa-file"></i>
                                                                @if ($path)
                                                                    <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener">
                                                                        <span class="board-attachment-name">{{ $name }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="board-attachment-name">{{ $name }}</span>
                                                                @endif
                                                                <button type="button" class="board-attachment-remove" data-existing-attachment-index="{{ $index }}">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="board-file-preview" id="answerAttachmentFilePreview"></div>
                                        </div>
                                    </div>
                                    <div id="bo-removed-answer-attachments"></div>
                                    <small class="board-form-text d-block mt-1">※ 최대 5개, 각 파일 10MB 이하</small>
                                    @error('answer_attachments')
                                        <span class="bo-inline-error">{{ $message }}</span>
                                    @enderror
                                    @error('answer_attachments.*')
                                        <span class="bo-inline-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-backoffice-ckeditor-assets />
    <script src="{{ asset('js/backoffice/one-on-one-inquiries-form.js') }}"></script>
@endsection

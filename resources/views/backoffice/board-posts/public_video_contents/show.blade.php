@extends('backoffice.layouts.app')

@section('title', $board->name ?? '영상 콘텐츠')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
@endsection

@section('content')
    @php
        $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
        $youtubeUrl = $customFields['youtube_url'] ?? null;
        $youtubeEmbedUrl = null;

        if ($youtubeUrl) {
            if (preg_match('~youtube\.com/watch\?v=([A-Za-z0-9_-]+)~', $youtubeUrl, $matches)) {
                $youtubeEmbedUrl = 'https://www.youtube.com/embed/'.$matches[1];
            } elseif (preg_match('~youtu\.be/([A-Za-z0-9_-]+)~', $youtubeUrl, $matches)) {
                $youtubeEmbedUrl = 'https://www.youtube.com/embed/'.$matches[1];
            } elseif (preg_match('~youtube\.com/embed/([A-Za-z0-9_-]+)~', $youtubeUrl, $matches)) {
                $youtubeEmbedUrl = 'https://www.youtube.com/embed/'.$matches[1];
            }
        }
    @endphp

    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'public_video_contents') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> 목록으로
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-post-header">
                    <div class="board-post-title">
                        <h3>{{ $post->title }}</h3>
                    </div>
                    <div class="board-post-meta">
                        <span class="board-post-author">작성자: {{ $post->author_name ?? '알 수 없음' }}</span>
                        <span class="board-post-date">작성일: {{ $post->created_at->format('Y-m-d H:i') }}</span>
                        <span class="board-post-views">조회수: {{ $post->view_count ?? 0 }}</span>
                    </div>
                </div>

                @if ($youtubeUrl)
                    <div class="board-post-custom-fields">
                        <h6>유튜브 링크</h6>
                        <div class="board-custom-fields-list">
                            <div class="board-custom-field-item">
                                <span class="board-custom-field-value">
                                    <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener">{{ $youtubeUrl }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($youtubeEmbedUrl)
                    <div class="board-post-content">
                        <iframe width="100%" height="420" src="{{ $youtubeEmbedUrl }}" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
                    </div>
                @endif

                <div class="board-post-content">
                    {!! $post->content !!}
                </div>

                @if ($post->attachments)
                    <div class="board-post-attachments">
                        <h6>첨부파일</h6>
                        <div class="board-attachment-list">
                            @php
                                $attachments = json_decode($post->attachments, true);
                            @endphp
                            @if (is_array($attachments))
                                @foreach ($attachments as $attachment)
                                    <div class="board-attachment-item">
                                        <i class="fas fa-file"></i>
                                        <a href="{{ asset('storage/' . $attachment['path']) }}"
                                           class="board-attachment-link"
                                           target="_blank"
                                           download="{{ $attachment['name'] }}">
                                            {{ $attachment['name'] }}
                                        </a>
                                        <span class="board-attachment-size">
                                            ({{ number_format($attachment['size'] / 1024 / 1024, 2) }}MB)
                                        </span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                <div class="board-post-actions">
                    <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'public_video_contents', $post->id]) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> 수정
                    </a>
                    <form action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'public_video_contents', $post->id]) }}"
                          method="POST" class="d-inline js-delete-confirm-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> 삭제
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/board-posts.js') }}"></script>
@endsection

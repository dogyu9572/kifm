@extends('backoffice.layouts.app')

@section('title', $board->name ?? '위원회 토론장')

@section('content')
<div class="board-container">
    <div class="board-page-header">
        <div class="board-page-buttons">
            <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'community_committee_discussions') }}" class="btn btn-secondary btn-sm">
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

            @if ($post->is_notice)
                <div class="board-post-notice">
                    <span class="badge badge-warning">공지</span>
                </div>
            @endif

            @if ($post->category)
                <div class="board-post-category">
                    <span class="badge badge-info">{{ $post->category }}</span>
                </div>
            @endif

            <div class="board-post-content">
                {!! $post->content !!}
            </div>

            <div class="board-post-actions">
                <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'community_committee_discussions', $post->id]) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> 수정
                </a>
                <form action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'community_committee_discussions', $post->id]) }}" method="POST" class="d-inline js-delete-confirm-form">
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

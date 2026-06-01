@extends('backoffice.layouts.app')

@section('title', $board->name ?? '학술지 수정')

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_journals') }}" class="btn btn-secondary btn-sm">
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

            @php
                $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
                $linkUrl = $customFields['link_url'] ?? '';
            @endphp

            <form action="{{ route('backoffice.board-posts.update', [$board->slug ?? 'academic_journals', $post->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="content" value="{{ old('content', $post->content ?: '학술지 링크') }}">

                <div class="board-form-group">
                    <label for="title" class="board-form-label">제목 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="board-form-group">
                    <label for="custom_field_link_url" class="board-form-label">링크 URL <span class="required">*</span></label>
                    <input type="url" class="board-form-control" id="custom_field_link_url" name="custom_field_link_url" value="{{ old('custom_field_link_url', $linkUrl) }}" placeholder="https://example.com" required>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">공개여부 <span class="required">*</span></label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="visibility_public" name="is_active" value="1" @checked((string) old('is_active', (string) ($post->is_active ?? 1)) === '1') required>
                            <label for="visibility_public">공개</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="visibility_private" name="is_active" value="0" @checked((string) old('is_active', (string) ($post->is_active ?? 1)) === '0') required>
                            <label for="visibility_private">비공개</label>
                        </div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label for="author_name" class="board-form-label">작성자</label>
                    <input type="text" class="board-form-control" id="author_name" name="author_name" value="{{ old('author_name', auth()->user()->name ?? $post->author_name) }}">
                </div>

                <div class="board-form-group">
                    <label for="created_at" class="board-form-label">등록일시</label>
                    <input type="datetime-local" class="board-form-control" id="created_at" name="created_at" value="{{ old('created_at', ($post->created_at ? \Illuminate\Support\Carbon::parse($post->created_at)->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i'))) }}">
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_journals') }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

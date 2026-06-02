@extends('backoffice.layouts.app')

@section('title', $board->name ?? '회칙 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
@endsection

@section('content')
@php
    $returnUrl = request('return_url', route('backoffice.board-posts.index', $board->slug ?? 'society_rules'));
@endphp

<div id="alertModal" class="modal">
    <div class="modal-content">
        <div id="modalHeader" class="modal-header">
            <span id="modalTitle">알림</span>
            <span class="close-modal">&times;</span>
        </div>
        <div id="modalBody" class="modal-body">
            <p id="modalMessage"></p>
        </div>
    </div>
</div>

<div class="board-container">
    <div class="board-header">
        <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            @if (session('success'))
                <div class="alert alert-success board-hidden-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="board-alert board-alert-info">
                위원회별 내규와 프로토콜은 각 위원회 관리 페이지에서 확인해 주세요.
            </div>

            @if ($errors->any())
                <div class="board-alert board-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('backoffice.board-posts.update', [$board->slug ?? 'society_rules', $post->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_url" value="{{ $returnUrl }}">

                <div class="board-form-group">
                    <label for="title" class="board-form-label">타이틀 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="board-form-group">
                    <label for="content" class="board-form-label">회칙 내용 <span class="required">*</span></label>
                    <textarea class="board-form-control board-form-textarea" id="content" name="content" rows="18" data-backoffice-ckeditor data-source-editing="true" required>{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 회칙 저장
                    </button>
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

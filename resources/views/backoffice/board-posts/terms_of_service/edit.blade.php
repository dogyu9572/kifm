@extends('backoffice.layouts.app')

@section('title', $board->name ?? '이용약관')

@section('content')
@php
    $returnUrl = request('return_url', route('backoffice.board-posts.index', $board->slug ?? 'terms_of_service'));
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

            <form action="{{ route('backoffice.board-posts.update', [$board->slug ?? 'terms_of_service', $post->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                <input type="hidden" name="title" value="{{ old('title', $post->title ?: '이용약관') }}">

                <div class="board-form-group">
                    <label for="content" class="board-form-label">이용약관 <span class="required">*</span></label>
                    <textarea class="board-form-control board-form-textarea" id="content" name="content" rows="18" data-backoffice-ckeditor data-source-editing="true" required>{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
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

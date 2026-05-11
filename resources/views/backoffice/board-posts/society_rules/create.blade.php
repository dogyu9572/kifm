@extends('backoffice.layouts.app')

@section('title', ($board->name ?? '회칙 관리'))

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'society_rules') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
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

            <form action="{{ route('backoffice.board-posts.store', $board->slug ?? 'society_rules') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="board-form-group">
                    <label for="title" class="board-form-label">타이틀 <span class="required">*</span></label>
                    <input type="text" class="board-form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="content" class="board-form-label">회칙 내용 <span class="required">*</span></label>
                    <textarea class="board-form-control board-form-textarea" id="content" name="content" rows="18" data-backoffice-ckeditor data-source-editing="true" required>{{ old('content') }}</textarea>
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

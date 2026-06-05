@extends('backoffice.layouts.app')

@section('title', '세션 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> 행사로
                </a>
                <a href="{{ route('backoffice.academic-events.sessions.abstracts', [$event, $session]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> 초록 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                @if ($errors->any())
                    <div class="board-alert board-alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('backoffice.academic-events.sessions.update', [$event, $session]) }}" id="bo-academic-session-form">
                    @csrf
                    @method('PUT')
                    <div class="board-form-group">
                        <label class="board-form-label">세션명 <span class="required">*</span></label>
                        <input type="text" name="name" class="board-form-control" value="{{ old('name', $session->name) }}" required>
                    </div>
                    <div class="board-form-group">
                        <label class="board-form-label">분류</label>
                        <select name="category" class="board-form-control board-form-control--max-md">
                            <option value="">선택</option>
                            @foreach ($categoryLabels as $code => $label)
                                <option value="{{ $code }}" @selected(old('category', $session->category) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="board-form-group">
                        <label class="board-form-label">날짜 <span class="required">*</span></label>
                        <input type="date" name="session_date" class="board-form-control board-form-control--max-md" value="{{ old('session_date', $session->session_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="board-form-group">
                        <label class="board-form-label">시작 시간 <span class="required">*</span></label>
                        <input type="time" name="start_time" class="board-form-control board-form-control--max-sm" value="{{ old('start_time', substr((string) $session->start_time, 0, 5)) }}" required>
                    </div>
                    <div class="board-form-group">
                        <label class="board-form-label">종료 시간 <span class="required">*</span></label>
                        <input type="time" name="end_time" class="board-form-control board-form-control--max-sm" value="{{ old('end_time', substr((string) $session->end_time, 0, 5)) }}" required>
                    </div>
                    <div class="board-form-group">
                        <label class="board-form-label">좌장 / 연자</label>
                        <input type="text" name="chair_speakers" class="board-form-control" value="{{ old('chair_speakers', $session->chair_speakers) }}" placeholder="콤마(,)로 구분하여 여러 명을 입력">
                    </div>
                    <div class="board-form-group">
                        <label class="board-form-label">세션 설명</label>
                        <textarea name="description" id="session_description" class="board-form-control board-form-textarea" rows="8" data-backoffice-ckeditor="true">{{ old('description', $session->description) }}</textarea>
                    </div>
                    <input type="hidden" name="sort_order" value="{{ old('sort_order', $session->sort_order) }}">
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
    <script src="{{ asset('js/backoffice/academic-session-form.js') }}"></script>
@endsection

@extends('backoffice.layouts.app')

@section('title', '학술행사 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-trainings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/sorting.css') }}">
@endsection

@section('content')
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
            <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                @if (session('success'))
                    <div class="alert alert-success hidden-alert">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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

                <form id="bo-academic-event-form" method="POST" action="{{ route('backoffice.academic-events.update', $event) }}" enctype="multipart/form-data"
                    data-search-sponsors-url="{{ route('backoffice.academic-sponsor-masters.search') }}"
                    data-store-sponsor-master-url="{{ route('backoffice.academic-sponsor-masters.quick-store') }}"
                    data-search-abstracts-url="{{ route('backoffice.academic-events.search-abstracts') }}"
                    data-academic-event-id="{{ $event->getKey() }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="return_url" value="{{ request('return_url') }}">
                    @include('backoffice.academic-events._form', ['isEdit' => true])
                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>

                @foreach ($event->sessions ?? [] as $sessionDeleteForm)
                    <form id="bo-session-delete-form-{{ $sessionDeleteForm->id }}" method="POST" action="{{ route('backoffice.academic-events.sessions.destroy', [$event, $sessionDeleteForm]) }}" class="d-none js-academic-session-delete-form">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-backoffice-ckeditor-assets />
    <script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <script src="{{ asset('js/backoffice/board-image-file-preview.js') }}"></script>
    <script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
    <script src="{{ asset('js/backoffice/academic-events-form.js') }}"></script>
@endsection

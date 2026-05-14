@extends('backoffice.layouts.app')

@section('title', '위원회 등록')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-trainings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
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

                <form method="POST" action="{{ route('backoffice.community-committees.store') }}" enctype="multipart/form-data" id="bo-community-committee-form">
                    @csrf
                    @include('backoffice.community-committees._form')
                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-backoffice-ckeditor-assets />
    <script src="{{ asset('js/backoffice/board-image-file-preview.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-post-form.js') }}"></script>
    <script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
    <script src="{{ asset('js/backoffice/community-committees-form.js') }}"></script>
@endsection


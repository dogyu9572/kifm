@extends('backoffice.layouts.app')

@section('title', '주소록 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger board-hidden-alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="board-container">
        <div class="board-header">
            <a href="{{ route('backoffice.address-books.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>
        <div class="board-card">
            <div class="board-card-body">
                <form action="{{ route('backoffice.address-books.update', $addressBook) }}" method="POST" id="address-book-form">
                    @csrf
                    @method('PUT')
                    @include('backoffice.address_books._form')
                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">저장</button>
                        <a href="{{ route('backoffice.address-books.index') }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/address-books.js') }}"></script>
@endsection

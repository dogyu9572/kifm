@extends('backoffice.layouts.app')

@section('title', '임원 등록')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ route('backoffice.society-executives.index') }}" class="btn btn-secondary btn-sm">
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

                <form action="{{ route('backoffice.society-executives.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('backoffice.society_executives._form')

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ route('backoffice.society-executives.index') }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/society-executives.js') }}"></script>
@endsection

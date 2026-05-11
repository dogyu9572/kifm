@extends('backoffice.layouts.app')

@section('title', '인정의 등록')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
@endsection

@section('content')
@if ($errors->any())
    <div class="board-alert board-alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="board-container">
    <div class="board-header">
        <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>
    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route('backoffice.certified-members.store') }}" method="POST" class="bo-member-form">
                @csrf
                @include('backoffice.certified_members._form')
                <div class="board-form-actions board-form-actions--member-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                    <a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
@endsection


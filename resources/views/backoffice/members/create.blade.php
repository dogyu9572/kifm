@extends('backoffice.layouts.app')

@section('title', '회원 등록')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="{{ asset('js/backoffice/members.js') }}"></script>
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
        <a href="{{ route('backoffice.members.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route('backoffice.members.store') }}" method="POST" id="memberForm" class="bo-member-form">
                @csrf
                @include('backoffice.members._form')
            </form>

            @include('backoffice.members._history_placeholders')

            <div class="board-form-actions board-form-actions--member-footer">
                <button type="submit" class="btn btn-primary" form="memberForm">
                    <i class="fas fa-save"></i> 저장
                </button>
                <a href="{{ route('backoffice.members.index') }}" class="btn btn-secondary">취소</a>
            </div>
        </div>
    </div>
</div>

@include('member.pop_search_school')
@endsection

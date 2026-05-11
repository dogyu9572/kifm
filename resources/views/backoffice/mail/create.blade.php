@extends('backoffice.layouts.app')

@section('title', '메일 발송 등록')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
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
            <a href="{{ route('backoffice.mails.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>
        <div class="board-card">
            <div class="board-card-body">
                <form action="{{ route('backoffice.mails.store') }}" method="POST" id="mail-form">
                    @csrf
                    @include('backoffice.mail._form', ['mail' => null])
                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-outline-secondary" name="submit_action" value="draft">임시 저장</button>
                        <button type="submit" class="btn btn-primary" name="submit_action" value="send">발송(예약)하기</button>
                        <a href="{{ route('backoffice.mails.index') }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/mail.js') }}"></script>
@endsection

@extends('backoffice.layouts.app')

@section('title', '연간 일정 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ route('backoffice.annual_schedule.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                @if ($errors->any())
                    <div class="alert alert-danger board-hidden-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('backoffice.annual_schedule.update', $annualSchedule) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('backoffice.annual_schedule._form')

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ route('backoffice.annual_schedule.index') }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/annual-schedules.js') }}"></script>
@endsection

@extends('backoffice.layouts.app')

@section('title', '학술행사 참가 및 결제 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-trainings.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
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

                <form action="{{ route('backoffice.academic-event-registrations.update', $registration) }}" method="POST" id="bo-academic-event-registration-form"
                    data-search-members-url="{{ route('backoffice.academic-event-registrations.search-members') }}"
                    data-search-plans-url="{{ route('backoffice.academic-event-registrations.search-payment-plans') }}">
                    @csrf
                    @method('PUT')
                    @include('backoffice.academic-event-registrations._form')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
    <script src="{{ asset('js/backoffice/academic-event-registrations-form.js') }}"></script>
@endsection

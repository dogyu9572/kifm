@extends('backoffice.layouts.app')

@section('title', '연수교육 참가 및 결제 수정')

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

                @if (session('success'))
                    <div class="board-alert board-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('backoffice.edu-training-payments.update', $payment) }}" method="POST" id="edu-training-payment-form"
                    data-search-members-url="{{ route('backoffice.edu-training-payments.search-members') }}"
                    data-search-plans-url="{{ route('backoffice.edu-training-payments.search-payment-plans') }}"
                    data-confirm-deposit-url="{{ route('backoffice.edu-training-payments.confirm-deposit', $payment) }}">
                    @csrf
                    @method('PUT')
                    @include('backoffice.edu-training-payments._form')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
    <script src="{{ asset('js/backoffice/edu-training-payments-form.js') }}"></script>
@endsection


@extends('backoffice.layouts.app')

@section('title', '결제 항목 등록')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/payment-plans.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                @if ($errors->any())
                    <div class="alert alert-danger board-hidden-alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('backoffice.payment-plans.store') }}" method="POST">
                    @csrf
                    @include('backoffice.payment_plans._fields', [
                        'plan' => null,
                        'selectedGrades' => $selectedGrades,
                        'selectedTypes' => $selectedTypes,
                        'categoryLabels' => $categoryLabels,
                        'gradeLabels' => $gradeLabels,
                        'memberTypeLabels' => $memberTypeLabels,
                    ])

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/payment-plans-form.js') }}"></script>
@endsection

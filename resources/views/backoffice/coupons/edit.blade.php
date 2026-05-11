@extends('backoffice.layouts.app')

@section('title', '쿠폰 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/payment-plans.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/coupons.css') }}">
@endsection

@section('content')
    <div class="board-container" id="bo-coupon-form-root" data-generate-code-url="{{ route('backoffice.coupons.generate-code') }}">
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

                <form action="{{ route('backoffice.coupons.update', $coupon) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('backoffice.coupons._fields', [
                        'coupon' => $coupon,
                        'selectedCategories' => $selectedCategories,
                        'categoryLabels' => $categoryLabels,
                    ])

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                        <button type="button" class="btn btn-danger" id="bo-coupon-delete-btn">
                            <i class="fas fa-trash"></i> 삭제
                        </button>
                    </div>
                </form>

                <form id="bo-coupon-delete-form" action="{{ route('backoffice.coupons.destroy', $coupon) }}" method="POST" class="bo-hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/coupons-form.js') }}"></script>
@endsection

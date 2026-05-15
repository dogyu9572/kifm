@extends('backoffice.layouts.app')

@section('title', '주치의 등록')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-trainings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
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
                    <div class="board-alert board-alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="bo-local-doctor-form" action="{{ route('backoffice.local-doctors.store') }}" method="POST" enctype="multipart/form-data">
                    <textarea id="bo-local-doctor-sigungu-json" class="d-none" rows="1" cols="1" readonly tabindex="-1" autocomplete="off" aria-hidden="true">{{ json_encode($sigunguBySido, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}</textarea>
                    @csrf
                    @include('backoffice.local-doctors._form', [
                        'localDoctor' => null,
                        'categories' => $categories,
                        'categoryManageUrl' => $categoryManageUrl ?? null,
                        'functionalTests' => $functionalTests,
                        'treatmentAreas' => $treatmentAreas,
                        'statusLabels' => $statusLabels,
                        'sigunguBySido' => $sigunguBySido,
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
    <script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <x-backoffice-ckeditor-assets />
    <script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-image-file-preview.js') }}"></script>
    <script src="{{ asset('js/backoffice/local-doctors-form.js') }}"></script>
@endsection

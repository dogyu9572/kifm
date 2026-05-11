@extends('backoffice.layouts.app')

@section('title', '연수교육 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-trainings.css') }}">
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

                <form id="bo-edu-training-form" action="{{ route('backoffice.edu-trainings.update', $eduTraining) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('backoffice.edu-trainings._fields', [
                        'eduTraining' => $eduTraining,
                        'seasonLabels' => $seasonLabels,
                        'methodLabels' => $methodLabels,
                        'statusLabels' => $statusLabels,
                        'yearOptions' => $yearOptions,
                        'gradeLabels' => $gradeLabels,
                        'attachments' => $attachments,
                    ])

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>

                <form id="bo-edu-training-delete-form" action="{{ route('backoffice.edu-trainings.destroy', $eduTraining) }}" method="POST" class="bo-hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-backoffice-ckeditor-assets />
    <script src="{{ asset('js/backoffice/edu-trainings-form.js') }}"></script>
@endsection

@extends('backoffice.layouts.app')

@section('title', '강좌 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
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

                <form id="bo-edu-course-form" action="{{ route('backoffice.edu-courses.update', $course) }}" method="POST" enctype="multipart/form-data"
                    data-search-members-url="{{ route('backoffice.edu-courses.search-members') }}">
                    @csrf
                    @method('PUT')
                    @include('backoffice.edu-courses._fields', [
                        'course' => $course,
                        'gradeLabels' => $gradeLabels,
                        'courseTypeLabels' => $courseTypeLabels,
                        'yearOptions' => $yearOptions,
                        'linkedTrainings' => $linkedTrainings,
                        'linkedEvents' => $linkedEvents,
                    ])
                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>

                <form id="bo-edu-course-delete-form" action="{{ route('backoffice.edu-courses.destroy', $course) }}" method="POST" class="bo-hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-backoffice-ckeditor-assets />
    <script src="{{ asset('js/backoffice/member-selector-modal.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-image-file-preview.js') }}"></script>
    <script src="{{ asset('js/backoffice/edu-courses-form.js') }}"></script>
@endsection


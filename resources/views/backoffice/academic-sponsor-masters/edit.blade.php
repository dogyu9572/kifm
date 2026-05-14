@extends('backoffice.layouts.app')

@section('title', '스폰서 수정')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    <div class="board-container">
        <div class="board-header">
            <a href="{{ route('backoffice.academic-sponsor-masters.index') }}" class="btn btn-secondary btn-sm">
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

                <h3 class="bo-section-title">스폰서 정보 입력</h3>

                <form action="{{ route('backoffice.academic-sponsor-masters.update', $master) }}" method="POST" enctype="multipart/form-data" id="bo-sponsor-master-form">
                    @csrf
                    @method('PUT')
                    @include('backoffice.academic-sponsor-masters._form', ['master' => $master, 'requireLogo' => false])

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ route('backoffice.academic-sponsor-masters.index') }}" class="btn btn-secondary">취소</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-sponsor-masters-form.js') }}"></script>
@endsection

@extends('backoffice.layouts.app')

@section('title', '연수교육 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container" id="bo-edu-trainings-index" data-bulk-destroy-url="{{ route('backoffice.edu-trainings.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.edu-trainings.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 연수 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.edu-trainings.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="filter_year">연도</label>
                                <select name="year" id="filter_year" class="filter-select">
                                    <option value="">전체 연도</option>
                                    @foreach ($yearOptions ?? [] as $yearOption)
                                        <option value="{{ $yearOption }}" @selected((string) request('year') === (string) $yearOption)>{{ $yearOption }}년</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_round_type">연수 차수</label>
                                <select name="round_type" id="filter_round_type" class="filter-select">
                                    <option value="">전체 차수</option>
                                    @foreach ($roundTypeLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('round_type') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_training_method">연수방식</label>
                                <select name="training_method" id="filter_training_method" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($methodLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('training_method') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_status">상태</label>
                                <select name="status" id="filter_status" class="filter-select">
                                    <option value="">전체 상태</option>
                                    @foreach ($statusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('status') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="filter_keyword">검색어</label>
                                <input type="text" name="keyword" id="filter_keyword" class="filter-input"
                                    value="{{ request('keyword') }}" placeholder="연수명을 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.edu-trainings.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $trainings->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.edu-trainings.index') }}" class="per-page-form" id="edu-training-per-page-form">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            <input type="hidden" name="round_type" value="{{ request('round_type') }}">
                            <input type="hidden" name="training_method" value="{{ request('training_method') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-edu-training-per-page">
                                <option value="10" @selected($perPage === 10)>10개</option>
                                <option value="20" @selected($perPage === 20)>20개</option>
                                <option value="50" @selected($perPage === 50)>50개</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w8">연도</th>
                                <th class="w8">시즌</th>
                                <th>연수명</th>
                                <th class="w10">연수 차수</th>
                                <th class="w12">연수방식</th>
                                <th class="w8">상태</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trainings as $training)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $training->id }}" class="form-check-input bo-edu-training-checkbox bo-row-checkbox">
                                    </td>
                                    <td>{{ $training->year }}</td>
                                    <td>{{ $seasonLabels[$training->season] ?? $training->season }}</td>
                                    <td>{{ $training->title }}</td>
                                    <td>
                                        @if ($training->rounds->isNotEmpty())
                                            {{ $training->rounds->pluck('round_label')->implode(', ') }}
                                        @else
                                            {{ $training->round_type ?: '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $methodLabels[$training->training_method] ?? $training->training_method }}</td>
                                    <td>{{ $statusLabels[$training->status] ?? $training->status }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.edu-trainings.edit', ['edu_training' => $training, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.edu-trainings.destroy', $training) }}" method="POST" class="d-inline js-delete-confirm-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> 삭제
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">등록된 연수교육이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$trainings" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/edu-trainings-index.js') }}"></script>
@endsection

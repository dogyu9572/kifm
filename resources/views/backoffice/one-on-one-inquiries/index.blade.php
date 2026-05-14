@extends('backoffice.layouts.app')

@section('title', '1:1 문의 관리')

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

    <div class="board-container" id="bo-one-on-one-inquiries-index" data-bulk-destroy-url="{{ route('backoffice.one-on-one-inquiries.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.one-on-one-inquiries.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="filter_date_from">문의일</label>
                                <input type="date" name="date_from" id="filter_date_from" class="filter-input" value="{{ request('date_from') }}">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_date_to">~</label>
                                <input type="date" name="date_to" id="filter_date_to" class="filter-input" value="{{ request('date_to') }}">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_answer_status">답변 상태</label>
                                <select name="answer_status" id="filter_answer_status" class="filter-select">
                                    <option value="all">전체</option>
                                    @foreach ($statusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('answer_status') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_search_field">검색어</label>
                                <select name="search_field" id="filter_search_field" class="filter-select">
                                    @foreach ($searchFieldLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('search_field', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label sr-only" for="filter_keyword">검색어 입력</label>
                                <input type="text" name="keyword" id="filter_keyword" class="filter-input"
                                    value="{{ request('keyword') }}" placeholder="검색어를 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.one-on-one-inquiries.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $inquiries->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.one-on-one-inquiries.index') }}" class="per-page-form" id="one-on-one-inquiry-per-page-form">
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                            <input type="hidden" name="answer_status" value="{{ request('answer_status') }}">
                            <input type="hidden" name="search_field" value="{{ request('search_field') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-one-on-one-inquiry-per-page">
                                <option value="10" @selected($perPage === 10)>10개</option>
                                <option value="20" @selected($perPage === 20)>20개</option>
                                <option value="50" @selected($perPage === 50)>50개</option>
                                <option value="100" @selected($perPage === 100)>100개</option>
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
                                <th class="w5">번호</th>
                                <th class="w10">회원명</th>
                                <th>제목</th>
                                <th class="w15">문의일시</th>
                                <th class="w15">답변일시</th>
                                <th class="w10">답변 상태</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inquiries as $inquiry)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $inquiry->id }}" class="form-check-input bo-one-on-one-inquiry-checkbox bo-row-checkbox">
                                    </td>
                                    @php
                                        $rowNumber = $inquiries->total() - ($inquiries->currentPage() - 1) * $inquiries->perPage() - $loop->index;
                                    @endphp
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ $inquiry->displayMemberName() ?: '-' }}</td>
                                    <td>
                                        {{ $inquiry->title }}
                                    </td>
                                    <td>{{ optional($inquiry->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($inquiry->answered_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                    <td>
                                        @if ($inquiry->answer_status === 'DONE')
                                            <span class="badge badge-success">{{ $statusLabels['DONE'] }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $statusLabels['PENDING'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.one-on-one-inquiries.edit', ['one_on_one_inquiry' => $inquiry, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.one-on-one-inquiries.destroy', $inquiry) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="8" class="text-center">등록된 문의가 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$inquiries" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/one-on-one-inquiries-index.js') }}"></script>
@endsection

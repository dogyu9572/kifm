@extends('backoffice.layouts.app')

@section('title', '임원진 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/sorting.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.society-executives.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 임원 추가
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.society-executives.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="group_no" class="filter-label">그룹</label>
                                <select name="group_no" id="group_no" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="1" @selected(request('group_no') === '1')>1그룹</option>
                                    <option value="2" @selected(request('group_no') === '2')>2그룹</option>
                                    <option value="3" @selected(request('group_no') === '3')>3그룹</option>
                                </select>
                            </div>
                            <div class="filter-group filter-group-large">
                                <label for="keyword" class="filter-label">검색어</label>
                                <div class="filter-inline">
                                    <select name="search_type" class="filter-select">
                                        <option value="name" @selected(request('search_type', 'name') === 'name')>이름</option>
                                        <option value="position" @selected(request('search_type') === 'position')>직책</option>
                                        <option value="organization" @selected(request('search_type') === 'organization')>소속</option>
                                    </select>
                                    <input type="text" id="keyword" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="검색어를 입력하세요.">
                                </div>
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.society-executives.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $executives->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.society-executives.index') }}" class="per-page-form">
                            <input type="hidden" name="group_no" value="{{ request('group_no') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type', 'name') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                                <option value="20" @selected($perPage === 20)>20개</option>
                                <option value="50" @selected($perPage === 50)>50개</option>
                                <option value="100" @selected($perPage === 100)>100개</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table sortable-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w5">순서</th>
                                <th class="w8">번호</th>
                                <th class="w8">그룹</th>
                                <th class="w10">직책</th>
                                <th class="w10">이름</th>
                                <th>소속</th>
                                <th class="w15">이메일</th>
                                <th class="w10">정렬순서</th>
                                <th class="w10">사용여부</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-tbody">
                            @forelse ($executives as $executive)
                                <tr data-post-id="{{ $executive->id }}">
                                    <td>
                                        <input type="checkbox" value="{{ $executive->id }}" class="form-check-input executive-checkbox">
                                    </td>
                                    <td class="sort-handle-cell">
                                        <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                                    </td>
                                    <td>{{ $executives->total() - (($executives->currentPage() - 1) * $executives->perPage()) - $loop->index }}</td>
                                    <td>{{ $executive->group_no }}그룹</td>
                                    <td>{{ $executive->position }}</td>
                                    <td>{{ $executive->name }}</td>
                                    <td>{{ $executive->organization }}</td>
                                    <td>{{ $executive->email ?: '-' }}</td>
                                    <td>{{ $executive->sort_order }}</td>
                                    <td>{{ $executive->is_active ? '사용' : '미사용' }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.society-executives.edit', $executive) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.society-executives.destroy', $executive) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="11" class="text-center">등록된 임원 정보가 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$executives" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/sorting.js') }}"></script>
    <script src="{{ asset('js/backoffice/society-executives.js') }}"></script>
@endsection

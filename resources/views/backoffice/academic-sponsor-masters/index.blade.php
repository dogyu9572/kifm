@extends('backoffice.layouts.app')

@section('title', '스폰서 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/sorting.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger board-hidden-alert">{{ session('error') }}</div>
    @endif

    <div
        class="board-container"
        id="bo-academic-sponsor-masters-index"
        data-bulk-destroy-url="{{ route('backoffice.academic-sponsor-masters.bulk-destroy') }}"
        data-sort-order-url="{{ route('backoffice.academic-sponsor-masters.update-sort-order') }}"
    >
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.academic-sponsor-masters.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 스폰서 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.academic-sponsor-masters.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="status">상태</label>
                                <select id="status" name="status" class="filter-select">
                                    <option value="all" @selected(request('status', 'all') === 'all')>전체</option>
                                    <option value="active" @selected(request('status') === 'active')>활성화</option>
                                    <option value="inactive" @selected(request('status') === 'inactive')>비활성화</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="registered_date">등록일</label>
                                <input id="registered_date" name="registered_date" type="date" class="filter-input" value="{{ request('registered_date', '') }}">
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="name">스폰서명</label>
                                <input id="name" name="name" type="text" class="filter-input" value="{{ request('name', '') }}" placeholder="스폰서명">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">&nbsp;</label>
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.academic-sponsor-masters.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $sponsors->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.academic-sponsor-masters.index') }}" id="bo-sponsor-master-per-page-form" class="per-page-form">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <input type="hidden" name="registered_date" value="{{ request('registered_date', '') }}">
                            <input type="hidden" name="name" value="{{ request('name', '') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select id="per_page" name="per_page" class="per-page-select bo-sponsor-master-per-page">
                                @foreach (\App\Services\Backoffice\AcademicSponsorMasterService::PER_PAGE_OPTIONS as $n)
                                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}건</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table sortable-table">
                        <thead>
                            <tr>
                                <th class="board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w5">순서</th>
                                <th class="w5">번호</th>
                                <th class="w18">스폰서명</th>
                                <th class="w15">로고</th>
                                <th class="w10">상태</th>
                                <th class="w12">등록일</th>
                                <th class="w14">관리</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-tbody">
                            @forelse ($sponsors as $row)
                                <tr data-post-id="{{ $row->id }}">
                                    <td>
                                        <input type="checkbox" value="{{ $row->id }}" class="form-check-input bo-sponsor-master-checkbox bo-row-checkbox">
                                    </td>
                                    <td class="sort-handle-cell">
                                        <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                                    </td>
                                    <td>{{ $sponsors->total() - (($sponsors->currentPage() - 1) * $sponsors->perPage()) - $loop->index }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>
                                        @if ($row->logo_path)
                                            <img src="{{ asset('storage/' . $row->logo_path) }}" alt="" width="100" height="40">
                                        @else
                                            <span class="board-form-text">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->status === 'active')
                                            <span class="badge badge-success">활성화</span>
                                        @else
                                            <span class="badge badge-secondary">비활성화</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($row->created_at)->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.academic-sponsor-masters.edit', $row) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.academic-sponsor-masters.destroy', $row) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="8" class="text-center">등록된 스폰서가 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$sponsors" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-sponsor-masters-index.js') }}"></script>
@endsection

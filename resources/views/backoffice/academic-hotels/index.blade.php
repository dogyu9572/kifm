@extends('backoffice.layouts.app')

@section('title', '숙박 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
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
        id="bo-academic-hotels-index"
        data-bulk-destroy-url="{{ route('backoffice.academic-hotels.bulk-destroy') }}"
    >
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.academic-hotels.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 숙박 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.academic-hotels.index') }}" class="filter-form">
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
                                <label class="filter-label" for="name">숙소명</label>
                                <input id="name" name="name" type="text" class="filter-input" value="{{ request('name', '') }}" placeholder="숙소명">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">&nbsp;</label>
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.academic-hotels.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $hotels->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.academic-hotels.index') }}" id="bo-hotel-per-page-form" class="per-page-form">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <input type="hidden" name="registered_date" value="{{ request('registered_date', '') }}">
                            <input type="hidden" name="name" value="{{ request('name', '') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select id="per_page" name="per_page" class="per-page-select bo-hotel-per-page">
                                @foreach (\App\Services\Backoffice\AcademicHotelService::PER_PAGE_OPTIONS as $n)
                                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}건</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w5">번호</th>
                                <th class="w14">숙소명</th>
                                <th class="w22">주소</th>
                                <th class="w10">연락처</th>
                                <th class="w8">상태</th>
                                <th class="w10">등록일</th>
                                <th class="w12">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hotels as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $row->id }}" class="form-check-input bo-hotel-checkbox bo-row-checkbox">
                                    </td>
                                    <td>{{ $hotels->total() - (($hotels->currentPage() - 1) * $hotels->perPage()) - $loop->index }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(trim($row->address . ' ' . (string) ($row->address_detail ?? '')), 60) }}</td>
                                    <td>{{ $row->phone }}</td>
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
                                            <a href="{{ route('backoffice.academic-hotels.edit', $row) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.academic-hotels.destroy', $row) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="8" class="text-center">등록된 숙박이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$hotels" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-hotels-index.js') }}"></script>
@endsection

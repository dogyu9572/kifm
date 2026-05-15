@extends('backoffice.layouts.app')

@section('title', $board->name ?? '게시판')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/sorting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    @php
        $customFieldsConfig = collect($board->custom_fields_config ?? []);
        $historyYearOptionsRaw = (string) ($customFieldsConfig->firstWhere('name', 'history_year')['options'] ?? '');
        $historyMonthOptionsRaw = (string) ($customFieldsConfig->firstWhere('name', 'history_month')['options'] ?? '1,2,3,4,5,6,7,8,9,10,11,12');
        $historyYearOptions = array_values(array_filter(array_map('trim', explode(',', $historyYearOptionsRaw))));
        $historyMonthOptions = array_values(array_filter(array_map('trim', explode(',', $historyMonthOptionsRaw))));
    @endphp

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.board-posts.create', $board->slug ?? 'notice') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 연혁 추가
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'notice') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="year" class="filter-label">연도</label>
                                <select id="year" name="year" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($historyYearOptions as $year)
                                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}년</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="month" class="filter-label">월</label>
                                <select id="month" name="month" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($historyMonthOptions as $month)
                                        <option value="{{ $month }}" @selected((string) request('month') === (string) $month)>{{ $month }}월</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="visibility" class="filter-label">사용여부</label>
                                <select id="visibility" name="visibility" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="public" @selected(request('visibility') === 'public')>사용</option>
                                    <option value="private" @selected(request('visibility') === 'private')>미사용</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="keyword" class="filter-label">내용</label>
                                <input type="text" id="keyword" name="keyword" class="filter-input"
                                    placeholder="검색어를 입력하세요." value="{{ request('keyword') }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'notice') }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 목록 개수 선택 -->
                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $posts->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'notice') }}" class="per-page-form">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            <input type="hidden" name="month" value="{{ request('month') }}">
                            <input type="hidden" name="visibility" value="{{ request('visibility') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                                <option value="10" @selected(request('per_page', 15) == 10)>10개</option>
                                <option value="20" @selected(request('per_page', 15) == 20)>20개</option>
                                <option value="50" @selected(request('per_page', 15) == 50)>50개</option>
                                <option value="100" @selected(request('per_page', 15) == 100)>100개</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table {{ $board->enable_sorting ? 'sortable-table' : '' }}">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                @if($board->enable_sorting)
                                    <th class="w5">순서</th>
                                @endif
                                <th class="w10">연도</th>
                                <th class="w10">월</th>
                                <th>내용</th>
                                <th class="w10">사용여부</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody @if($board->enable_sorting) id="sortable-tbody" @endif>
                            @forelse($posts as $post)
                                @php
                                    $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
                                    if (! is_array($customFields)) {
                                        $customFields = [];
                                    }
                                    $historyYear = $customFields['history_year'] ?? '-';
                                    $historyMonth = $customFields['history_month'] ?? '-';
                                @endphp
                                <tr @if($board->enable_sorting) data-post-id="{{ $post->id }}" @endif>
                                    <td>
                                        <input type="checkbox" name="selected_posts[]" value="{{ $post->id }}" class="form-check-input post-checkbox">
                                    </td>
                                    @if($board->enable_sorting)
                                        <td class="sort-handle-cell">
                                            <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                                        </td>
                                    @endif
                                    <td>{{ $historyYear }}</td>
                                    <td>{{ $historyMonth }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>
                                        @if ($post->is_active)
                                            <span class="status-badge status-active">사용</span>
                                        @else
                                            <span class="status-badge status-inactive">미사용</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'notice', $post->id]) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form
                                                action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'notice', $post->id]) }}"
                                                method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="{{ $board->enable_sorting ? '7' : '6' }}" class="text-center">등록된 게시글이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$posts" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/board-posts.js') }}"></script>
    @if($board->enable_sorting)
        <script src="{{ asset('js/backoffice/sorting.js') }}"></script>
    @endif
@endsection

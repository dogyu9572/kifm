@extends('backoffice.layouts.app')

@section('title', $board->name ?? '학술대회 연혁')

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

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.board-posts.create', $board->slug ?? 'academic_conference_history') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 연혁 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_conference_history') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="visibility" class="filter-label">공개여부</label>
                                <select id="visibility" name="visibility" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="public" @selected(request('visibility') === 'public')>공개</option>
                                    <option value="private" @selected(request('visibility') === 'private')>비공개</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="keyword" class="filter-label">검색어</label>
                                <input type="text" id="keyword" name="keyword" class="filter-input" placeholder="행사명을 입력하세요." value="{{ request('keyword') }}">
                                <input type="hidden" name="search_type" value="keyword">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_conference_history') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $posts->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_conference_history') }}" class="per-page-form">
                            <input type="hidden" name="visibility" value="{{ request('visibility') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type', 'keyword') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select" data-js-submit-form="1">
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
                                <th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                @if($board->enable_sorting)
                                    <th class="w5">순서</th>
                                @endif
                                <th class="w20">행사 기간</th>
                                <th>행사명</th>
                                <th class="w12">행사자료</th>
                                <th class="w10">공개여부</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody @if($board->enable_sorting) id="sortable-tbody" @endif>
                            @forelse($posts as $post)
                                @php
                                    $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
                                    $attachments = $post->attachments ? json_decode($post->attachments, true) : [];
                                    $startDate = $customFields['event_start_date'] ?? '';
                                    $endDate = $customFields['event_end_date'] ?? '';
                                    $periodText = trim($startDate . (($startDate !== '' && $endDate !== '') ? ' ~ ' : '') . $endDate);
                                @endphp
                                <tr @if($board->enable_sorting) data-post-id="{{ $post->id }}" @endif>
                                    <td><input type="checkbox" name="selected_posts[]" value="{{ $post->id }}" class="form-check-input post-checkbox"></td>
                                    @if($board->enable_sorting)
                                        <td class="sort-handle-cell">
                                            <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                                        </td>
                                    @endif
                                    <td>{{ $periodText !== '' ? $periodText : '-' }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ is_array($attachments) && count($attachments) > 0 ? '등록' : '-' }}</td>
                                    <td>
                                        @if ((bool) ($post->is_active ?? true))
                                            <span class="status-badge status-active">공개</span>
                                        @else
                                            <span class="status-badge status-inactive">비공개</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'academic_conference_history', $post->id, 'return_url' => request()->fullUrl()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'academic_conference_history', $post->id]) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="{{ $board->enable_sorting ? '7' : '6' }}" class="text-center">등록된 연혁이 없습니다.</td>
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

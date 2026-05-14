@extends('backoffice.layouts.app')

@section('title', $board->name ?? '학술대회 공지사항')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
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
                <a href="{{ route('backoffice.board-posts.create', $board->slug ?? 'academic_notices') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 공지사항 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_notices') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="event_id" class="filter-label">학술대회</label>
                                <select id="event_id" name="event_id" class="filter-select">
                                    <option value="">전체 학술대회</option>
                                    @foreach ($events ?? [] as $ev)
                                        <option value="{{ $ev->id }}" @selected((string) request('event_id') === (string) $ev->id)>
                                            {{ $ev->year }} {{ $ev->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="visibility" class="filter-label">상태</label>
                                <select id="visibility" name="visibility" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="public" @selected(request('visibility') === 'public')>활성화</option>
                                    <option value="private" @selected(request('visibility') === 'private')>비활성화</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="start_date" class="filter-label">등록일 시작</label>
                                <input type="date" id="start_date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="end_date" class="filter-label">등록일 끝</label>
                                <input type="date" id="end_date" name="end_date" class="filter-input" value="{{ request('end_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="search_type" class="filter-label">검색 구분</label>
                                <select id="search_type" name="search_type" class="filter-select">
                                    <option value="title" @selected(request('search_type', 'title') === 'title')>제목</option>
                                    <option value="author" @selected(request('search_type') === 'author')>작성자</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="keyword" class="filter-label">검색어</label>
                                <input type="text" id="keyword" name="keyword" class="filter-input" placeholder="검색어를 입력하세요." value="{{ request('keyword') }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_notices') }}" class="btn btn-secondary">
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
                        <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_notices') }}" class="per-page-form">
                            <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                            <input type="hidden" name="visibility" value="{{ request('visibility') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type') }}">
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
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                <th class="w6">번호</th>
                                <th class="w18">학술대회</th>
                                <th>제목</th>
                                <th class="w10">작성자</th>
                                <th class="w8">상태</th>
                                <th class="w12">등록일</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td><input type="checkbox" name="selected_posts[]" value="{{ $post->id }}" class="form-check-input post-checkbox"></td>
                                    <td>
                                        @if ($post->is_notice)
                                            <span class="board-notice-badge">공지</span>
                                        @else
                                            @php
                                                $postNumber = $posts->total() - ($posts->currentPage() - 1) * $posts->perPage() - $loop->index;
                                            @endphp
                                            {{ $postNumber }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (! empty($post->event_title))
                                            {{ $post->event_year }} {{ $post->event_title }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $post->title }}
                                    </td>
                                    <td>{{ $post->author_name ?? '-' }}</td>
                                    <td>
                                        @if ((bool) ($post->is_active ?? true))
                                            <span class="status-badge status-notice">활성화</span>
                                        @else
                                            <span class="status-badge status-general">비활성화</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'academic_notices', $post->id, 'return_url' => request()->fullUrl()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'academic_notices', $post->id]) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="8" class="text-center">등록된 공지사항이 없습니다.</td>
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
@endsection

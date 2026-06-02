@extends('backoffice.layouts.app')

@section('title', $board->name ?? '회원 자료실')

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
                <a href="{{ route('backoffice.board-posts.create', $board->slug ?? 'member_archive') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 신규 자료 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'member_archive') }}" class="filter-form">
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
                                <label for="member_grade" class="filter-label">회원 설정</label>
                                <select id="member_grade" name="member_grade" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="associate" @selected(request('member_grade') === 'associate')>준회원 이상</option>
                                    <option value="regular" @selected(request('member_grade') === 'regular')>정회원 이상</option>
                                    <option value="lifetime" @selected(request('member_grade') === 'lifetime')>평생회원</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="archive_type" class="filter-label">자료 유형</label>
                                <select id="archive_type" name="archive_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="ebook" @selected(request('archive_type') === 'ebook')>E-book (초록집)</option>
                                    <option value="video" @selected(request('archive_type') === 'video')>강의 영상</option>
                                    <option value="paper" @selected(request('archive_type') === 'paper')>논문/학술지</option>
                                    <option value="guide" @selected(request('archive_type') === 'guide')>가이드라인</option>
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
                        </div>
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search_type" class="filter-label">검색어</label>
                                <select id="search_type" name="search_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="title" @selected(request('search_type') === 'title')>제목</option>
                                    <option value="keyword" @selected(request('search_type') === 'keyword')>키워드</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="keyword" class="filter-label">검색어 입력</label>
                                <input type="text" id="keyword" name="keyword" class="filter-input" placeholder="검색어를 입력하세요." value="{{ request('keyword') }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'member_archive') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">총 {{ $posts->total() }}건</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'member_archive') }}" class="per-page-form">
                            <input type="hidden" name="visibility" value="{{ request('visibility') }}">
                            <input type="hidden" name="member_grade" value="{{ request('member_grade') }}">
                            <input type="hidden" name="archive_type" value="{{ request('archive_type') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select" data-js-submit-form="1">
                                <option value="10" @selected(request('per_page', 15) == 10)>10개</option>
                                <option value="20" @selected(request('per_page', 15) == 20)>20개</option>
                                <option value="50" @selected(request('per_page', 15) == 50)>50개</option>
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
                                <th class="w10">썸네일</th>
                                <th class="w15">자료 유형</th>
                                <th class="w15">회원 설정</th>
                                <th>제목</th>
                                <th class="w10">공개여부</th>
                                <th class="w10">등록일</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                @php
                                    $customFields = is_string($post->custom_fields ?? null)
                                        ? (json_decode($post->custom_fields, true) ?: [])
                                        : ($post->custom_fields ?? []);
                                    $archiveType = match($customFields['archive_type'] ?? null) {
                                        'ebook' => 'E-book (초록집)',
                                        'video' => '강의 영상',
                                        'paper' => '논문/학술지',
                                        'guide' => '가이드라인',
                                        default => '-',
                                    };
                                    $memberGrade = match($customFields['member_grade'] ?? null) {
                                        'all' => '전체 회원',
                                        'associate' => '준회원 이상',
                                        'regular' => '정회원 이상',
                                        'lifetime' => '평생회원',
                                        default => '-',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_posts[]" value="{{ $post->id }}" class="form-check-input post-checkbox">
                                    </td>
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
                                        @if(!empty($post->thumbnail))
                                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="썸네일" class="gallery-thumbnail-small">
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td>{{ $archiveType }}</td>
                                    <td>{{ $memberGrade }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ (bool) ($post->is_active ?? true) ? '공개' : '비공개' }}</td>
                                    <td>{{ $post->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'member_archive', $post->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'member_archive', $post->id]) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="9" class="text-center">등록된 자료가 없습니다.</td>
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

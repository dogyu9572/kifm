@extends('backoffice.layouts.app')

@section('title', '세션 초록 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/academic-session-abstracts.css') }}">
@endsection

@section('content')
    <div id="alertModal" class="modal">
        <div class="modal-content">
            <div id="modalHeader" class="modal-header">
                <span id="modalTitle">알림</span>
                <span class="close-modal">&times;</span>
            </div>
            <div id="modalBody" class="modal-body">
                <p id="modalMessage"></p>
            </div>
        </div>
    </div>

    @php
        $sessionDateText = $session->session_date?->format('Y-m-d') ?: '-';
        $sessionStartTime = $session->start_time ? substr((string) $session->start_time, 0, 5) : '-';
        $sessionEndTime = $session->end_time ? substr((string) $session->end_time, 0, 5) : '-';
        $sessionCategoryText = $categoryLabels[$session->category] ?? ($session->category ?: '-');
        $rows = old('items');
        if ($rows === null) {
            $rows = $session->items->map(static fn ($item) => [
                'row_type' => $item->row_type,
                'academic_event_abstract_id' => $item->academic_event_abstract_id,
                'start_time' => substr((string) $item->start_time, 0, 5),
                'end_time' => substr((string) $item->end_time, 0, 5),
                'title' => $item->title,
                'presenter' => $item->presenter,
            ])->all();
        }
    @endphp

    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif

    <div class="board-container" id="bo-session-abstracts-page">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> 행사로
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="bo-session-abstracts-header">
                    <div>
                        <h3 class="mb-1">초록 등록</h3>
                        <div class="board-form-help bo-session-abstracts-meta">
                            <span>세션명: <strong>{{ $session->name }}</strong></span>
                            <span>분류: {{ $sessionCategoryText }}</span>
                            <span>날짜: {{ $sessionDateText }}</span>
                            <span>시간: {{ $sessionStartTime }} ~ {{ $sessionEndTime }}</span>
                        </div>
                    </div>
                    <div class="bo-session-abstracts-actions">
                        <button type="button" class="btn btn-secondary btn-sm" data-session-item-action="add-break">
                            <i class="fas fa-coffee"></i> 휴식 추가
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-session-item-action="open-abstract-modal">
                            <i class="fas fa-database"></i> 초록 등록
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-session-item-action="add-abstract">
                            <i class="fas fa-plus"></i> 직접 등록
                        </button>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="board-alert board-alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('backoffice.academic-events.sessions.abstracts.update', [$event, $session]) }}" id="bo-session-abstracts-form">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive">
                        <table class="board-table bo-session-abstracts-table">
                            <thead>
                                <tr>
                                    <th class="w20">시간</th>
                                    <th>초록명/제목</th>
                                    <th class="w20">발표자/연자</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody id="bo-session-items-tbody">
                                @foreach ($rows as $index => $row)
                                    @php $rowType = $row['row_type'] ?? 'abstract'; @endphp
                                    <tr class="bo-session-item-row @if ($rowType === 'break') bo-session-break-row @endif" data-session-item-row data-row-type="{{ $rowType }}">
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][row_type]" value="{{ $rowType }}" data-item-field="row_type">
                                            <input type="hidden" name="items[{{ $index }}][academic_event_abstract_id]" value="{{ $row['academic_event_abstract_id'] ?? '' }}" data-item-field="academic_event_abstract_id">
                                            <div class="bo-session-time-fields">
                                                <input type="time" name="items[{{ $index }}][start_time]" class="board-form-control board-form-control--max-sm" value="{{ $row['start_time'] ?? '' }}" data-item-field="start_time" required>
                                                <span>~</span>
                                                <input type="time" name="items[{{ $index }}][end_time]" class="board-form-control board-form-control--max-sm" value="{{ $row['end_time'] ?? '' }}" data-item-field="end_time" required>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][title]" class="board-form-control" value="{{ $row['title'] ?? '' }}" data-item-field="title" @if ($rowType === 'break') readonly @endif>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][presenter]" class="board-form-control" value="{{ $row['presenter'] ?? '' }}" data-item-field="presenter" @if ($rowType === 'break') readonly @endif>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" data-session-item-action="remove">삭제</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="board-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 저장
                        </button>
                        <a href="{{ $cancelUrl }}" class="btn btn-secondary">목록으로</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="bo-session-abstract-row-template">
        <tr class="bo-session-item-row" data-session-item-row data-row-type="abstract">
            <td>
                <input type="hidden" data-item-field="row_type" value="abstract">
                <input type="hidden" data-item-field="academic_event_abstract_id" value="">
                <div class="bo-session-time-fields">
                    <input type="time" class="board-form-control board-form-control--max-sm" data-item-field="start_time" required>
                    <span>~</span>
                    <input type="time" class="board-form-control board-form-control--max-sm" data-item-field="end_time" required>
                </div>
            </td>
            <td><input type="text" class="board-form-control" data-item-field="title" required></td>
            <td><input type="text" class="board-form-control" data-item-field="presenter" required></td>
            <td><button type="button" class="btn btn-danger btn-sm" data-session-item-action="remove">삭제</button></td>
        </tr>
    </template>

    <template id="bo-session-break-row-template">
        <tr class="bo-session-item-row bo-session-break-row" data-session-item-row data-row-type="break">
            <td>
                <input type="hidden" data-item-field="row_type" value="break">
                <input type="hidden" data-item-field="academic_event_abstract_id" value="">
                <div class="bo-session-time-fields">
                    <input type="time" class="board-form-control board-form-control--max-sm" data-item-field="start_time" required>
                    <span>~</span>
                    <input type="time" class="board-form-control board-form-control--max-sm" data-item-field="end_time" required>
                </div>
            </td>
            <td><input type="text" class="board-form-control" data-item-field="title" value="Coffee Break" readonly></td>
            <td><input type="text" class="board-form-control" data-item-field="presenter" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm" data-session-item-action="remove">삭제</button></td>
        </tr>
    </template>

    <div class="modal bo-member-search-modal" id="bo-abstract-modal">
        <div class="modal-content bo-member-search-modal-content">
            <div class="modal-header">
                <h5 class="modal-title">초록 선택</h5>
                <button type="button" class="close" data-session-item-action="close-abstract-modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="board-filter member-search-modal-filter">
                    <div class="filter-row">
                        <div class="filter-group filter-group-grow">
                            <label class="filter-label" for="bo-abstract-modal-keyword">검색어</label>
                            <input type="text" id="bo-abstract-modal-keyword" class="filter-input" placeholder="초록 제목 또는 연자명">
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">&nbsp;</label>
                            <div class="filter-buttons">
                                <button type="button" class="btn btn-primary" data-session-item-action="filter-abstract-modal">검색</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="board-checkbox-column"><input type="checkbox" id="bo-abstract-modal-check-all"></th>
                                <th>제목</th>
                                <th class="w15">연자</th>
                                <th class="w10">상태</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($abstracts as $abstract)
                                <tr data-abstract-modal-row data-keyword="{{ $abstract->title }} {{ $abstract->author_name }}">
                                    <td>
                                        <input type="checkbox" class="bo-abstract-modal-check" value="{{ $abstract->id }}" data-title="{{ $abstract->title }}" data-presenter="{{ $abstract->author_name }}">
                                    </td>
                                    <td>{{ $abstract->title }}</td>
                                    <td>{{ $abstract->author_name }}</td>
                                    <td>{{ $statusLabels[$abstract->status] ?? $abstract->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">등록된 초록이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bo-session-modal-actions">
                    <button type="button" class="btn btn-secondary" data-session-item-action="close-abstract-modal">취소</button>
                    <button type="button" class="btn btn-primary" data-session-item-action="add-selected-abstracts">선택 추가</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-session-abstracts.js') }}"></script>
@endsection

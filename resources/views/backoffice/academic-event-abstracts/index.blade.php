@extends('backoffice.layouts.app')

@section('title', '초록 관리')

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

    <div class="board-container" id="bo-academic-event-abstracts-index" data-bulk-destroy-url="{{ route('backoffice.academic-event-abstracts.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.academic-event-abstracts.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 대상자 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.academic-event-abstracts.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="academic_event_id">행사</label>
                                <select id="academic_event_id" name="academic_event_id" class="filter-select">
                                    <option value="">전체 행사</option>
                                    @foreach ($events as $ev)
                                        <option value="{{ $ev->id }}" @selected((string) request('academic_event_id') === (string) $ev->id)>
                                            {{ $ev->year }} {{ $ev->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="presentation_type">발표 분류</label>
                                <select id="presentation_type" name="presentation_type" class="filter-select">
                                    <option value="all" @selected(request('presentation_type', 'all') === 'all')>전체</option>
                                    @foreach ($presentationTypeLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('presentation_type', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="status">상태</label>
                                <select id="status" name="status" class="filter-select">
                                    <option value="all" @selected(request('status', 'all') === 'all')>전체</option>
                                    @foreach ($statusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('status', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="search_keyword">검색어</label>
                                <input id="search_keyword" name="search_keyword" type="text" class="filter-input" value="{{ request('search_keyword', '') }}" placeholder="저자명 또는 초록 제목">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">&nbsp;</label>
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.academic-event-abstracts.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $abstracts->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.academic-event-abstracts.index') }}" id="bo-ae-abs-per-page-form" class="per-page-form">
                            <input type="hidden" name="academic_event_id" value="{{ request('academic_event_id', '') }}">
                            <input type="hidden" name="presentation_type" value="{{ request('presentation_type', 'all') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <input type="hidden" name="search_keyword" value="{{ request('search_keyword', '') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select id="per_page" name="per_page" class="per-page-select bo-ae-abs-per-page">
                                <option value="10" @selected($perPage === 10)>10건</option>
                                <option value="20" @selected($perPage === 20)>20건</option>
                                <option value="50" @selected($perPage === 50)>50건</option>
                                <option value="100" @selected($perPage === 100)>100건</option>
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
                                <th class="w15">행사명</th>
                                <th class="w20">제목</th>
                                <th class="w8">저자</th>
                                <th class="w8">등록구분</th>
                                <th class="w10">발표유형</th>
                                <th class="w8">접수일</th>
                                <th class="w8">상태</th>
                                <th class="w8">파일수령</th>
                                <th class="w10">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($abstracts as $index => $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $row->id }}" class="form-check-input bo-ae-abs-checkbox bo-row-checkbox">
                                    </td>
                                    <td>{{ $abstracts->firstItem() + $index }}</td>
                                    <td>{{ $row->event->title ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($row->title, 60) }}</td>
                                    <td>{{ $row->author_name }}</td>
                                    <td>
                                        @php
                                            $rb = $row->registered_by;
                                            $rbClass = $rb === 'admin' ? 'badge-primary' : 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $rbClass }}">{{ $registeredByListLabels[$rb] ?? $rb }}</span>
                                    </td>
                                    <td>{{ $presentationTypeLabels[$row->presentation_type] ?? $row->presentation_type }}</td>
                                    <td>{{ optional($row->submitted_at)->format('Y.m.d') }}</td>
                                    <td>
                                        @php
                                            $st = $row->status;
                                            $stClass = $st === 'confirmed' ? 'badge-success' : 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $stClass }}">{{ $statusLabels[$st] ?? $st }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $fr = $row->file_receipt_status;
                                            $frClass = 'badge-secondary';
                                            if ($fr === 'received') {
                                                $frClass = 'badge-success';
                                            } elseif ($fr === 'not_received') {
                                                $frClass = 'badge-warning';
                                            } elseif ($fr === 'not_submitted') {
                                                $frClass = 'badge-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $frClass }}">{{ $fileReceiptLabels[$fr] ?? $fr }}</span>
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.academic-event-abstracts.edit', ['academic_event_abstract' => $row, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.academic-event-abstracts.destroy', $row) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="11" class="text-center">조건에 일치하는 초록이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$abstracts" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-event-abstracts-index.js') }}"></script>
@endsection

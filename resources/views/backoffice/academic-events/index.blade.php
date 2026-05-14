@extends('backoffice.layouts.app')

@section('title', '학술행사 목록')

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

    <div class="board-container" id="bo-academic-events-index" data-bulk-destroy-url="{{ route('backoffice.academic-events.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger"><i class="fas fa-trash"></i> 선택 삭제</button>
                <a href="{{ route('backoffice.academic-events.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> 행사 생성</a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.academic-events.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">연도</label>
                                <select name="year" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($yearOptions as $year)
                                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}년</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">시즌</label>
                                <select name="season" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($seasonLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('season') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">사전등록 진행 상태</label>
                                <select name="pre_reg_status" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($preRegStatusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('pre_reg_status') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label">검색어</label>
                                <input type="text" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="행사명">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.academic-events.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info"><span class="list-count">Total : {{ $events->total() }}</span></div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.academic-events.index') }}" id="academic-event-per-page-form" class="per-page-form">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            <input type="hidden" name="season" value="{{ request('season') }}">
                            <input type="hidden" name="pre_reg_status" value="{{ request('pre_reg_status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-academic-event-per-page">
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
                                <th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                <th class="w6">연도</th>
                                <th class="w8">시즌</th>
                                <th>행사명</th>
                                <th class="w12">일시</th>
                                <th class="w14">사전 등록 기간</th>
                                <th class="w10">사전등록</th>
                                <th class="w14">초록 등록 기간</th>
                                <th class="w8">초록</th>
                                <th class="w6">공개</th>
                                <th class="w12">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $e)
                                <tr>
                                    <td><input type="checkbox" value="{{ $e->id }}" class="form-check-input bo-academic-event-checkbox bo-row-checkbox"></td>
                                    <td>{{ $e->year ?? '-' }}</td>
                                    <td>{{ $e->season ? ($seasonLabels[$e->season] ?? $e->season) : '-' }}</td>
                                    <td>
                                        {{ $e->title }}
                                    </td>
                                    <td>
                                        @if ($e->start_at)
                                            {{ $e->start_at->format('Y.m.d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($e->pre_reg_start && $e->pre_reg_end)
                                            {{ $e->pre_reg_start->format('Y.m.d') }}~{{ $e->pre_reg_end->format('Y.m.d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($e->list_pre_reg === 'upcoming')
                                            <span class="badge badge-warning">진행 예정</span>
                                        @elseif ($e->list_pre_reg === 'ongoing')
                                            <span class="badge badge-success">모집 중</span>
                                        @elseif ($e->list_pre_reg === 'closed')
                                            <span class="badge badge-danger">신청 마감</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($e->abstract_start && $e->abstract_end)
                                            {{ $e->abstract_start->format('Y.m.d') }}~{{ $e->abstract_end->format('Y.m.d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($e->list_abs === 'pending')
                                            <span class="badge badge-secondary">대기</span>
                                        @elseif ($e->list_abs === 'ongoing')
                                            <span class="badge badge-success">진행중</span>
                                        @elseif ($e->list_abs === 'closed')
                                            <span class="badge badge-danger">마감</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $e->is_public === 'Y' ? '공개' : '비공개' }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.academic-events.edit', ['academic_event' => $e, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 수정</a>
                                            <form action="{{ route('backoffice.academic-events.destroy', $e) }}" method="POST" class="d-inline js-delete-confirm-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> 삭제</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">조건에 일치하는 데이터가 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$events" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-events-index.js') }}"></script>
@endsection

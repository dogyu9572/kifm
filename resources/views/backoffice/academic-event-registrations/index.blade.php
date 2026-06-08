@extends('backoffice.layouts.app')

@section('title', '참가 및 결제 관리')

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

    <div class="board-container" id="bo-academic-event-registrations-index" data-bulk-destroy-url="{{ route('backoffice.academic-event-registrations.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger"><i class="fas fa-trash"></i> 선택 삭제</button>
                <a href="{{ route('backoffice.academic-event-registrations.export', request()->query()) }}" class="btn btn-secondary">
                    <i class="fas fa-file-download"></i> 엑셀 다운로드
                </a>
                <a href="{{ route('backoffice.academic-event-registrations.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 직접 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.academic-event-registrations.index') }}" class="filter-form">
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
                                <label class="filter-label" for="payment_status">결제 상태</label>
                                <select id="payment_status" name="payment_status" class="filter-select">
                                    <option value="all">전체 상태</option>
                                    @foreach ($paymentStatusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('payment_status', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="reg_type">등록 구분</label>
                                <select id="reg_type" name="reg_type" class="filter-select">
                                    <option value="all">전체</option>
                                    @foreach ($regTypeLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('reg_type', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="search_keyword">검색어</label>
                                <input id="search_keyword" name="search_keyword" type="text" class="filter-input" value="{{ request('search_keyword', '') }}" placeholder="이름 또는 연락처">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">&nbsp;</label>
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.academic-event-registrations.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $registrations->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.academic-event-registrations.index') }}" id="bo-ae-reg-per-page-form" class="per-page-form">
                            <input type="hidden" name="academic_event_id" value="{{ request('academic_event_id', '') }}">
                            <input type="hidden" name="payment_status" value="{{ request('payment_status', 'all') }}">
                            <input type="hidden" name="reg_type" value="{{ request('reg_type', 'all') }}">
                            <input type="hidden" name="search_keyword" value="{{ request('search_keyword', '') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select id="per_page" name="per_page" class="per-page-select bo-ae-reg-per-page">
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
                                <th class="w3">번호</th>
                                <th class="w10">참가 번호</th>
                                <th class="w15">행사명</th>
                                <th class="w5">이름</th>
                                <th class="w8">휴대폰번호</th>
                                <th class="w10">이메일</th>
                                <th class="w5">등록 구분</th>
                                <th class="w10">결제항목</th>
                                <th class="w5">결제수단</th>
                                <th class="w5">결제 상태</th>
                                <th class="w8">서류 출력</th>
                                <th class="w7">등록일</th>
                                <th class="w10">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registrations as $index => $r)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $r->id }}" class="form-check-input bo-ae-reg-checkbox bo-row-checkbox">
                                    </td>
                                    <td>{{ $registrations->firstItem() + $index }}</td>
                                    <td>{{ $r->registration_no ?: '-' }}</td>
                                    <td>{{ $r->event->title ?? '-' }}</td>
                                    <td>{{ $r->name }}</td>
                                    <td>{{ $r->phone ?: '-' }}</td>
                                    <td>{{ $r->email ?: '-' }}</td>
                                    <td>{{ $regTypeLabels[$r->reg_type] ?? $r->reg_type }}</td>
                                    <td>
                                        @if ($r->items->isEmpty())
                                            -
                                        @else
                                            {{ \Illuminate\Support\Str::limit($r->items->pluck('item_name')->implode(', '), 40) }}
                                        @endif
                                    </td>
                                    <td>{{ $paymentMethodLabels[$r->payment_method] ?? $r->payment_method }}</td>
                                    <td>{{ $paymentStatusLabels[$r->payment_status] ?? $r->payment_status }}</td>
                                    <td>
                                        @if ($r->payment_status === 'completed')
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.academic-event-registrations.print-receipt', $r) }}" class="btn btn-secondary btn-sm" target="_blank" rel="noopener">
                                                    영수증
                                                </a>
                                                @if ($r->cancelled_at === null)
                                                    <a href="{{ route('backoffice.academic-event-registrations.print-participation', $r) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                                        참가증명서
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ optional($r->registered_at)->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.academic-event-registrations.edit', ['academic_event_registration' => $r, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.academic-event-registrations.destroy', $r) }}" method="POST" class="d-inline js-ae-reg-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> 삭제</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">등록된 참가 내역이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$registrations" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/academic-event-registrations-index.js') }}"></script>
@endsection

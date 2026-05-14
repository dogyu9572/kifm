@extends('backoffice.layouts.app')

@section('title', '연수교육 참가 및 결제 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/edu-training-payments.css') }}">
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
                <a href="{{ route('backoffice.edu-training-payments.export', request()->query()) }}" class="btn btn-secondary">
                    <i class="fas fa-file-download"></i> 엑셀 다운로드
                </a>
                <a href="{{ route('backoffice.edu-training-payments.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 직접 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter bo-edu-payment-filter">
                    <form method="GET" action="{{ route('backoffice.edu-training-payments.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="training_id">연수</label>
                                <select id="training_id" name="training_id" class="filter-select">
                                    <option value="">전체 연수</option>
                                    @foreach ($trainings as $training)
                                        <option value="{{ $training->id }}" @selected((string) request('training_id') === (string) $training->id)>
                                            {{ $training->year }} {{ $training->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="payment_status">결제 상태</label>
                                <select id="payment_status" name="payment_status" class="filter-select">
                                    <option value="all">전체 상태</option>
                                    @foreach ($statusLabels as $code => $label)
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
                                    <a href="{{ route('backoffice.edu-training-payments.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $payments->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.edu-training-payments.index') }}" class="per-page-form">
                            <input type="hidden" name="training_id" value="{{ request('training_id', '') }}">
                            <input type="hidden" name="payment_status" value="{{ request('payment_status', 'all') }}">
                            <input type="hidden" name="reg_type" value="{{ request('reg_type', 'all') }}">
                            <input type="hidden" name="search_keyword" value="{{ request('search_keyword', '') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select id="per_page" name="per_page" class="per-page-select bo-per-page-select">
                                <option value="10" @selected($perPage === 10)>10건</option>
                                <option value="20" @selected($perPage === 20)>20건</option>
                                <option value="50" @selected($perPage === 50)>50건</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w8">번호</th>
                                <th class="w12">주문번호</th>
                                <th>연수명</th>
                                <th class="w8">이름</th>
                                <th class="w12">휴대폰번호</th>
                                <th class="w12">이메일</th>
                                <th class="w8">등록 구분</th>
                                <th class="w10">결제항목</th>
                                <th class="w8">결제수단</th>
                                <th class="w10">결제 상태</th>
                                <th class="w10">등록일</th>
                                <th class="w12">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $index => $payment)
                                <tr>
                                    <td>{{ $payments->firstItem() + $index }}</td>
                                    <td>{{ $payment->order_no }}</td>
                                    <td>{{ $payment->training->title ?? '-' }}</td>
                                    <td>{{ $payment->name }}</td>
                                    <td>{{ $payment->phone ?: '-' }}</td>
                                    <td>{{ $payment->email ?: '-' }}</td>
                                    <td>{{ $regTypeLabels[$payment->reg_type] ?? $payment->reg_type }}</td>
                                    <td>
                                        @if ($payment->items->isEmpty())
                                            -
                                        @else
                                            {{ $payment->items->pluck('item_name')->implode(', ') }}
                                        @endif
                                    </td>
                                    <td>{{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}</td>
                                    <td>{{ $statusLabels[$payment->payment_status] ?? $payment->payment_status }}</td>
                                    <td>{{ optional($payment->registered_at)->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.edu-training-payments.edit', ['edu_training_payment' => $payment, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">수정</a>
                                            <form action="{{ route('backoffice.edu-training-payments.cancel', $payment) }}" method="POST" class="d-inline js-cancel-form">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">취소</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">등록된 참가 내역이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$payments" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/edu-training-payments-index.js') }}"></script>
@endsection


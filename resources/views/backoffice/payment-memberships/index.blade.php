@extends('backoffice.layouts.app')

@section('title', '회비 납부 내역')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/payment-memberships.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter bo-payment-membership-filter">
                    <form method="GET" action="{{ route('backoffice.payment-memberships.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="payment_status">처리상태</label>
                                <select id="payment_status" name="payment_status" class="filter-select">
                                    <option value="all">전체</option>
                                    @foreach ($paymentStatusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('payment_status', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="date_from">결제 신청일</label>
                                <div class="bo-payment-date-range">
                                    <input type="date" id="date_from" name="date_from" class="filter-input" value="{{ request('date_from', '') }}">
                                    <span class="bo-payment-date-sep">~</span>
                                    <input type="date" id="date_to" name="date_to" class="filter-input" value="{{ request('date_to', '') }}">
                                </div>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label">&nbsp;</label>
                                <div class="bo-payment-inline-actions">
                                    <a href="{{ $presetDateUrls['all'] }}" class="btn btn-outline-secondary btn-sm">전체</a>
                                    <a href="{{ $presetDateUrls['today'] }}" class="btn btn-outline-secondary btn-sm">오늘</a>
                                    <a href="{{ $presetDateUrls['yesterday'] }}" class="btn btn-outline-secondary btn-sm">어제</a>
                                    <a href="{{ $presetDateUrls['week'] }}" class="btn btn-outline-secondary btn-sm">1주일</a>
                                    <a href="{{ $presetDateUrls['month'] }}" class="btn btn-outline-secondary btn-sm">이달</a>
                                    <a href="{{ $presetDateUrls['year'] }}" class="btn btn-outline-secondary btn-sm">올해</a>
                                </div>
                            </div>
                        </div>
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="keyword_type">검색어</label>
                                <select id="keyword_type" name="keyword_type" class="filter-select">
                                    @foreach ($keywordTypeLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('keyword_type', 'memberId') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="keyword">검색어 입력</label>
                                <input id="keyword" name="keyword" type="text" class="filter-input" value="{{ request('keyword', '') }}" placeholder="검색어를 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">&nbsp;</label>
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.payment-memberships.index') }}" class="btn btn-secondary">
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
                        <form method="GET" action="{{ route('backoffice.payment-memberships.index') }}" class="per-page-form">
                            <input type="hidden" name="payment_status" value="{{ request('payment_status', 'all') }}">
                            <input type="hidden" name="date_from" value="{{ request('date_from', '') }}">
                            <input type="hidden" name="date_to" value="{{ request('date_to', '') }}">
                            <input type="hidden" name="keyword_type" value="{{ request('keyword_type', 'memberId') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword', '') }}">
                            <label for="perPageSelect" class="per-page-label">표시 개수:</label>
                            <select id="perPageSelect" name="per_page" class="per-page-select bo-per-page-select">
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
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w10">결제번호</th>
                                <th class="w8">회원등급</th>
                                <th class="w10">아이디</th>
                                <th class="w8">이름</th>
                                <th class="w12">핸드폰번호</th>
                                <th class="w12">이메일주소</th>
                                <th class="w8">결제방식</th>
                                <th class="w8">결제여부</th>
                                <th class="w10">결제 신청일</th>
                                <th class="w10">결제 완료일</th>
                                <th class="w12">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input bo-row-checkbox"></td>
                                    <td>{{ $payment->payment_no }}</td>
                                    <td>{{ $memberLevelLabels[$payment->member_grade ?? ''] ?? ($payment->member_grade ?? '-') }}</td>
                                    <td>{{ $payment->member->login_id ?? '-' }}</td>
                                    <td>{{ $payment->member->name ?? '-' }}</td>
                                    <td>{{ $payment->member->phone_number ?? '-' }}</td>
                                    <td>{{ $payment->member->email ?? '-' }}</td>
                                    <td>{{ $paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method }}</td>
                                    <td>
                                        <span class="bo-payment-badge bo-payment-badge--{{ $payment->payment_status }}">
                                            {{ $paymentStatusLabels[$payment->payment_status] ?? $payment->payment_status }}
                                        </span>
                                    </td>
                                    <td>{{ optional($payment->requested_at)->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ optional($payment->paid_at)->format('Y-m-d') ?? '-' }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.payment-memberships.edit', $payment) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.payment-memberships.destroy', $payment) }}" method="POST" class="d-inline js-confirm-delete">
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
                                    <td colspan="12" class="text-center">납부 내역이 없습니다.</td>
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


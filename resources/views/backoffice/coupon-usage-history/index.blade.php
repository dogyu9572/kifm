@extends('backoffice.layouts.app')

@section('title', '쿠폰 사용 이력')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/coupons.css') }}">
@endsection

@section('content')
    <div class="board-container" id="bo-coupon-history-index">
        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.coupon-usage-history.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="filter_used_from">사용일시</label>
                                <div class="date-range">
                                    <input type="date" name="used_from" id="filter_used_from" class="filter-input"
                                        value="{{ request('used_from') }}">
                                    <span class="date-separator">~</span>
                                    <input type="date" name="used_to" id="filter_used_to" class="filter-input"
                                        value="{{ request('used_to') }}">
                                </div>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_payment_category">적용 결제 항목</label>
                                <select name="payment_category" id="filter_payment_category" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($categoryLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('payment_category') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_coupon_code">쿠폰 코드</label>
                                <input type="text" name="coupon_code" id="filter_coupon_code" class="filter-input"
                                    value="{{ request('coupon_code') }}" placeholder="쿠폰 코드를 입력하세요.">
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="filter_member_keyword">회원명 / 아이디</label>
                                <input type="text" name="member_keyword" id="filter_member_keyword" class="filter-input"
                                    value="{{ request('member_keyword') }}" placeholder="회원명 또는 아이디를 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.coupon-usage-history.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $histories->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.coupon-usage-history.index') }}" class="per-page-form" id="coupon-history-per-page-form">
                            <input type="hidden" name="used_from" value="{{ request('used_from') }}">
                            <input type="hidden" name="used_to" value="{{ request('used_to') }}">
                            <input type="hidden" name="payment_category" value="{{ request('payment_category') }}">
                            <input type="hidden" name="coupon_code" value="{{ request('coupon_code') }}">
                            <input type="hidden" name="member_keyword" value="{{ request('member_keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-coupon-history-per-page">
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
                                <th class="w8">번호</th>
                                <th>쿠폰명</th>
                                <th class="w10">쿠폰 코드</th>
                                <th class="w10">회원명</th>
                                <th class="w10">아이디</th>
                                <th class="w12">적용 결제 항목</th>
                                <th class="w14">사용일시</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $history)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $history->id ?? $loop->index }}" class="form-check-input bo-row-checkbox">
                                    </td>
                                    <td>{{ $histories->total() - (($histories->currentPage() - 1) * $histories->perPage()) - $loop->index }}</td>
                                    <td>{{ $history->coupon_name }}</td>
                                    <td><span class="bo-coupon-code">{{ $history->coupon_code }}</span></td>
                                    <td>{{ $history->member_name }}</td>
                                    <td>{{ $history->member_login_id }}</td>
                                    <td>{{ $categoryLabels[$history->payment_category] ?? $history->payment_category }}</td>
                                    <td>{{ $history->used_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">쿠폰 사용 이력이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$histories" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/coupon-usage-history-index.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-table-select-all.js') }}"></script>
@endsection

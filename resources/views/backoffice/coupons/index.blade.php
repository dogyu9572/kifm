@extends('backoffice.layouts.app')

@section('title', '쿠폰 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/coupons.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container" id="bo-coupons-index" data-bulk-destroy-url="{{ route('backoffice.coupons.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled>
                    <i class="fas fa-trash"></i> 선택 삭제 (0)
                </button>
                <a href="{{ route('backoffice.coupons.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 쿠폰 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.coupons.index') }}" class="filter-form">
                        <div class="filter-row">
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
                                <label class="filter-label" for="filter_valid_from">유효기간</label>
                                <div class="date-range">
                                    <input type="date" name="valid_from" id="filter_valid_from" class="filter-input"
                                        value="{{ request('valid_from') }}">
                                    <span class="date-separator">~</span>
                                    <input type="date" name="valid_to" id="filter_valid_to" class="filter-input"
                                        value="{{ request('valid_to') }}">
                                </div>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_status">사용 여부</label>
                                <select name="status" id="filter_status" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="ACTIVE" @selected(request('status') === 'ACTIVE')>사용</option>
                                    <option value="INACTIVE" @selected(request('status') === 'INACTIVE')>미사용</option>
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="filter_keyword">쿠폰명</label>
                                <input type="text" name="keyword" id="filter_keyword" class="filter-input"
                                    value="{{ request('keyword') }}" placeholder="쿠폰명을 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.coupons.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $coupons->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.coupons.index') }}" class="per-page-form" id="coupon-per-page-form">
                            <input type="hidden" name="payment_category" value="{{ request('payment_category') }}">
                            <input type="hidden" name="valid_from" value="{{ request('valid_from') }}">
                            <input type="hidden" name="valid_to" value="{{ request('valid_to') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-coupon-per-page">
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
                                <th class="w14">적용 결제 항목</th>
                                <th class="w8">할인 방식</th>
                                <th class="w10">할인 금액/율</th>
                                <th class="w14">유효기간</th>
                                <th class="w6">사용 횟수</th>
                                <th class="w8">사용 여부</th>
                                <th class="w10">등록일</th>
                                <th class="w14">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coupons as $coupon)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $coupon->id }}" class="form-check-input bo-coupon-checkbox bo-row-checkbox">
                                    </td>
                                    <td>{{ $coupons->total() - (($coupons->currentPage() - 1) * $coupons->perPage()) - $loop->index }}</td>
                                    <td>{{ $coupon->coupon_name }}</td>
                                    <td><span class="bo-coupon-code">{{ $coupon->coupon_code }}</span></td>
                                    <td>
                                        @foreach ($coupon->paymentCategories->sortBy('payment_category') as $pc)
                                            {{ $categoryLabels[$pc->payment_category] ?? $pc->payment_category }}@if (!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $coupon->discount_type === 'FIXED' ? '정액' : '정률' }}</td>
                                    <td>
                                        @if ($coupon->discount_type === 'FIXED')
                                            {{ number_format((float) $coupon->discount_value) }}원
                                        @else
                                            {{ rtrim(rtrim(number_format((float) $coupon->discount_value, 2, '.', ''), '0'), '.') }}%
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->valid_from->format('Y.m.d') }} ~ {{ $coupon->valid_to->format('Y.m.d') }}
                                    </td>
                                    <td>{{ $coupon->usage_count }}</td>
                                    <td>{{ $coupon->status === 'ACTIVE' ? '사용' : '미사용' }}</td>
                                    <td>{{ $coupon->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.coupons.edit', ['coupon' => $coupon, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.coupons.destroy', $coupon) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="12" class="text-center">등록된 쿠폰이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$coupons" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/coupons-index.js') }}"></script>
@endsection

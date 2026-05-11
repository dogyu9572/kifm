@extends('backoffice.layouts.app')

@section('title', '결제 항목 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/payment-plans.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container" id="bo-payment-plans-index" data-bulk-destroy-url="{{ route('backoffice.payment-plans.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.payment-plans.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 항목 추가
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.payment-plans.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="filter_category">결제 항목</label>
                                <select name="category" id="filter_category" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($categoryLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('category') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_member_status">회원 여부</label>
                                <select name="member_status" id="filter_member_status" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="member" @selected(request('member_status') === 'member')>회원</option>
                                    <option value="non-member" @selected(request('member_status') === 'non-member')>비회원</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_executive">임원 여부</label>
                                <select name="executive" id="filter_executive" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="executive" @selected(request('executive') === 'executive')>임원</option>
                                    <option value="non-executive" @selected(request('executive') === 'non-executive')>임원 아님</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_member_type">구분</label>
                                <select name="member_type" id="filter_member_type" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($memberTypeLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('member_type') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_use_status">사용 여부</label>
                                <select name="use_status" id="filter_use_status" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="active" @selected(request('use_status') === 'active')>사용</option>
                                    <option value="inactive" @selected(request('use_status') === 'inactive')>미사용</option>
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="filter_keyword">결제항목명</label>
                                <input type="text" name="keyword" id="filter_keyword" class="filter-input"
                                    value="{{ request('keyword') }}" placeholder="결제항목명을 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.payment-plans.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $plans->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.payment-plans.index') }}" class="per-page-form" id="payment-plan-per-page-form">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="member_status" value="{{ request('member_status') }}">
                            <input type="hidden" name="executive" value="{{ request('executive') }}">
                            <input type="hidden" name="member_type" value="{{ request('member_type') }}">
                            <input type="hidden" name="use_status" value="{{ request('use_status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-per-page-select">
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
                                <th class="w10">결제 항목</th>
                                <th>결제항목명</th>
                                <th class="w8">회원 여부</th>
                                <th class="w12">회원 등급</th>
                                <th class="w12">구분</th>
                                <th class="w8">임원 여부</th>
                                <th class="w12">금액</th>
                                <th class="w8">사용 여부</th>
                                <th class="w10">등록일</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plans as $plan)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $plan->id }}" class="form-check-input bo-plan-checkbox">
                                    </td>
                                    <td>{{ $plans->total() - (($plans->currentPage() - 1) * $plans->perPage()) - $loop->index }}</td>
                                    <td>{{ $categoryLabels[$plan->category] ?? $plan->category }}</td>
                                    <td>{{ $plan->plan_name }}</td>
                                    <td>{{ $plan->member_status === 'member' ? '회원' : '비회원' }}</td>
                                    <td>
                                        @if ($plan->member_status === 'non-member')
                                            -
                                        @else
                                            @foreach ($plan->grades as $g)
                                                {{ $gradeLabels[$g->grade] ?? $g->grade }}@if (!$loop->last), @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($plan->member_status === 'non-member')
                                            -
                                        @else
                                            @foreach ($plan->types as $t)
                                                {{ $memberTypeLabels[$t->member_type] ?? $t->member_type }}@if (!$loop->last), @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($plan->member_status === 'non-member')
                                            -
                                        @elseif ($plan->executive === 'executive')
                                            임원
                                        @else
                                            임원 아님
                                        @endif
                                    </td>
                                    <td class="bo-amount-cell">
                                        @if ($plan->category === 'conference')
                                            <span class="bo-amount-line">사전 {{ number_format((int) $plan->price_early) }}원</span>
                                            <span class="bo-amount-line">현장 {{ number_format((int) $plan->price_site) }}원</span>
                                        @else
                                            {{ number_format((int) $plan->price) }}원
                                        @endif
                                    </td>
                                    <td>{{ $plan->use_status === 'active' ? '사용' : '미사용' }}</td>
                                    <td>{{ $plan->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.payment-plans.edit', ['payment_plan' => $plan, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.payment-plans.destroy', $plan) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="12" class="text-center">등록된 결제 항목이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$plans" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/payment-plans-index.js') }}"></script>
@endsection

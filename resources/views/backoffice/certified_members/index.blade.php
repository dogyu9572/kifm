@extends('backoffice.layouts.app')

@section('title', '인정의 관리')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
@endsection

@section('content')
@if (session('success'))
    <div class="board-alert board-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="board-container">
    <div class="board-page-header">
        <div class="board-page-buttons">
            <a href="{{ route('backoffice.certified-members.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> 인정의 등록
            </a>
        </div>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <div class="board-filter bo-certified-filter">
                <form method="GET" action="{{ route('backoffice.certified-members.index') }}" class="filter-form">
                    <div class="bo-member-inline-row bo-certified-inline-row">
                        <span class="bo-member-inline-label">상태</span>
                        <div class="checkbox-group bo-certified-inline-options">
                            <label class="checkbox-label"><input type="radio" name="status" value="all" @checked(($filters['status'] ?? 'all') === 'all')><span>전체</span></label>
                            <label class="checkbox-label"><input type="radio" name="status" value="active" @checked(($filters['status'] ?? '') === 'active')><span>정상</span></label>
                            <label class="checkbox-label"><input type="radio" name="status" value="expired" @checked(($filters['status'] ?? '') === 'expired')><span>만료</span></label>
                        </div>
                    </div>
                    <div class="bo-member-inline-row bo-certified-inline-row">
                        <span class="bo-member-inline-label">유효 기간</span>
                        <div class="bo-certified-date-range">
                            <input type="date" id="validity_start" name="validity_start" class="filter-input board-form-control--max-md" value="{{ $filters['validity_start'] ?? '' }}">
                            <span class="bo-member-date-sep">~</span>
                            <input type="date" id="validity_end" name="validity_end" class="filter-input board-form-control--max-md" value="{{ $filters['validity_end'] ?? '' }}">
                        </div>
                    </div>
                    <div class="bo-member-inline-row bo-certified-inline-row">
                        <span class="bo-member-inline-label">남은 기간</span>
                        <div class="checkbox-group bo-certified-inline-options">
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="all" @checked(($filters['remaining_period'] ?? 'all') === 'all')><span>전체</span></label>
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="1y" @checked(($filters['remaining_period'] ?? '') === '1y')><span>1년</span></label>
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="6m" @checked(($filters['remaining_period'] ?? '') === '6m')><span>6개월</span></label>
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="3m" @checked(($filters['remaining_period'] ?? '') === '3m')><span>3개월</span></label>
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="1m" @checked(($filters['remaining_period'] ?? '') === '1m')><span>1개월</span></label>
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="2w" @checked(($filters['remaining_period'] ?? '') === '2w')><span>2주일</span></label>
                            <label class="checkbox-label"><input type="radio" name="remaining_period" value="1w" @checked(($filters['remaining_period'] ?? '') === '1w')><span>1주일</span></label>
                        </div>
                    </div>
                    <div class="bo-member-inline-row bo-certified-inline-row bo-certified-search-row">
                        <span class="bo-member-inline-label">검색어</span>
                        <select id="search_field" name="search_field" class="filter-select">
                            <option value="name" @selected(($filters['search_field'] ?? 'name') === 'name')>이름</option>
                            <option value="id" @selected(($filters['search_field'] ?? '') === 'id')>아이디</option>
                            <option value="license" @selected(($filters['search_field'] ?? '') === 'license')>의사면허번호</option>
                        </select>
                        <input id="keyword" type="text" name="keyword" class="filter-input bo-certified-search-input" value="{{ $filters['keyword'] ?? '' }}" placeholder="검색어를 입력하세요.">
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> 검색
                            </button>
                            <a href="{{ route('backoffice.certified-members.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> 초기화
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="board-list-header">
                <div class="list-info">
                    <span class="list-count">Total : {{ $certifiedMembers->total() }}</span>
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
                            <th>아이디</th>
                            <th>이름</th>
                            <th>이메일</th>
                            <th>연락처</th>
                            <th>의사면허번호</th>
                            <th>상태</th>
                            <th>유효기간</th>
                            <th>남은 기간</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($certifiedMembers as $certifiedMember)
                            <tr>
                                <td>
                                    <input type="checkbox" value="{{ $certifiedMember->id }}" class="form-check-input bo-row-checkbox">
                                </td>
                                <td>{{ $certifiedMembers->total() - (($certifiedMembers->currentPage() - 1) * $certifiedMembers->perPage()) - $loop->index }}</td>
                                <td>{{ $certifiedMember->member->login_id ?? '-' }}</td>
                                <td>{{ $certifiedMember->member->name ?? '-' }}</td>
                                <td>{{ $certifiedMember->member->email ?? '-' }}</td>
                                <td>{{ $certifiedMember->member->phone_number ?? '-' }}</td>
                                <td>{{ $certifiedMember->member->license_number ?? '-' }}</td>
                                <td>{{ $certifiedMember->statusLabel() }}</td>
                                <td>{{ optional($certifiedMember->validity_start_date)->format('Y.m.d') }} ~ {{ optional($certifiedMember->validity_end_date)->format('Y.m.d') }}</td>
                                <td>
                                    @if ($certifiedMember->remainingDays() < 0)
                                        -
                                    @else
                                        {{ $certifiedMember->remainingDays() }}일
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('backoffice.certified-members.edit', ['certified_member' => $certifiedMember, 'return_url' => request()->fullUrl()]) }}" class="btn btn-primary btn-sm">확인</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">등록된 인정의 데이터가 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$certifiedMembers" />
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/board-table-select-all.js') }}"></script>
@endsection

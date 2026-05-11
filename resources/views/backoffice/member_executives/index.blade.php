@extends('backoffice.layouts.app')

@section('title', '임원 관리')

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
            <a href="{{ route('backoffice.member-executives.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> 임원 추가
            </a>
        </div>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <div class="board-filter bo-exec-filter">
                <form method="GET" action="{{ route('backoffice.member-executives.index') }}" class="filter-form">
                    <div class="bo-member-inline-row bo-exec-inline-row">
                        <span class="bo-member-inline-label">임원 직책</span>
                        <div class="checkbox-group bo-member-inline-group">
                            <label class="checkbox-label"><input type="checkbox" name="roles[]" value="all" @checked(in_array('all', (array) request('roles', []), true))><span>전체</span></label>
                            @foreach ($roleLabels as $code => $label)
                                <label class="checkbox-label"><input type="checkbox" name="roles[]" value="{{ $code }}" @checked(in_array($code, (array) request('roles', []), true))><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </div>
                    <div class="bo-member-inline-row bo-exec-inline-row">
                        <span class="bo-member-inline-label">임기 상태</span>
                        <div class="checkbox-group bo-member-inline-group">
                            <label class="checkbox-label"><input type="radio" name="term_status" value="all" @checked(request('term_status', 'all') === 'all')><span>전체</span></label>
                            <label class="checkbox-label"><input type="radio" name="term_status" value="active" @checked(request('term_status') === 'active')><span>임기중</span></label>
                            <label class="checkbox-label"><input type="radio" name="term_status" value="expired" @checked(request('term_status') === 'expired')><span>임기만료</span></label>
                            <label class="checkbox-label"><input type="radio" name="term_status" value="upcoming" @checked(request('term_status') === 'upcoming')><span>임기예정</span></label>
                        </div>
                    </div>
                    <div class="bo-member-inline-row bo-exec-inline-row bo-exec-search-row">
                        <span class="bo-member-inline-label">검색어</span>
                        <select id="search_field" name="search_field" class="filter-select">
                            <option value="name" @selected(request('search_field', 'name') === 'name')>이름</option>
                            <option value="email" @selected(request('search_field') === 'email')>이메일</option>
                            <option value="phone" @selected(request('search_field') === 'phone')>연락처</option>
                        </select>
                        <input id="search_keyword" type="text" name="search_keyword" class="filter-input bo-exec-search-input" value="{{ request('search_keyword', '') }}" placeholder="검색어를 입력하세요.">
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> 검색
                            </button>
                            <a href="{{ route('backoffice.member-executives.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> 초기화
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="board-list-header">
                <div class="list-info">
                    <span class="list-count">Total : {{ $executives->total() }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th>등급</th>
                            <th>임원 직책</th>
                            <th>임기 상태</th>
                            <th>임기</th>
                            <th>이름</th>
                            <th>이메일</th>
                            <th>연락처</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($executives as $executive)
                            <tr>
                                <td>{{ $memberLevelLabels[$executive->member->member_level ?? ''] ?? '-' }}</td>
                                <td>{{ $roleLabels[$executive->executive_role] ?? $executive->executive_role }}</td>
                                <td>{{ $executive->termStatusLabel() }}</td>
                                <td>
                                    {{ optional($executive->term_start_date)->format('Y.m.d') ?? '-' }}
                                    ~
                                    {{ $executive->is_indefinite ? '무기한' : (optional($executive->term_end_date)->format('Y.m.d') ?? '-') }}
                                </td>
                                <td>{{ $executive->member->name ?? '-' }}</td>
                                <td>{{ $executive->member->email ?? '-' }}</td>
                                <td>{{ $executive->member->phone_number ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('backoffice.member-executives.edit', $executive) }}" class="btn btn-primary btn-sm">확인</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">등록된 임원 데이터가 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$executives" />
        </div>
    </div>
</div>
@endsection


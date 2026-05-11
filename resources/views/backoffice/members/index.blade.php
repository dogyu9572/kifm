@extends('backoffice.layouts.app')

@section('title', '회원 관리')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/members-list.css') }}">
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/backoffice/members.js') }}"></script>
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
            <button type="button" id="btnDeleteMultiple" class="btn btn-danger">
                <i class="fas fa-trash"></i> 선택 삭제
            </button>
            <button type="button" id="btnExport" class="btn btn-secondary">
                <i class="fas fa-file-excel"></i> 엑셀 다운로드
            </button>
            <a href="{{ route('backoffice.members.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> 회원 등록
            </a>
        </div>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <div class="board-filter">
                <form method="GET" action="{{ route('backoffice.members.index') }}" class="filter-form" id="searchForm">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    @php
                        $gSel = $filters['grades'] ?? [];
                        if (! is_array($gSel)) {
                            $gSel = [];
                        }
                    @endphp

                    <div class="bo-member-inline-row bo-member-inline-row--search">
                        <div class="bo-member-inline-item bo-member-inline-item--sort">
                            <span class="bo-member-inline-label">정렬방식</span>
                            <div class="board-radio-group bo-member-inline-group">
                                <div class="board-radio-item">
                                    <input type="radio" id="sort_joinDate" name="sort_order" value="joinDate" class="board-radio-input" @checked(($filters['sort_order'] ?? 'joinDate') === 'joinDate')>
                                    <label for="sort_joinDate">가입일순</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="sort_name" name="sort_order" value="name" class="board-radio-input" @checked(($filters['sort_order'] ?? '') === 'name')>
                                    <label for="sort_name">이름순</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="sort_id" name="sort_order" value="id" class="board-radio-input" @checked(($filters['sort_order'] ?? '') === 'id')>
                                    <label for="sort_id">ID순</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-member-inline-row bo-member-inline-row--due">
                        <div class="bo-member-inline-item bo-member-inline-item--grade">
                            <span class="bo-member-inline-label">회원등급</span>
                            <div class="checkbox-group bo-member-inline-group">
                                @foreach ($memberLevelLabels as $code => $label)
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="grades[]" value="{{ $code }}" @checked(in_array($code, $gSel, true))>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="bo-member-inline-item bo-member-inline-item--certified bo-member-inline-item--break">
                            <span class="bo-member-inline-label">인정의 여부</span>
                            <div class="board-radio-group bo-member-inline-group bo-member-inline-group--due">
                                <div class="board-radio-item">
                                    <input type="radio" id="cert_all" name="is_certified" value="all" class="board-radio-input" @checked(($filters['is_certified'] ?? 'all') === 'all')>
                                    <label for="cert_all">전체</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="cert_yes" name="is_certified" value="certified" class="board-radio-input" @checked(($filters['is_certified'] ?? '') === 'certified')>
                                    <label for="cert_yes">인정의</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="cert_no" name="is_certified" value="none" class="board-radio-input" @checked(($filters['is_certified'] ?? '') === 'none')>
                                    <label for="cert_no">비인정의</label>
                                </div>
                            </div>
                        </div>
                        <div class="bo-member-inline-item bo-member-inline-item--break">
                            <span class="bo-member-inline-label">휴면회원</span>
                            <label class="checkbox-label">
                                <input type="checkbox" name="inactive_only" value="1" @checked(! empty($filters['inactive_only']))>
                                <span>1년 이상 로그인 하지 않은 회원만 출력</span>
                            </label>
                        </div>
                    </div>

                    <div class="bo-member-inline-row">
                        <div class="bo-member-inline-item bo-member-inline-item--wide">
                            <span class="bo-member-inline-label">최종 회비 납부 기준일</span>
                            <div class="board-radio-group bo-member-inline-group">
                                <div class="board-radio-item">
                                    <input type="radio" id="due_all" name="due_mode" value="all" class="board-radio-input" @checked(($filters['due_mode'] ?? 'all') === 'all')>
                                    <label for="due_all">모두</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="due_gte" name="due_mode" value="gte" class="board-radio-input" @checked(($filters['due_mode'] ?? '') === 'gte')>
                                    <label for="due_gte">이상</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="due_lte" name="due_mode" value="lte" class="board-radio-input" @checked(($filters['due_mode'] ?? '') === 'lte')>
                                    <label for="due_lte">이하</label>
                                </div>
                                <input type="date" id="due_date" name="due_date" class="filter-input bo-member-date-input" value="{{ $filters['due_date'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="bo-member-inline-row">
                        <div class="bo-member-inline-item bo-member-inline-item--wide">
                            <span class="bo-member-inline-label">임원 여부</span>
                            <div class="board-radio-group bo-member-inline-group">
                                <div class="board-radio-item">
                                    <input type="radio" id="ex_all" name="executive_status" value="all" class="board-radio-input" @checked(($filters['executive_status'] ?? 'all') === 'all')>
                                    <label for="ex_all">전체</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="ex_yes" name="executive_status" value="executive" class="board-radio-input" @checked(($filters['executive_status'] ?? '') === 'executive')>
                                    <label for="ex_yes">임원</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="ex_no" name="executive_status" value="non_executive" class="board-radio-input" @checked(($filters['executive_status'] ?? '') === 'non_executive')>
                                    <label for="ex_no">비임원</label>
                                </div>
                            </div>
                        </div>
                        <div class="bo-member-inline-item bo-member-inline-item--break">
                            <span class="bo-member-inline-label">연회비</span>
                            <div class="board-radio-group bo-member-inline-group">
                                <div class="board-radio-item">
                                    <input type="radio" id="af_all" name="annual_fee" value="all" class="board-radio-input" @checked(($filters['annual_fee'] ?? 'all') === 'all')>
                                    <label for="af_all">모두</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="af_none" name="annual_fee" value="none" class="board-radio-input" @checked(($filters['annual_fee'] ?? '') === 'none')>
                                    <label for="af_none">연회비가 없는 회원</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="af_paid" name="annual_fee" value="paid" class="board-radio-input" @checked(($filters['annual_fee'] ?? '') === 'paid')>
                                    <label for="af_paid">완납</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="af_unpaid" name="annual_fee" value="unpaid" class="board-radio-input" @checked(($filters['annual_fee'] ?? '') === 'unpaid')>
                                    <label for="af_unpaid">미납</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-member-inline-row">
                        <div class="bo-member-inline-item">
                            <span class="bo-member-inline-label">검색조건</span>
                            <div class="board-radio-group bo-member-inline-group">
                                <div class="board-radio-item">
                                    <input type="radio" id="sc_join" name="search_condition" value="joinDate" class="board-radio-input" @checked(($filters['search_condition'] ?? 'joinDate') === 'joinDate')>
                                    <label for="sc_join">가입일</label>
                                </div>
                                <div class="board-radio-item">
                                    <input type="radio" id="sc_login" name="search_condition" value="lastLogin" class="board-radio-input" @checked(($filters['search_condition'] ?? '') === 'lastLogin')>
                                    <label for="sc_login">접속일</label>
                                </div>
                            </div>
                        </div>
                        <div class="bo-member-inline-item bo-member-inline-item--wide">
                            <span class="bo-member-inline-label">검색기간</span>
                            <div class="bo-member-inline-group">
                                <input type="date" id="date_start" name="date_start" class="filter-input bo-member-date-input" value="{{ $filters['date_start'] ?? $filters['join_date_start'] ?? '' }}">
                                <span class="bo-member-date-sep">~</span>
                                <input type="date" id="date_end" name="date_end" class="filter-input bo-member-date-input" value="{{ $filters['date_end'] ?? $filters['join_date_end'] ?? '' }}">
                                <div class="filter-buttons bo-member-date-presets">
                                    <button type="button" class="btn btn-outline-secondary btn-sm bo-date-preset" data-preset="all">전체</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm bo-date-preset" data-preset="today">오늘</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm bo-date-preset" data-preset="yesterday">어제</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm bo-date-preset" data-preset="week">1주일</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm bo-date-preset" data-preset="month">이달</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm bo-date-preset" data-preset="year">올해</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bo-member-inline-row">
                        <div class="bo-member-inline-item">
                            <span class="bo-member-inline-label">검색어</span>
                            <div class="bo-member-inline-group">
                                <select id="search_field" name="search_field" class="filter-select bo-member-search-field">
                                    <option value="">선택</option>
                                    <option value="name" @selected(($filters['search_field'] ?? '') === 'name')>회원 이름</option>
                                    <option value="id" @selected(($filters['search_field'] ?? '') === 'id')>아이디</option>
                                    <option value="email" @selected(($filters['search_field'] ?? '') === 'email')>이메일</option>
                                    <option value="mobile" @selected(($filters['search_field'] ?? '') === 'mobile')>핸드폰</option>
                                    <option value="address" @selected(($filters['search_field'] ?? '') === 'address')>주소</option>
                                    <option value="licenseNo" @selected(($filters['search_field'] ?? '') === 'licenseNo')>의사면허번호</option>
                                    <option value="specialistNo" @selected(($filters['search_field'] ?? '') === 'specialistNo')>전문의번호</option>
                                    <option value="specialty" @selected(($filters['search_field'] ?? '') === 'specialty')>전문과</option>
                                    <option value="workplace" @selected(($filters['search_field'] ?? '') === 'workplace')>직장명</option>
                                    <option value="university" @selected(($filters['search_field'] ?? '') === 'university')>출신대학</option>
                                    <option value="graduateYear" @selected(($filters['search_field'] ?? '') === 'graduateYear')>졸업년도</option>
                                </select>
                                <input type="text" id="search_keyword" name="search_keyword" class="filter-input bo-member-search-input" value="{{ $filters['search_keyword'] ?? '' }}" placeholder="검색어를 입력하세요.">
                            </div>
                        </div>
                        <div class="bo-member-inline-item">
                            <div class="filter-buttons bo-member-submit-buttons">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> 검색
                                </button>
                                <a href="{{ route('backoffice.members.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> 초기화
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="board-list-header">
                <div class="list-info">
                    <span class="list-count">Total : {{ $members->total() }}</span>
                </div>
                <div class="list-controls">
                    <form method="GET" action="{{ route('backoffice.members.index') }}" class="per-page-form" id="boMembersPerPageForm">
                        <input type="hidden" name="sort_order" value="{{ $filters['sort_order'] ?? 'joinDate' }}">
                        <input type="hidden" name="search_condition" value="{{ $filters['search_condition'] ?? 'joinDate' }}">
                        <input type="hidden" name="date_start" value="{{ $filters['date_start'] ?? '' }}">
                        <input type="hidden" name="date_end" value="{{ $filters['date_end'] ?? '' }}">
                        <input type="hidden" name="is_certified" value="{{ $filters['is_certified'] ?? 'all' }}">
                        <input type="hidden" name="inactive_only" value="{{ ! empty($filters['inactive_only']) ? '1' : '0' }}">
                        <input type="hidden" name="due_mode" value="{{ $filters['due_mode'] ?? 'all' }}">
                        <input type="hidden" name="due_date" value="{{ $filters['due_date'] ?? '' }}">
                        <input type="hidden" name="annual_fee" value="{{ $filters['annual_fee'] ?? 'all' }}">
                        <input type="hidden" name="executive_status" value="{{ $filters['executive_status'] ?? 'all' }}">
                        <input type="hidden" name="search_field" value="{{ $filters['search_field'] ?? '' }}">
                        <input type="hidden" name="search_keyword" value="{{ $filters['search_keyword'] ?? '' }}">
                        @if (is_array($filters['grades'] ?? null))
                            @foreach ($filters['grades'] as $g)
                                <input type="hidden" name="grades[]" value="{{ $g }}">
                            @endforeach
                        @endif
                        <label for="perPageSelect" class="per-page-label">표시 개수:</label>
                        <select name="per_page" id="perPageSelect" class="per-page-select">
                            <option value="20" @selected($perPage == 20)>20개</option>
                            <option value="50" @selected($perPage == 50)>50개</option>
                            <option value="100" @selected($perPage == 100)>100개</option>
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
                            <th>등급</th>
                            <th>구분</th>
                            <th>인정의</th>
                            <th>아이디</th>
                            <th>이름</th>
                            <th>이메일</th>
                            <th>연락처</th>
                            <th>소속 위원회</th>
                            <th>가입일</th>
                            <th>최종 로그인</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $index => $member)
                            <tr data-id="{{ $member->id }}">
                                <td>
                                    <input type="checkbox" name="selected_members[]" value="{{ $member->id }}" class="form-check-input bo-row-checkbox">
                                </td>
                                <td>{{ $memberLevelLabels[$member->member_level] ?? '-' }}</td>
                                <td>{{ $member->job_type ? ($jobTypeLabels[$member->job_type] ?? $member->job_type) : '-' }}</td>
                                <td>{{ $member->certified_instructor ? '인정의' : '-' }}</td>
                                <td>{{ $member->login_id ?? '-' }}</td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->email ?? '-' }}</td>
                                <td>{{ $member->phone_number }}</td>
                                <td>
                                    @php
                                        $cc = $member->committee_codes ?? [];
                                        if (! is_array($cc)) {
                                            $cc = [];
                                        }
                                        $cn = [];
                                        foreach ($cc as $c) {
                                            $cn[] = $committeeLabels[$c] ?? $c;
                                        }
                                    @endphp
                                    {{ $cn !== [] ? implode(', ', $cn) : '-' }}
                                </td>
                                <td>{{ $member->created_at->format('Y.m.d') }}</td>
                                <td>{{ $member->last_login_at ? $member->last_login_at->format('Y.m.d H:i') : '-' }}</td>
                                <td>
                                    <div class="board-btn-group">
                                        <a href="{{ route('backoffice.members.edit', $member->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> 수정
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-member" data-id="{{ $member->id }}">
                                            <i class="fas fa-trash"></i> 삭제
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">등록된 회원이 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$members" />
        </div>
    </div>
</div>
@endsection

@extends('backoffice.layouts.app')

@section('title', '수강 신청 내역')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif

    <div class="board-container" id="bo-edu-course-enrollments-index" data-bulk-destroy-url="{{ route('backoffice.edu-course-enrollments.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger"><i class="fas fa-trash"></i> 선택 삭제</button>
                <a href="{{ route('backoffice.edu-course-enrollments.export', request()->query()) }}" class="btn btn-secondary"><i class="fas fa-file-download"></i> 엑셀 다운로드</a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.edu-course-enrollments.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">분류</label>
                                <select name="category" class="filter-select">
                                    <option value="">분류 전체</option>
                                    @foreach ($categoryLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('category') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">개설연도</label>
                                <select name="open_year" class="filter-select">
                                    <option value="">개설연도 전체</option>
                                    @foreach ($yearOptions as $year)
                                        <option value="{{ $year }}" @selected((string) request('open_year') === (string) $year)>{{ $year }}년</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">강좌</label>
                                <select name="edu_course_id" class="filter-select">
                                    <option value="">강좌 전체</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected((string) request('edu_course_id') === (string) $course->id)>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">상태</label>
                                <select name="enrollment_status" class="filter-select">
                                    <option value="">전체 상태</option>
                                    @foreach ($statusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('enrollment_status') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="filter-row mt-2">
                            <div class="filter-group">
                                <label class="filter-label">신청일</label>
                                <div class="bo-inline-form">
                                    <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                                    <span>~</span>
                                    <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">검색</label>
                                <select name="search_field" class="filter-select">
                                    <option value="all" @selected(request('search_field', 'all') === 'all')>전체</option>
                                    <option value="course" @selected(request('search_field') === 'course')>강좌명</option>
                                    <option value="name" @selected(request('search_field') === 'name')>수강자명</option>
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label">검색어</label>
                                <input type="text" name="search_keyword" class="filter-input" value="{{ request('search_keyword') }}" placeholder="검색어를 입력하세요.">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">&nbsp;</label>
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.edu-course-enrollments.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info"><span class="list-count">Total : {{ $enrollments->total() }}</span></div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.edu-course-enrollments.index') }}" id="edu-course-enrollment-per-page-form" class="per-page-form">
                            @foreach (request()->except('per_page', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <label class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-edu-course-enrollment-per-page">
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
                                <th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                <th class="w8">번호</th>
                                <th class="w12">분류</th>
                                <th class="w8">개설연도</th>
                                <th>강의명</th>
                                <th class="w10">수강자명</th>
                                <th class="w10">신청일</th>
                                <th class="w10">수강만료일</th>
                                <th class="w8">진도율</th>
                                <th class="w10">상태</th>
                                <th class="w10">수료증</th>
                                <th class="w12">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($enrollments as $enrollment)
                                <tr>
                                    <td><input type="checkbox" value="{{ $enrollment->id }}" class="form-check-input bo-edu-course-enrollment-checkbox bo-row-checkbox"></td>
                                    <td>{{ $enrollments->total() - (($enrollments->currentPage() - 1) * $enrollments->perPage()) - $loop->index }}</td>
                                    <td>{{ $categoryLabels[$enrollment->course->course_type ?? ''] ?? '-' }}</td>
                                    <td>{{ $enrollment->course->open_year ?? '-' }}</td>
                                    <td>{{ $enrollment->course->title ?? '-' }}</td>
                                    <td>{{ $enrollment->member_name }}</td>
                                    <td>{{ optional($enrollment->applied_at)->format('Y.m.d') ?? '-' }}</td>
                                    <td>{{ optional($enrollment->expire_at)->format('Y.m.d') ?? '—' }}</td>
                                    <td>{{ $enrollment->progress_rate }}%</td>
                                    <td>
                                        @if ($enrollment->enrollment_status === 'payment_pending')
                                            <span class="text-warning font-weight-bold">{{ $statusLabels[$enrollment->enrollment_status] ?? $enrollment->enrollment_status }}</span>
                                        @elseif ($enrollment->enrollment_status === 'in_progress' || $enrollment->enrollment_status === 'completed')
                                            <span class="text-primary font-weight-bold">{{ $statusLabels[$enrollment->enrollment_status] ?? $enrollment->enrollment_status }}</span>
                                        @else
                                            <span class="text-secondary font-weight-bold">{{ $statusLabels[$enrollment->enrollment_status] ?? $enrollment->enrollment_status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($enrollment->certificate_status === 'issued')
                                            <span class="text-success font-weight-bold">{{ $certificateLabels[$enrollment->certificate_status] ?? '발급완료' }}</span>
                                        @else
                                            <span class="text-muted font-weight-bold">{{ $certificateLabels[$enrollment->certificate_status] ?? '미발급' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('backoffice.edu-course-enrollments.show', ['edu_course_enrollment' => $enrollment, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 상세보기</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-center">등록된 수강 신청 내역이 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$enrollments" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/edu-course-enrollments-index.js') }}"></script>
@endsection


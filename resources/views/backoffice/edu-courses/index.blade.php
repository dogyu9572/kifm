@extends('backoffice.layouts.app')

@section('title', '강좌 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif

    <div class="board-container" id="bo-edu-courses-index" data-bulk-destroy-url="{{ route('backoffice.edu-courses.bulk-destroy') }}">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger"><i class="fas fa-trash"></i> 선택 삭제</button>
                <a href="{{ route('backoffice.edu-courses.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> 강좌 등록</a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.edu-courses.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">과정 유형</label>
                                <select name="course_type" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($courseTypeLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('course_type') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">개설연도</label>
                                <select name="open_year" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($yearOptions as $year)
                                        <option value="{{ $year }}" @selected((string) request('open_year') === (string) $year)>{{ $year }}년</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">사용여부</label>
                                <select name="use_yn" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="Y" @selected(request('use_yn') === 'Y')>사용</option>
                                    <option value="N" @selected(request('use_yn') === 'N')>미사용</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">검색</label>
                                <select name="search_field" class="filter-select">
                                    @foreach ($searchFieldLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('search_field', 'all') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label">검색어</label>
                                <input type="text" name="search_keyword" class="filter-input" value="{{ request('search_keyword') }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.edu-courses.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info"><span class="list-count">Total : {{ $courses->total() }}</span></div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.edu-courses.index') }}" id="edu-course-per-page-form" class="per-page-form">
                            <input type="hidden" name="course_type" value="{{ request('course_type') }}">
                            <input type="hidden" name="open_year" value="{{ request('open_year') }}">
                            <input type="hidden" name="use_yn" value="{{ request('use_yn') }}">
                            <input type="hidden" name="search_field" value="{{ request('search_field', 'all') }}">
                            <input type="hidden" name="search_keyword" value="{{ request('search_keyword') }}">
                            <label class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select bo-edu-course-per-page">
                                <option value="10" @selected($perPage === 10)>10개</option>
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
                                <th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                <th class="w8">번호</th>
                                <th class="w12">분류</th>
                                <th class="w8">개설연도</th>
                                <th>강의명</th>
                                <th class="w10">교수명</th>
                                <th class="w8">수강기간</th>
                                <th class="w10">상단 노출여부</th>
                                <th class="w8">사용여부</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($courses as $course)
                                <tr>
                                    <td><input type="checkbox" value="{{ $course->id }}" class="form-check-input bo-edu-course-checkbox bo-row-checkbox"></td>
                                    <td>{{ $courses->total() - (($courses->currentPage() - 1) * $courses->perPage()) - $loop->index }}</td>
                                    <td>{{ $courseTypeLabels[$course->course_type] ?? $course->course_type }}</td>
                                    <td>{{ $course->open_year }}년</td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->professorMember->name ?? $course->professor_name ?? '-' }}</td>
                                    <td>{{ $course->duration_days ? $course->duration_days . '일' : '-' }}</td>
                                    <td>{{ $course->expose_yn === 'Y' ? '노출' : '미노출' }}</td>
                                    <td>{{ $course->use_yn === 'Y' ? '사용' : '미사용' }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.edu-courses.edit', ['edu_course' => $course, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 수정</a>
                                            <form action="{{ route('backoffice.edu-courses.destroy', $course) }}" method="POST" class="d-inline js-delete-confirm-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> 삭제</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center">등록된 강좌가 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$courses" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/edu-courses-index.js') }}"></script>
@endsection


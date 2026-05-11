@extends('backoffice.layouts.app')

@section('title', '위원회 목록')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif

    <div class="board-container" id="bo-community-committees-index">
        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.community-committees.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">위원회명</label>
                                <input type="text" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="위원회명을 입력하세요">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.community-committees.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-page-header mb-2">
                    <div class="board-page-buttons">
                        <a href="{{ route('backoffice.community-committee-applicants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> 전체 신청 현황 조회
                        </a>
                        <a href="{{ route('backoffice.community-committees.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> 위원회 등록
                        </a>
                    </div>
                </div>

                <div class="board-list-header">
                    <div class="list-info"><span class="list-count">Total : {{ $committees->total() }}</span></div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.community-committees.index') }}" id="committee-per-page-form" class="per-page-form">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label class="per-page-label">표시 개수:</label>
                            <select name="per_page" class="per-page-select js-committee-per-page">
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
                                <th class="w10">썸네일</th>
                                <th class="w15">위원회 유형</th>
                                <th>위원회명</th>
                                <th class="w12">신청 현황</th>
                                <th class="w12">소속인원/정원</th>
                                <th class="w10">노출여부</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($committees as $committee)
                                <tr>
                                    <td>                                     
                                            <img src="{{ asset('storage/' . $committee->thumbnail_path) }}" alt="썸네일" width="60" height="40">
                                       
                                    </td>
                                    <td>{{ $typeLabels[$committee->committee_type] ?? $committee->committee_type }}</td>
                                    <td>{{ $committee->name }}</td>
                                    <td>
                                        @if ($committee->committee_type === 'special')
                                            -
                                        @else
                                            <a href="{{ route('backoffice.community-committee-applicants.index', ['committee_id' => $committee->id]) }}">{{ $committee->applications_live_count ?? 0 }}건</a>
                                        @endif
                                    </td>
                                    <td>{{ $committee->members_live_count ?? 0 }} / {{ $committee->member_limit ?: '-' }}</td>
                                    <td>{{ $visibilityLabels[$committee->visibility_yn] ?? $committee->visibility_yn }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.community-committees.edit', ['communityCommittee' => $committee, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 수정</a>
                                            <form action="{{ route('backoffice.community-committees.destroy', $committee) }}" method="POST" class="d-inline js-delete-committee-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> 삭제</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">등록된 위원회가 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$committees" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/community-committees-index.js') }}"></script>
@endsection


@extends('backoffice.layouts.app')

@section('title', '위원회 신청 현황')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/member-selector-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @php
        $statusValue = strtoupper((string) request('status', ''));
        $statusTextClasses = [
            'PENDING' => 'text-warning font-weight-bold',
            'APPROVED' => 'text-primary font-weight-bold',
            'REJECTED' => 'text-danger font-weight-bold',
        ];
        $returnQuery = request()->getQueryString();
    @endphp
    <div class="board-container">
        @if (session('success'))
            <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger board-hidden-alert">{{ session('error') }}</div>
        @endif

        <div class="board-header">
            <a href="{{ route('backoffice.community-committees.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 위원회 목록</a>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                @if ($selectedCommittee)
                    <div class="board-list-header mb-2">
                        <div class="list-info">
                            <span class="list-count">{{ $selectedCommittee->name }}</span>
                        </div>
                    </div>
                @endif

                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.community-committee-applicants.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label">위원회명</label>
                                <select name="committee_id" class="filter-select">
                                    <option value="">전체 위원회</option>
                                    @foreach ($committees as $committee)
                                        <option value="{{ $committee->id }}" @selected((string) request('committee_id') === (string) $committee->id)>{{ $committee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">신청 상태</label>
                                <select name="status" class="filter-select">
                                    <option value="">전체 상태</option>
                                    <option value="PENDING" @selected($statusValue === 'PENDING')>대기</option>
                                    <option value="APPROVED" @selected($statusValue === 'APPROVED')>승인</option>
                                    <option value="REJECTED" @selected($statusValue === 'REJECTED')>반려</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">검색어</label>
                                <input type="text" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="신청자명, 이메일, 연락처">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 조회</button>
                                    <a href="{{ route('backoffice.community-committee-applicants.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-page-header mb-2">
                    <div class="board-page-buttons">
                        <a href="{{ route('backoffice.community-committee-applicants.export', request()->query()) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i> 엑셀 다운로드
                        </a>
                    </div>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $applications->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.community-committee-applicants.index') }}" class="per-page-form">
                            <input type="hidden" name="committee_id" value="{{ request('committee_id') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <label class="per-page-label">표시 개수:</label>
                            <select name="per_page" class="per-page-select" onchange="this.form.submit()">
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
                                <th class="w8">No</th>
                                <th>위원회명</th>
                                <th class="w12">신청자</th>
                                <th>이메일</th>
                                <th class="w15">연락처</th>
                                <th class="w12">신청일</th>
                                <th class="w10">상태</th>
                                <th class="w12">승인날짜</th>
                                <th class="w20">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $index => $application)
                                @php
                                    $status = $application->status ?? 'PENDING';
                                    $statusLabel = $statusLabels[$status] ?? $status;
                                    $statusTextClass = $statusTextClasses[$status] ?? 'text-secondary font-weight-bold';
                                    $rowNumber = $applications->total() - (($applications->currentPage() - 1) * $applications->perPage()) - $index;
                                @endphp
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ $application->committee->name ?? '-' }}</td>
                                    <td>{{ $application->applicant_name ?? '-' }}</td>
                                    <td>{{ $application->email ?? '-' }}</td>
                                    <td>{{ $application->phone ?? '-' }}</td>
                                    <td>{{ optional($application->applied_at)->format('Y-m-d') ?? '-' }}</td>
                                    <td><span class="{{ $statusTextClass }}">{{ $statusLabel }}</span></td>
                                    <td>
                                        @if ($status === 'APPROVED')
                                            {{ optional($application->processed_at)->format('Y-m-d') ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            @if ($status === 'PENDING')
                                                <form method="POST" action="{{ route('backoffice.community-committee-applicants.approve', ['application' => $application->id]) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="return_query" value="{{ $returnQuery }}">
                                                    <button type="submit" class="btn btn-primary btn-sm js-approve-button">승인</button>
                                                </form>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm js-open-reject-modal"
                                                    data-action-type="reject"
                                                    data-action-url="{{ route('backoffice.community-committee-applicants.reject', ['application' => $application->id]) }}"
                                                    data-applicant-name="{{ $application->applicant_name }}"
                                                >반려</button>
                                            @elseif ($status === 'APPROVED')
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm js-open-reject-modal"
                                                    data-action-type="cancel"
                                                    data-action-url="{{ route('backoffice.community-committee-applicants.cancel-approval', ['application' => $application->id]) }}"
                                                    data-applicant-name="{{ $application->applicant_name }}"
                                                >승인 취소</button>
                                            @else
                                                <button
                                                    type="button"
                                                    class="btn btn-secondary btn-sm js-show-reason"
                                                    data-reason="{{ $application->reject_reason }}"
                                                >사유 확인</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">신청 내역이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$applications" />
            </div>
        </div>
    </div>

    <div class="modal js-reject-modal bo-member-search-modal">
        <div class="modal-content bo-member-search-modal-content">
            <div class="modal-header">
                <h5 class="modal-title js-reject-modal-title">반려 사유 입력</h5>
                <button type="button" class="close js-close-reject-modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" class="js-reject-form">
                @csrf
                <input type="hidden" name="return_query" value="{{ $returnQuery }}">
                <div class="modal-body">
                    <div class="board-form-group">
                        <label class="board-form-label">대상자</label>
                        <input type="text" class="board-form-control js-reject-target" readonly>
                    </div>
                    <div class="board-form-group mb-0">
                        <label class="board-form-label">사유 <span class="required">*</span></label>
                        <textarea name="reject_reason" class="board-form-control board-form-textarea js-reject-reason" rows="4" placeholder="사유를 입력해주세요."></textarea>
                        <small class="board-form-text">* 입력한 사유는 신청자 안내 문구로 사용됩니다.</small>
                    </div>
                </div>
                <div class="board-form-actions px-3 pb-3">
                    <button type="submit" class="btn btn-primary js-reject-submit">저장</button>
                    <button type="button" class="btn btn-secondary js-close-reject-modal">취소</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/community-committee-applicants.js') }}"></script>
@endsection


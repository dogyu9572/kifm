@extends('backoffice.layouts.app')

@section('title', '메일 발송 내역')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ route('backoffice.mails.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 신규 발송
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.mails.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="status" class="filter-label">전송 상태</label>
                                <select name="status" id="status" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach (['DRAFT' => '작성중', 'SCHEDULED' => '전송예약', 'SENT' => '전송완료', 'FAILED' => '발송실패'] as $key => $label)
                                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="date_type" class="filter-label">기간</label>
                                <select name="date_type" id="date_type" class="filter-select">
                                    <option value="created_at" @selected(request('date_type', 'created_at') === 'created_at')>작성일</option>
                                    <option value="sent_at" @selected(request('date_type') === 'sent_at')>발송일</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <input type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
                            </div>
                            <div class="filter-group">
                                <input type="date" name="end_date" class="filter-input" value="{{ request('end_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="search_field" class="filter-label">검색어</label>
                                <select name="search_field" id="search_field" class="filter-select">
                                    <option value="title" @selected(request('search_field', 'title') === 'title')>제목</option>
                                    <option value="writer" @selected(request('search_field') === 'writer')>작성자</option>
                                    <option value="body" @selected(request('search_field') === 'body')>메일 본문</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <input type="text" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="검색어를 입력하세요">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.mails.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w8">번호</th>
                                <th class="w10">상태</th>
                                <th>제목</th>
                                <th class="w10">대상 수</th>
                                <th class="w12">작성일</th>
                                <th class="w15">발송일</th>
                                <th class="w20">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mails as $mail)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $mail->id }}" class="form-check-input bo-row-checkbox">
                                    </td>
                                    <td>{{ $mails->total() - (($mails->currentPage() - 1) * $mails->perPage()) - $loop->index }}</td>
                                    <td>{{ $mail->status }}</td>
                                    <td>{{ $mail->subject ?: '-' }}</td>
                                    <td>{{ number_format($mail->recipient_count) }}</td>
                                    <td>{{ optional($mail->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ optional($mail->sent_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.mails.edit', $mail) }}" class="btn btn-primary btn-sm">수정</a>
                                            <form action="{{ route('backoffice.mails.copy', $mail) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-secondary btn-sm">복사</button></form>
                                            <form action="{{ route('backoffice.mails.destroy', $mail) }}" method="POST" class="d-inline js-delete-confirm-form">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">삭제</button></form>
                                            @if ($mail->status === 'SCHEDULED')
                                                <form action="{{ route('backoffice.mails.cancel-schedule', $mail) }}" method="POST" class="d-inline js-cancel-schedule-form">@csrf<button type="submit" class="btn btn-warning btn-sm">예약취소</button></form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">발송 내역이 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$mails" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/mail.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-table-select-all.js') }}"></script>
@endsection

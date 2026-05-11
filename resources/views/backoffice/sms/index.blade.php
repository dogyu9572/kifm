@extends('backoffice.layouts.app')

@section('title', '문자 발송 내역')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ route('backoffice.sms.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 새 문자 작성
                </a>
            </div>
        </div>
        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.sms.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="status" class="filter-label">발송 상태</label>
                                <select id="status" name="status" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach (['DRAFT' => '작성중', 'RESERVED' => '전송예약', 'DONE' => '발송 완료', 'FAILED' => '실패'] as $key => $label)
                                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group"><input type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}"></div>
                            <div class="filter-group"><input type="date" name="end_date" class="filter-input" value="{{ request('end_date') }}"></div>
                            <div class="filter-group"><input type="text" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="검색어"></div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.sms.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w10">상태</th>
                                <th>제목</th>
                                <th class="w10">대상 수</th>
                                <th class="w12">작성일</th>
                                <th class="w15">발송일</th>
                                <th class="w20">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($smsMessages as $sms)
                                <tr>
                                    <td>{{ $sms->status }}</td>
                                    <td>{{ $sms->subject ?: \Illuminate\Support\Str::limit((string) $sms->body, 40) }}</td>
                                    <td>{{ number_format($sms->recipient_count) }}</td>
                                    <td>{{ optional($sms->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ optional($sms->sent_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.sms.edit', $sms) }}" class="btn btn-primary btn-sm">수정</a>
                                            <form action="{{ route('backoffice.sms.copy', $sms) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-secondary btn-sm">복사</button></form>
                                            <form action="{{ route('backoffice.sms.destroy', $sms) }}" method="POST" class="d-inline js-delete-confirm-form">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">삭제</button></form>
                                            @if ($sms->status === 'RESERVED')
                                                <form action="{{ route('backoffice.sms.cancel-schedule', $sms) }}" method="POST" class="d-inline js-cancel-schedule-form">@csrf<button type="submit" class="btn btn-warning btn-sm">예약취소</button></form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">발송 이력이 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$smsMessages" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/sms.js') }}"></script>
@endsection

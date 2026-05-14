@extends('backoffice.layouts.app')

@section('title', '주소록 관리')

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
                <a href="{{ route('backoffice.address-books.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 등록
                </a>
            </div>
        </div>
        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.address-books.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group filter-group-large">
                                <input type="text" name="keyword" class="filter-input" value="{{ request('keyword') }}" placeholder="주소록명 검색">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                    <a href="{{ route('backoffice.address-books.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
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
                                <th>주소록명</th>
                                <th class="w12">등록 회원 수</th>
                                <th class="w12">등록일</th>
                                <th class="w12">최종 수정일</th>
                                <th class="w20">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($addressBooks as $addressBook)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $addressBook->id }}" class="form-check-input bo-row-checkbox">
                                    </td>
                                    <td>{{ $addressBooks->total() - (($addressBooks->currentPage() - 1) * $addressBooks->perPage()) - $loop->index }}</td>
                                    <td>{{ $addressBook->name }}</td>
                                    <td>{{ number_format($addressBook->member_count) }}명</td>
                                    <td>{{ optional($addressBook->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ optional($addressBook->updated_at)->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.address-books.edit', $addressBook) }}" class="btn btn-primary btn-sm">수정</a>
                                            <form action="{{ route('backoffice.address-books.destroy', $addressBook) }}" method="POST" class="d-inline js-delete-confirm-form">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">삭제</button></form>
                                            <a href="{{ route('backoffice.mails.create') }}?recipient_type=addressbook&address_book_id={{ $addressBook->id }}" class="btn btn-secondary btn-sm">이메일 발송</a>
                                            <a href="{{ route('backoffice.sms.create') }}?recipient_type=addressbook&address_book_id={{ $addressBook->id }}" class="btn btn-secondary btn-sm">SMS 발송</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">등록된 주소록이 없습니다.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$addressBooks" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/address-books.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-table-select-all.js') }}"></script>
@endsection

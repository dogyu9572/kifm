@extends('backoffice.layouts.app')

@section('title', '진료 과목 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger board-hidden-alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="board-container" id="bo-doctor-categories-index">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ route('backoffice.doctor-categories.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 과목 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $categories->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.doctor-categories.index') }}" class="per-page-form" id="bo-doctor-categories-per-page-form">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select">
                                <option value="10" @selected($perPage === 10)>10개</option>
                                <option value="20" @selected($perPage === 20)>20개</option>
                                <option value="50" @selected($perPage === 50)>50개</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w8">번호</th>
                                <th>과목명</th>
                                <th class="w10">정렬</th>
                                <th class="w10">상태</th>
                                <th class="w12">등록일</th>
                                <th class="w14">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $cat)
                                <tr>
                                    <td>{{ $categories->total() - (($categories->currentPage() - 1) * $categories->perPage()) - $loop->index }}</td>
                                    <td>{{ $cat->name }}</td>
                                    <td>{{ $cat->sort_order }}</td>
                                    <td>
                                        @if ($cat->status === 'active')
                                            <span class="badge badge-success">{{ $statusLabels['active'] ?? '사용중' }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $statusLabels['inactive'] ?? '미사용' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $cat->created_at?->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.doctor-categories.edit', $cat) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.doctor-categories.destroy', $cat) }}" method="POST" class="d-inline js-delete-confirm-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> 삭제
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">등록된 진료 과목이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$categories" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/doctor-categories-index.js') }}"></script>
@endsection

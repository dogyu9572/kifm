@extends('backoffice.layouts.app')

@section('title', '주치의 목록')

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

    @php
        $filterSidoRaw = trim((string) request('sido', ''));
        $filterSido = $filterSidoRaw;
        if ($filterSidoRaw !== '') {
            $nf = \App\Services\Backoffice\LocalDoctorRegionNormalizer::normalizeSido($filterSidoRaw);
            $filterSido = $nf['sido'] !== '' ? $nf['sido'] : $filterSidoRaw;
        }
    @endphp

    <div
        class="board-container"
        id="bo-local-doctors-index"
        data-bulk-destroy-url="{{ route('backoffice.local-doctors.bulk-destroy') }}"
    >
        <textarea id="bo-local-doctors-sigungu-json" class="d-none" rows="1" cols="1" readonly tabindex="-1" autocomplete="off" aria-hidden="true">{{ json_encode($sigunguBySido, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}</textarea>
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled>
                    <i class="fas fa-trash"></i> 선택 삭제 (0)
                </button>
                <a href="{{ route('backoffice.local-doctors.create', ['return_url' => request()->getRequestUri()]) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 주치의 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.local-doctors.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label class="filter-label" for="filter_sido">시/도</label>
                                <select name="sido" id="filter_sido" class="filter-select">
                                    <option value="">전체 (시/도)</option>
                                    @foreach ($sidos as $sidoOption)
                                        <option value="{{ $sidoOption }}" @selected($filterSido === $sidoOption)>{{ $sidoOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_sigungu">시/군/구</label>
                                <select name="sigungu" id="filter_sigungu" class="filter-select">
                                    <option value="">전체 (시/군/구)</option>
                                    @php
                                        $sigunguList = $filterSido !== '' && isset($sigunguBySido[$filterSido])
                                            ? $sigunguBySido[$filterSido]
                                            : [];
                                        $filterSigungu = (string) request('sigungu', '');
                                    @endphp
                                    @foreach ($sigunguList as $sg)
                                        <option value="{{ $sg }}" @selected($filterSigungu === $sg)>{{ $sg }}</option>
                                    @endforeach
                                    @if ($filterSigungu !== '' && ! in_array($filterSigungu, $sigunguList, true))
                                        <option value="{{ $filterSigungu }}" selected>{{ $filterSigungu }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_doctor_category_id">진료과목</label>
                                <select name="doctor_category_id" id="filter_doctor_category_id" class="filter-select">
                                    <option value="">전체 진료과목</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected((string) request('doctor_category_id') === (string) $cat->id)>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group filter-group-grow">
                                <label class="filter-label" for="filter_keyword">검색어</label>
                                <input type="text" name="keyword" id="filter_keyword" class="filter-input"
                                    value="{{ request('keyword') }}" placeholder="병원명 또는 의사명">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label" for="filter_status">상태</label>
                                <select name="status" id="filter_status" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach ($statusLabels as $code => $label)
                                        <option value="{{ $code }}" @selected(request('status') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.local-doctors.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $doctors->total() }}</span>                    
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.local-doctors.index') }}" class="per-page-form" id="bo-local-doctors-per-page-form">
                            <input type="hidden" name="sido" value="{{ $filterSido }}">
                            <input type="hidden" name="sigungu" value="{{ $filterSigungu }}">
                            <input type="hidden" name="doctor_category_id" value="{{ request('doctor_category_id') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
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
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th class="w8">번호</th>
                                <th class="w10">시/도</th>
                                <th class="w10">시/군/구</th>
                                <th class="w12">진료과목</th>
                                <th class="w14">병원명</th>
                                <th>주소</th>
                                <th class="w12">연락처</th>
                                <th class="w10">의사명</th>
                                <th class="w8">상태</th>
                                <th class="w14">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($doctors as $doc)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $doc->id }}" class="form-check-input bo-local-doctor-checkbox bo-row-checkbox">
                                    </td>
                                    <td>{{ $doctors->total() - (($doctors->currentPage() - 1) * $doctors->perPage()) - $loop->index }}</td>
                                    <td>{{ $doc->sido ?: '-' }}</td>
                                    <td>{{ $doc->sigungu ?: '-' }}</td>
                                    <td>
                                        @if ($doc->doctorCategories->isEmpty())
                                            -
                                        @else
                                            {{ $doc->doctorCategories->pluck('name')->implode(', ') }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $doc->hospital_name }}
                                    </td>
                                    <td>{{ $doc->address ?: '-' }}</td>
                                    <td>{{ $doc->phone ?: '-' }}</td>
                                    <td>{{ $doc->doctor_name }}</td>
                                    <td>
                                        @if ($doc->status === 'active')
                                            <span class="badge badge-success">{{ $statusLabels['active'] ?? '운영중' }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $statusLabels['inactive'] ?? '미운영' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.local-doctors.edit', ['local_doctor' => $doc, 'return_url' => request()->getRequestUri()]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form action="{{ route('backoffice.local-doctors.destroy', $doc) }}" method="POST" class="d-inline js-delete-confirm-form">
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
                                    <td colspan="11" class="text-center">등록된 주치의가 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$doctors" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/local-doctors-index.js') }}"></script>
@endsection

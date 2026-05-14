@extends('backoffice.layouts.app')

@section('title', '회원 통계')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
@endsection

@section('content')
    <div class="board-container" id="bo-stats-members-index">
        <div class="board-page-header">
            <div class="board-page-buttons">
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.stats.members.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="date_from" class="filter-label">등록일 시작</label>
                                <input type="date" id="date_from" name="date_from" class="filter-input"
                                    value="{{ $dateFrom }}">
                            </div>
                            <div class="filter-group">
                                <label for="date_to" class="filter-label">등록일 끝</label>
                                <input type="date" id="date_to" name="date_to" class="filter-input"
                                    value="{{ $dateTo }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.stats.members.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bo-stats-grid">
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">전체 회원수</div>
                        <div class="bo-stat-value">{{ number_format($summary['totalMembers']) }}</div>
                        <div class="bo-stat-unit">명</div>
                    </div>
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">기간 내 신규 가입</div>
                        <div class="bo-stat-value bo-stat-value--success">
                            +{{ number_format($summary['periodJoin']) }}
                        </div>
                        <div class="bo-stat-unit">명</div>
                    </div>
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">기간 내 탈퇴</div>
                        <div class="bo-stat-value bo-stat-value--danger">
                            -{{ number_format($summary['periodLeave']) }}
                        </div>
                        <div class="bo-stat-unit">명</div>
                    </div>
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">유효 회원</div>
                        <div class="bo-stat-value">{{ number_format($summary['activeMembers']) }}</div>
                        <div class="bo-stat-unit">명</div>
                    </div>
                </div>

                <div class="bo-stats-tables">
                    <div class="bo-stats-section">
                        <h3 class="bo-stats-section-title">월별 신규 가입 현황</h3>
                        <div class="table-responsive">
                            <table class="bo-stats-table">
                                <thead>
                                    <tr>
                                        <th>월</th>
                                        <th class="text-right">신규 가입</th>
                                        <th class="text-right">탈퇴</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthly['rows'] as $row)
                                        <tr>
                                            <td>{{ $row['yearMonthLabel'] }}</td>
                                            <td class="text-right">{{ number_format($row['joinCount']) }}</td>
                                            <td class="text-right">{{ number_format($row['leaveCount']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">선택한 기간에 데이터가 없습니다.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>합계</td>
                                        <td class="text-right">{{ number_format($monthly['totalJoin']) }}</td>
                                        <td class="text-right">{{ number_format($monthly['totalLeave']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="bo-stats-section">
                        <h3 class="bo-stats-section-title">회원 등급별 분포</h3>
                        <div class="table-responsive">
                            <table class="bo-stats-table">
                                <thead>
                                    <tr>
                                        <th>등급</th>
                                        <th class="text-right">인원</th>
                                        <th class="text-right">비율</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradeDistribution['rows'] as $row)
                                        <tr>
                                            <td>{{ $row['gradeLabel'] }}</td>
                                            <td class="text-right">{{ number_format($row['count']) }}</td>
                                            <td class="text-right">
                                                <span class="bo-stats-badge {{ $row['gradeCode'] === 'regular' ? 'bo-stats-badge--solid' : 'bo-stats-badge--outline' }}">
                                                    {{ number_format($row['ratio'], 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/stats-members.js') }}"></script>
@endsection

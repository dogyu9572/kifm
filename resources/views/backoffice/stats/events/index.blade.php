@extends('backoffice.layouts.app')

@section('title', '행사 통계')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
@endsection

@section('content')
    <div class="board-container" id="bo-stats-events-index">
        <div class="board-page-header">
            <div class="board-page-buttons">
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.stats.events.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="date_from" class="filter-label">신청일 시작</label>
                                <input type="date" id="date_from" name="date_from" class="filter-input"
                                    value="{{ $dateFrom }}">
                            </div>
                            <div class="filter-group">
                                <label for="date_to" class="filter-label">신청일 끝</label>
                                <input type="date" id="date_to" name="date_to" class="filter-input"
                                    value="{{ $dateTo }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.stats.events.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bo-stats-grid bo-stats-grid--3">
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">기간 내 행사 수</div>
                        <div class="bo-stat-measure">
                            <span class="bo-stat-value">{{ number_format($summary['eventCount']) }}</span>
                            <span class="bo-stat-unit">건</span>
                        </div>
                    </div>
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">총 참가자</div>
                        <div class="bo-stat-measure">
                            <span class="bo-stat-value bo-stat-value--success">{{ number_format($summary['totalParticipants']) }}</span>
                            <span class="bo-stat-unit">명</span>
                        </div>
                    </div>
                    <div class="bo-stat-card">
                        <div class="bo-stat-label">총 결제 금액</div>
                        <div class="bo-stat-measure">
                            <span class="bo-stat-value">{{ number_format($summary['totalAmount']) }}</span>
                            <span class="bo-stat-unit">원</span>
                        </div>
                    </div>
                </div>

                <div class="bo-stats-section">
                    <h3 class="bo-stats-section-title">행사별 참가 현황</h3>
                    <div class="table-responsive">
                        <table class="bo-stats-table">
                            <thead>
                                <tr>
                                    <th>행사명</th>
                                    <th>행사일</th>
                                    <th class="text-right">신청</th>
                                    <th class="text-right">결제 완료</th>
                                    <th class="text-right">결제금액</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byEvent['rows'] as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ $row['eventDate'] ?? '-' }}</td>
                                        <td class="text-right">{{ number_format($row['applied']) }}</td>
                                        <td class="text-right">{{ number_format($row['paid']) }}</td>
                                        <td class="text-right">{{ number_format($row['amount']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">선택한 기간에 신청 데이터가 있는 행사가 없습니다.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">합계</td>
                                    <td class="text-right">{{ number_format($byEvent['totals']['applied']) }}</td>
                                    <td class="text-right">{{ number_format($byEvent['totals']['paid']) }}</td>
                                    <td class="text-right">{{ number_format($byEvent['totals']['amount']) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/stats-events.js') }}"></script>
@endsection

@extends('backoffice.layouts.app')

@section('title', $board->name ?? '학술대회 연혁')

@section('content')
@php
    $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
    if (! is_array($customFields)) {
        $customFields = [];
    }
    $attachments = $post->attachments ? json_decode($post->attachments, true) : [];
    if (! is_array($attachments)) {
        $attachments = [];
    }
    $startDate = $customFields['event_start_date'] ?? '';
    $endDate = $customFields['event_end_date'] ?? '';
    $periodText = trim($startDate . (($startDate !== '' && $endDate !== '') ? ' ~ ' : '') . $endDate);
@endphp

<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'academic_conference_history') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
        <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'academic_conference_history', $post->id]) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> 수정
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <div class="board-post-detail">
                <div class="board-detail-row">
                    <div class="board-detail-label">행사 기간</div>
                    <div class="board-detail-value">{{ $periodText !== '' ? $periodText : '-' }}</div>
                </div>
                <div class="board-detail-row">
                    <div class="board-detail-label">행사명</div>
                    <div class="board-detail-value">{{ $post->title }}</div>
                </div>
                <div class="board-detail-row">
                    <div class="board-detail-label">공개여부</div>
                    <div class="board-detail-value">{{ (bool) ($post->is_active ?? true) ? '공개' : '비공개' }}</div>
                </div>
                <div class="board-detail-row">
                    <div class="board-detail-label">행사자료</div>
                    <div class="board-detail-value">
                        @forelse($attachments as $attachment)
                            @php
                                $attachmentName = $attachment['name'] ?? '';
                                $attachmentPath = $attachment['path'] ?? null;
                            @endphp
                            @if(is_string($attachmentPath) && $attachmentPath !== '')
                                <div class="board-attachment-item">
                                    <a href="{{ asset('storage/'.$attachmentPath) }}" download="{{ $attachmentName }}" target="_blank" rel="noopener" class="board-attachment-link">{{ $attachmentName }}</a>
                                </div>
                            @endif
                        @empty
                            -
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

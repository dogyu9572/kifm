{{-- 게시글 수정 화면 하단: 동적 board_slug + post_id 기준 댓글 (boards.enable_comments) --}}
<div class="board-container bo-comments-panel">
    <div class="board-card">
        <div class="board-card-body">
            <h3 class="bo-section-title">댓글</h3>

            @foreach($comments as $comment)
                <div class="bo-comment-thread">
                    <div class="bo-comment-item border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <strong>{{ $comment->author_name }}</strong>
                                <span class="text-muted small ms-2">{{ $comment->created_at->format('Y.m.d H:i') }}</span>
                            </div>
                            <div class="board-btn-group">
                                <button type="button" class="btn btn-outline-secondary btn-sm bo-comment-reply-toggle" data-bo-reply-target="bo-reply-form-{{ $comment->id }}">
                                    답글
                                </button>
                                <form action="{{ route('backoffice.board-posts.comments.destroy', [$board->slug, $post->id, $comment->id]) }}" method="POST" class="d-inline js-delete-confirm-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">삭제</button>
                                </form>
                            </div>
                        </div>
                        <div class="mt-2">{!! nl2br(e($comment->content)) !!}</div>

                        <form action="{{ route('backoffice.board-posts.comments.update', [$board->slug, $post->id, $comment->id]) }}" method="POST" class="mt-3">
                            @csrf
                            @method('PUT')
                            <label class="board-form-label small">댓글 수정</label>
                            <textarea name="content" class="board-form-control" rows="3" required>{{ $comment->content }}</textarea>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">수정 저장</button>
                        </form>

                        <div id="bo-reply-form-{{ $comment->id }}" class="bo-comment-reply-form d-none mt-3 p-3 bg-light rounded">
                            <form action="{{ route('backoffice.board-posts.comments.store', [$board->slug, $post->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <label class="board-form-label small">답글</label>
                                <textarea name="content" class="board-form-control" rows="3" required placeholder="답글 내용"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">답글 등록</button>
                            </form>
                        </div>
                    </div>

                    @foreach($comment->replies as $reply)
                        <div class="bo-comment-item bo-comment-reply border rounded p-3 mb-2 ms-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <strong>{{ $reply->author_name }}</strong>
                                    <span class="text-muted small ms-2">{{ $reply->created_at->format('Y.m.d H:i') }}</span>
                                </div>
                                <form action="{{ route('backoffice.board-posts.comments.destroy', [$board->slug, $post->id, $reply->id]) }}" method="POST" class="d-inline js-delete-confirm-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">삭제</button>
                                </form>
                            </div>
                            <div class="mt-2">{!! nl2br(e($reply->content)) !!}</div>
                            <form action="{{ route('backoffice.board-posts.comments.update', [$board->slug, $post->id, $reply->id]) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PUT')
                                <label class="board-form-label small">댓글 수정</label>
                                <textarea name="content" class="board-form-control" rows="2" required>{{ $reply->content }}</textarea>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">수정 저장</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="mt-4">
                <h4 class="h6">댓글 등록</h4>
                <form action="{{ route('backoffice.board-posts.comments.store', [$board->slug, $post->id]) }}" method="POST">
                    @csrf
                    <textarea name="content" class="board-form-control" rows="4" required placeholder="댓글 내용"></textarea>
                    <button type="submit" class="btn btn-primary mt-2">등록</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/backoffice/board-post-comments.js') }}"></script>
@endpush

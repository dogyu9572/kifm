@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
@php
    $currentUser = auth()->user();
    $currentUserId = (int) auth()->id();
    $currentUserCanManageAll = $currentUser && $currentUser->isAdmin();
    $postContent = trim(strip_tags((string) $post->content));
    $showPostContent = $postContent !== '' && $postContent !== trim((string) $post->title);
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="subcommittee-heading">
	<div class="inner">
		@include('subcommittee.partials.committee_header', ['useCommitteeH1' => false, 'showCommitteeTabs' => true])

		<div class="board_view chat_wrap">
			<div class="tit_area">
				<h1 class="tit" id="subcommittee-post-heading">{{ $post->title }}</h1>
				<div class="date">
					<strong class="tt">등록일</strong><p>{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d') }}</p>
					<strong class="tt">조회수</strong><p>{{ number_format((int) ($post->view_count ?? 0)) }}</p>
				</div>
				<x-frontend.bookmark-button content-type="community_committee_discussions" :content-id="$post->id" :title="$post->title" :menu-label="$dName" :url="route('subcommittee.discussion_show', [$committee, $post->id])" />
			</div>

			<div class="chat_area">
				@if ($showPostContent)
					<div class="chat {{ (int) ($post->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
						<div class="name">
							<strong>{{ (int) ($post->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $post->author_name ?: '회원' }}</strong>
							<div class="date">{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d H:i') }}</div>
						</div>
						<div class="txtbox">
							{!! nl2br(e($postContent)) !!}
						</div>
					</div>
				@endif

				@foreach ($comments as $comment)
					@php
						$canManageComment = $currentUserCanManageAll || (int) ($comment->user_id ?? 0) === $currentUserId;
					@endphp
					<div class="chat {{ (int) ($comment->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
						<div class="name">
							<strong>{{ (int) ($comment->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $comment->author_name ?: '회원' }}</strong>
							<div class="date">{{ $comment->created_at->format('Y.m.d H:i') }}</div>
							@if ($canManageComment)
							<div class="option_area">
								<button type="button" class="btn_option">수정/삭제 펼침</button>
								<ul>
									<li><button type="button" data-comment-edit-toggle data-comment-id="{{ $comment->id }}">수정</button></li>
									<li>
										<form method="POST" action="{{ route('subcommittee.discussion_comment_destroy', [$committee, $post->id, $comment->id]) }}" data-comment-delete-form>
											@csrf
											@method('DELETE')
											<button type="submit">삭제</button>
										</form>
									</li>
								</ul>
							</div>
							@endif
						</div>
						<div class="txtbox" data-comment-body="{{ $comment->id }}">
							{!! nl2br(e($comment->content)) !!}
							@include('subcommittee.partials.discussion_comment_attachments', ['attachments' => $comment->display_attachments ?? []])
						</div>
						@if ($canManageComment)
							<form method="POST" action="{{ route('subcommittee.discussion_comment_update', [$committee, $post->id, $comment->id]) }}" class="chat_edit_form" data-comment-edit-form data-comment-id="{{ $comment->id }}" hidden>
								@csrf
								@method('PUT')
								<div class="chat_input">
									<input type="text" name="content" class="text" value="{{ $comment->content }}" required>
									<button type="submit" class="btn btn_wkk">수정</button>
									<button type="button" class="btn btn_kwg" data-comment-edit-cancel data-comment-id="{{ $comment->id }}">취소</button>
								</div>
							</form>
						@endif
					</div>

					@foreach ($comment->replies as $reply)
						@php
							$canManageReply = $currentUserCanManageAll || (int) ($reply->user_id ?? 0) === $currentUserId;
						@endphp
						<div class="chat {{ (int) ($reply->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
							<div class="name">
								<strong>{{ (int) ($reply->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $reply->author_name ?: '회원' }}</strong>
								<div class="date">{{ $reply->created_at->format('Y.m.d H:i') }}</div>
								@if ($canManageReply)
								<div class="option_area">
									<button type="button" class="btn_option">수정/삭제 펼침</button>
									<ul>
										<li><button type="button" data-comment-edit-toggle data-comment-id="{{ $reply->id }}">수정</button></li>
										<li>
											<form method="POST" action="{{ route('subcommittee.discussion_comment_destroy', [$committee, $post->id, $reply->id]) }}" data-comment-delete-form>
												@csrf
												@method('DELETE')
												<button type="submit">삭제</button>
											</form>
										</li>
									</ul>
								</div>
								@endif
							</div>
							<div class="txtbox" data-comment-body="{{ $reply->id }}">
								{!! nl2br(e($reply->content)) !!}
								@include('subcommittee.partials.discussion_comment_attachments', ['attachments' => $reply->display_attachments ?? []])
							</div>
							@if ($canManageReply)
								<form method="POST" action="{{ route('subcommittee.discussion_comment_update', [$committee, $post->id, $reply->id]) }}" class="chat_edit_form" data-comment-edit-form data-comment-id="{{ $reply->id }}" hidden>
									@csrf
									@method('PUT')
									<div class="chat_input">
										<input type="text" name="content" class="text" value="{{ $reply->content }}" required>
										<button type="submit" class="btn btn_wkk">수정</button>
										<button type="button" class="btn btn_kwg" data-comment-edit-cancel data-comment-id="{{ $reply->id }}">취소</button>
									</div>
								</form>
							@endif
						</div>
					@endforeach
				@endforeach
			</div>
			<div class="chat_input_area">
					<form method="POST" action="{{ route('subcommittee.discussion_comment_store', [$committee, $post->id]) }}" enctype="multipart/form-data" data-subcommittee-discussion-comment-form data-validation-message="{{ isset($errors) ? $errors->first('content') : '' }}">
					@csrf
					<div class="attach_area">
						<div class="input_attach attach_file_box"><input type="file" id="attach_file" name="attach_file"><label for="attach_file"><strong>첨부파일</strong></label><p></p></div>
						<div class="input_attach attach_image_box"><input type="file" id="attach_image" name="attach_image" accept="image/*"><label for="attach_image"><strong>이미지</strong></label><p>권장 사이즈: 200x200</p></div>
					</div>
					<div class="chat_input">
						<input type="text" name="content" class="text" value="{{ old('content') }}" placeholder="답글 내용을 작성해주세요.">
						<button type="submit" class="btn btn_wkk">보내기</button>
					</div>
				</form>
			</div>
		</div>

		<div class="board_bottom">
			<a href="{{ route('subcommittee.discussion', $committee) }}" class="btn btn_wkk btn_list">목록</a>
		</div>

	</div>
</section>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/script_bookmark.js') }}"></script>
<script src="{{ asset('js/frontend/subcommittee-discussion.js') }}"></script>
@endpush

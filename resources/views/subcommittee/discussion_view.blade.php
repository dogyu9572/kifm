@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
@php
    $currentUserId = (int) auth()->id();
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
				<div class="chat {{ (int) ($post->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
					<div class="name">
						<strong>{{ (int) ($post->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $post->author_name ?: '회원' }}</strong>
						<div class="date">{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d H:i') }}</div>
					</div>
					<div class="txtbox">
						{!! nl2br(e(strip_tags((string) $post->content))) !!}
					</div>
				</div>

				@foreach ($comments as $comment)
					<div class="chat {{ (int) ($comment->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
						<div class="name">
							<strong>{{ (int) ($comment->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $comment->author_name ?: '회원' }}</strong>
							<div class="date">{{ $comment->created_at->format('Y.m.d H:i') }}</div>
						</div>
						<div class="txtbox">
							{!! nl2br(e($comment->content)) !!}
						</div>
					</div>

					@foreach ($comment->replies as $reply)
						<div class="chat {{ (int) ($reply->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
							<div class="name">
								<strong>{{ (int) ($reply->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $reply->author_name ?: '회원' }}</strong>
								<div class="date">{{ $reply->created_at->format('Y.m.d H:i') }}</div>
							</div>
							<div class="txtbox">
								{!! nl2br(e($reply->content)) !!}
							</div>
						</div>
					@endforeach
				@endforeach
			</div>
			<form method="POST" action="{{ route('subcommittee.discussion_comment_store', [$committee, $post->id]) }}" class="chat_input_area" data-subcommittee-discussion-comment-form data-validation-message="{{ $errors->first('content') }}">
				@csrf
				<div class="chat_input">
					<input type="text" name="content" class="text" value="{{ old('content') }}" placeholder="답글 내용을 작성해주세요.">
					<button type="submit" class="btn btn_wkk">보내기</button>
				</div>
			</form>
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

@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
@php
    $currentUserId = (int) auth()->id();
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
					<div class="chat {{ (int) ($comment->user_id ?? 0) === $currentUserId ? 'me' : 'you' }}">
						<div class="name">
							<strong>{{ (int) ($comment->user_id ?? 0) === $currentUserId ? '(나) ' : '' }}{{ $comment->author_name ?: '회원' }}</strong>
							<div class="date">{{ $comment->created_at->format('Y.m.d H:i') }}</div>
							<div class="option_area">
								<button type="button" class="btn_option">수정/삭제 펼침</button>
								<ul>
									<li><button type="button">수정</button></li>
									<li><button type="button">삭제</button></li>
								</ul>
							</div>
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
								<div class="option_area">
									<button type="button" class="btn_option">수정/삭제 펼침</button>
									<ul>
										<li><button type="button">수정</button></li>
										<li><button type="button">삭제</button></li>
									</ul>
								</div>
							</div>
							<div class="txtbox">
								{!! nl2br(e($reply->content)) !!}
							</div>
						</div>
					@endforeach
				@endforeach
			</div>
			<div class="chat_input_area">
				<div class="attach_area">
					<div class="input_attach attach_file_box"><input type="file" id="attach_file"><label for="attach_file"><strong>첨부파일</strong></label><p></p></div>
					<div class="input_attach attach_image_box"><input type="file" id="attach_image"><label for="attach_image"><strong>이미지</strong></label><p>권장 사이즈: 200x200</p></div>
				</div>
				<form method="POST" action="{{ route('subcommittee.discussion_comment_store', [$committee, $post->id]) }}" class="chat_input_area" data-subcommittee-discussion-comment-form data-validation-message="{{ $errors->first('content') }}">
					@csrf
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
<script>
window.addEventListener('DOMContentLoaded', () => {
    const chatArea = document.querySelector('.chat_area');
    if (chatArea) chatArea.scrollTop = chatArea.scrollHeight;
});
document.addEventListener("DOMContentLoaded", function () {
    const fileInputs = document.querySelectorAll('.input_attach input[type="file"]');

    fileInputs.forEach(input => {
        const container = input.closest('.input_attach');
        const pTag = container.querySelector('p');
        const defaultText = pTag.innerHTML;

        input.addEventListener('change', function (e) {
            const file = e.target.files[0];

            pTag.innerHTML = '';

            if (file) {
                container.classList.add('in');

                const nameSpan = document.createElement('span');
                nameSpan.textContent = file.name + ' ';
                nameSpan.style.marginRight = '10px';

                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.textContent = '삭제';
                delBtn.style.cursor = 'pointer';

                delBtn.addEventListener('click', function () {
                    input.value = '';
                    pTag.innerHTML = defaultText;
                    container.classList.remove('in');
                });

                pTag.appendChild(nameSpan);
                pTag.appendChild(delBtn);
            } else {
                pTag.innerHTML = defaultText;
                container.classList.remove('in');
            }
        });
    });
	initOptionAreaToggle();
});
function initOptionAreaToggle() {
    const optionButtons = document.querySelectorAll('.option_area .btn_option');
    
    optionButtons.forEach(button => {
        const targetUl = button.nextElementSibling;
        
        if (targetUl && targetUl.tagName.toLowerCase() === 'ul') {
            button.addEventListener('click', function() {
                if (targetUl.style.display === 'block') {
                    targetUl.style.display = 'none';
                } else {
                    targetUl.style.display = 'block';
                }
            });
        }
    });
}
</script>
@endpush

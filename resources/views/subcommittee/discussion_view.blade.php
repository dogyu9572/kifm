@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
@php
    $attachments = $post->attachments ? json_decode($post->attachments, true) : [];
    if (! is_array($attachments)) {
        $attachments = [];
    }
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
					<strong class="tt">조회수</strong><p>128</p>
				</div>
				<x-frontend.bookmark-button content-type="community_committee_discussions" :content-id="$post->id" :title="$post->title" :menu-label="$dName" :url="route('subcommittee.discussion_show', [$committee, $post->id])" />
			</div>

			<div class="chat_area">
				<div class="chat you">
					<div class="name">
						<strong>박진호 원장</strong>
						<div class="date">2026.03.17  14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="txtbox">
						전달해주신 서킷 브레이커 가이드 잘 확인했습니다. 혹시 전이 과정을 한눈에 볼 수 있는 요약 이미지가 있을까요?
					</div>
				</div>
				<div class="chat me">
					<div class="name">
						<strong>(나) 이강민 교수</strong>
						<div class="date">2026.03.17 14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="txtbox">
						네, 요약 다이어그램입니다. 200x200 사이즈로 컴팩트하게 정리된 이미지이니 참고해 보세요.
					</div>
				</div>
				<div class="chat you">
					<div class="name">
						<strong>박진호 원장</strong>
						<div class="date">2026.03.17  14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="txtbox">
						설정값 예시가 포함된 엑셀 파일도 함께 전달드립니다. 아래 버튼을 클릭하여 바로 다운로드하실 수 있습니다.
					</div>
				</div>
				<div class="chat me">
					<div class="name">
						<strong>(나) 이강민 교수</strong>
						<div class="date">2026.03.17 14:20</div>
						<div class="option_area">
							<button type="button" class="btn_option">수정/삭제 펼침</button>
							<ul>
								<li><button type="button">수정</button></li>
								<li><button type="button">삭제</button></li>
							</ul>
						</div>
					</div>
					<div class="txtbox">
						네
					</div>
				</div>
			</div>
			<div class="chat_input_area">
				<div class="attach_area">
					<div class="input_attach attach_file_box"><input type="file" id="attach_file"><label for="attach_file"><strong>첨부파일</strong></label><p></p></div>
					<div class="input_attach attach_image_box"><input type="file" id="attach_image"><label for="attach_image"><strong>이미지</strong></label><p>권장 사이즈: 200x200</p></div>
				</div>
				<div class="chat_input">
					<input type="text" class="text" placeholder="답글 내용을 작성해주세요.">
					<button type="submit" class="btn btn_wkk">보내기</button>
				</div>
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
<script>
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
});
</script>

@endpush
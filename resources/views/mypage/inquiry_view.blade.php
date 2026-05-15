@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>

		<div class="board_view">
			<div class="tit_area">
				@if ($is_answered)
				<div class="state end">{{ $post->reply_status_label }}</div>
				@else
				<div class="state ing">{{ $post->reply_status_label }}</div>
				@endif
				<h1 class="tit" id="society-notices-heading">{{ $post->title }}</h1>
				<div class="date"><strong class="sound_only">등록일</strong><p>{{ $post->created_at_formatted }}</p></div>
				@if (! $is_answered)
				<div class="btn_abso">
					<a href="{{ route('mypage.inquiry_edit', ['id' => $post->id]) }}" class="btn i1">수정</a>
					<form action="{{ route('mypage.inquiry.destroy', $post->id) }}" method="POST" class="d-inline" data-mypage-inquiry-delete>
						@csrf
						@method('DELETE')
						<button type="submit" class="btn i2">삭제</button>
					</form>
				</div>
				@endif
			</div>

			@if (! empty($attachments))
			<div class="file_area">
				@foreach ($attachments as $attachment)
				<a href="{{ asset('storage/'.$attachment['path']) }}" download="{{ $attachment['name'] }}">
					<strong>{{ $attachment['name'] }}</strong>
					@if (isset($attachment['size']))
					<span>({{ number_format((int) $attachment['size'] / 1024, 1) }}KB)</span>
					@endif
					<i class="btn_download flex_center">다운로드</i>
				</a>
				@endforeach
			</div>
			@endif

			<div class="cont">
				{!! $post->content !!}
			</div>

			@if (! $is_answered)
			<div class="gbox contact_info">
				<h2>문의하신 내용이 접수 되었습니다.</h2>
				<p>최대한 빠른 시일내에 답변드리겠습니다.</p>
			</div>
			@endif

			@if ($is_answered && $answer_comment)
			<div class="reply_wrap">
				<div class="writer">
					<div class="imgfit"><img src="/images/img_logo_profile.jpg" alt="" aria-hidden="true"></div>
					<div class="txt">
						<div class="name"><strong class="c_iden">{{ $answer_comment->author_name ?: '담당자' }}</strong>의 답변</div>
						<div class="date">{{ $answer_comment->created_at?->format('Y.m.d') }}</div>
					</div>
				</div>
				<div class="con">
					{!! $answer_comment->content !!}
				</div>
				@if (! empty($answer_attachments))
				<div class="file_area">
					@foreach ($answer_attachments as $attachment)
					<a href="{{ asset('storage/'.$attachment['path']) }}" download="{{ $attachment['name'] }}">
						<strong>{{ $attachment['name'] }}</strong>
						@if (isset($attachment['size']))
						<span>({{ number_format((int) $attachment['size'] / 1024, 1) }}KB)</span>
						@endif
					</a>
					@endforeach
				</div>
				@endif
			</div>
			@endif
		</div>

		<div class="board_bottom">
			<a href="{{ route('mypage.inquiry') }}" class="btn btn_wkk btn_list">목록</a>
		</div>

	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/mypage-inquiry-view.js') }}"></script>
@endpush

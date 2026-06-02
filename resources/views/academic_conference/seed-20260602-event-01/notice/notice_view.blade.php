@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="academic-notices-view-heading">
	<div class="inner">
		@if($notice)
		<div class="board_view">
			<div class="tit_area">
				<h1 class="tit" id="academic-notices-view-heading">{{ $notice->title }}</h1>
				<div class="date"><strong class="sound_only">등록일</strong><p>{{ \Illuminate\Support\Carbon::parse($notice->created_at)->format('Y.m.d') }}</p></div>
			</div>
			<div class="cont">
				{!! $notice->content !!}
			</div>
			@if(! empty($noticeAttachments))
				<div class="file_area">
					@foreach($noticeAttachments as $attachment)
						<a href="{{ $attachment['url'] }}" download="{{ $attachment['name'] }}"><strong>{{ $attachment['name'] }}</strong><span>({{ $attachment['size_text'] }})</span><i class="btn_download flex_center">다운로드</i></a>
					@endforeach
				</div>
			@endif
			<div class="prev_next">
				@if($prevNotice)
					<a href="{{ $conferenceBaseUrl }}/notice/view?id={{ $prevNotice->id }}" class="prev"><strong>이전 글</strong><p>{{ $prevNotice->title }}</p></a>
				@else
					<a href="#this" class="prev"><strong>이전 글</strong><p>이전 글이 없습니다.</p></a>
				@endif
				@if($nextNotice)
					<a href="{{ $conferenceBaseUrl }}/notice/view?id={{ $nextNotice->id }}" class="next"><strong>다음 글</strong><p>{{ $nextNotice->title }}</p></a>
				@else
					<a href="#this" class="next"><strong>다음 글</strong><p>다음 글이 없습니다.</p></a>
				@endif
			</div>
		</div>
		@else
			<div class="board_view">
				<div class="tit_area">
					<h1 class="tit" id="academic-notices-view-heading">등록된 공지사항이 없습니다.</h1>
				</div>
			</div>
		@endif
		
		<div class="board_bottom">
			<a href="{{ $conferenceBaseUrl }}/notice" class="btn btn_wkk btn_list">목록</a>
		</div>
		
	</div>
</section>
	
</main>

@endsection

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

		<div class="board_view">
			<div class="tit_area">
				<h1 class="tit" id="subcommittee-post-heading">{{ $post->title }}</h1>
				<div class="date"><strong class="sound_only">등록일</strong><p>{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d') }}</p></div>
				<x-frontend.bookmark-button content-type="community_committee_discussions" :content-id="$post->id" :title="$post->title" :menu-label="$dName" :url="route('subcommittee.discussion_show', [$committee, $post->id])" />
			</div>

			@if (! empty($attachments))
				<div class="file_area">
					@foreach ($attachments as $attachment)
						@continue (! is_array($attachment) || ! isset($attachment['path'], $attachment['name']))
						<a href="{{ asset('storage/'.$attachment['path']) }}" download="{{ $attachment['name'] }}">
							<strong>{{ $attachment['name'] }}</strong>
							@if (isset($attachment['size']))
								<span>({{ number_format($attachment['size'] / 1024, 1) }}KB)</span>
							@endif
							<i class="btn_download flex_center">다운로드</i>
						</a>
					@endforeach
				</div>
			@endif

			<div class="cont">
				{!! $post->content !!}
			</div>

			<div class="prev_next">
				@if ($prev)
					<a href="{{ route('subcommittee.discussion_show', [$committee, $prev->id]) }}" class="prev"><strong>이전 글</strong><p>{{ $prev->title }}</p></a>
				@else
					<a href="#this" class="prev" aria-disabled="true"><strong>이전 글</strong><p>이전 글이 없습니다.</p></a>
				@endif
				@if ($next)
					<a href="{{ route('subcommittee.discussion_show', [$committee, $next->id]) }}" class="next"><strong>다음 글</strong><p>{{ $next->title }}</p></a>
				@else
					<a href="#this" class="next" aria-disabled="true"><strong>다음 글</strong><p>다음 글이 없습니다.</p></a>
				@endif
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
@endpush

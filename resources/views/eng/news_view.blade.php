@extends('layouts.frontend')
@section('title', $geName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">
<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<div class="sub_title">{{ $geName }}</div>
		
		<div class="board_view">
			<div class="tit_area">
				<h1 class="tit" id="society-notices-heading">This is where the title will be placed. This is where the title will be placed.</h1>
				<div class="date"><strong class="sound_only">Date</strong><p>2026.03.01</p></div>
				<button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
			</div>
			<div class="file_area">
				<a href="#this" download><strong>This is where the attachment will be placed.</strong><span>(110.5KB)</span><i class="btn_download flex_center">Download</i></a>
			</div>
			<div class="cont">
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.<br/>
				This is the content of the post. This is the content of the post. This is the content of the post.
			</div>
			<div class="prev_next">
				<a href="#this" class="prev"><strong>Previous</strong><p>No previous post.</p></a>
				<a href="#this" class="next"><strong>Next</strong><p>No next post.</p></a>
			</div>
		</div>
		
		<div class="board_bottom">
			<a href="/eng/news" class="btn btn_wkk btn_list">List</a>
		</div>
		
	</div>
</section>
	
</main>
@endsection
@push('scripts')
<script src="{{ asset('js/script_bookmark.js') }}"></script>
@endpush
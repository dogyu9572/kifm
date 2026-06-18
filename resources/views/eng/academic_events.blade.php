@extends('layouts.frontend')
@section('title', $geName)
@section('gName', $gName)
@section('sName', $geName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_wrap" aria-labelledby="conference-heading">
	<div class="inner">
		<h1 class="sub_title" id="conference-heading">{{ $geName }}</h1>
		
		<div class="academic_event_head">
			<div class="imgfit" aria-hidden="true"><img src="/images/img_sample_conference_top.jpg" alt=""></div>
			<div class="txt">
				<a href="/academic_event/conference/view">
					<p class="eng_title c_iden">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
					<h2>2025 Korean Society for Functional Medicine Autumn Academic Conference</h2>
					<ul class="info_list">
						<li class="i1"><strong>Date</strong>November 16, 2026 (Sun)</li>
						<li class="i2"><strong>Registration</strong>November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
						<li class="i3"><strong>Venue</strong>Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
					</ul>
				</a>
				<div class="btns">
					<a href="#this" target="_blank" title="Opens in new window" class="btn btn_wkk btn_outlink">Visit Website</a>
					<a href="#this" target="_blank" title="Opens in new window" class="btn btn_wrr btn_outlink">Registration</a>
				</div>
				<button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
			</div>
		</div>
		
		<div class="academic_event_body">
			<div class="bdbtit">
				<h3>Latest {{ $geName }} List</h3>
				<ul class="tabs" role="tablist">
                    <li class="on"><a href="#this" role="tab" aria-selected="true">View All</a></li>
                    <li><a href="#this" role="tab" aria-selected="false">Upcoming</a></li>
                    <li><a href="#this" role="tab" aria-selected="false">Open</a></li>
                    <li><a href="#this" role="tab" aria-selected="false">Closed</a></li>
                </ul>
			</div>
			<div class="board_top">
				<div class="left">
					<label for="event-year" class="sound_only">Select Event Year</label>
					<select name="event-year" id="event-year-before" class="years">
						<option value="">Event Year</option>
					</select>
				</div>
				<div class="right">
					<form class="search_area">
                        <label for="event-search" class="sound_only">Search by Event Name</label>
                        <input type="text" id="event-search" class="text" placeholder="Enter event name">
                        <button type="submit" class="btn_search">Search</button>
                    </form>
				</div>
			</div>
			<ul class="list">
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state end"><span class="sound_only">Status:</span>Closed</span>
                        <h4>2025 Korean Society for Functional Medicine Autumn Academic Conference</h4>
                        <p class="summary">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
                        <ul class="details">
                            <li><strong>Date</strong> November 16, 2026 (Sun)</li>
                            <li><strong>Registration</strong> November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
                            <li><strong>Venue</strong> Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state expected"><span class="sound_only">Status:</span>Upcoming</span>
                        <h4>2025 Korean Society for Functional Medicine Autumn Academic Conference</h4>
                        <p class="summary">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
                        <ul class="details">
                            <li><strong>Date</strong> November 16, 2026 (Sun)</li>
                            <li><strong>Registration</strong> November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
                            <li><strong>Venue</strong> Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state ing"><span class="sound_only">Status:</span>Open</span>
                        <h4>2025 Korean Society for Functional Medicine Autumn Academic Conference</h4>
                        <p class="summary">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
                        <ul class="details">
                            <li><strong>Date</strong> November 16, 2026 (Sun)</li>
                            <li><strong>Registration</strong> November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
                            <li><strong>Venue</strong> Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state end"><span class="sound_only">Status:</span>Closed</span>
                        <h4>2025 Korean Society for Functional Medicine Autumn Academic Conference</h4>
                        <p class="summary">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
                        <ul class="details">
                            <li><strong>Date</strong> November 16, 2026 (Sun)</li>
                            <li><strong>Registration</strong> November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
                            <li><strong>Venue</strong> Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state expected"><span class="sound_only">Status:</span>Upcoming</span>
                        <h4>2025 Korean Society for Functional Medicine Autumn Academic Conference</h4>
                        <p class="summary">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
                        <ul class="details">
                            <li><strong>Date</strong> November 16, 2026 (Sun)</li>
                            <li><strong>Registration</strong> November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
                            <li><strong>Venue</strong> Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state ing"><span class="sound_only">Status:</span>Open</span>
                        <h4>2025 Korean Society for Functional Medicine Autumn Academic Conference</h4>
                        <p class="summary">Geroscience and functional medicine. The Role of Functional Medicine in the Era of Anti-Aging Treatment</p>
                        <ul class="details">
                            <li><strong>Date</strong> November 16, 2026 (Sun)</li>
                            <li><strong>Registration</strong> November 1, 2026 (Mon) ~ November 9, 2026 (Sun)</li>
                            <li><strong>Venue</strong> Yukwangsa Hall, 2F Main Building, Korea University College of Medicine</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="Add this event to bookmarks" aria-pressed="false"></button>
                </li>
			</ul>
		</div>

		<nav class="board-pagination" aria-label="Board page navigation">
			<ul class="pagination">
				<li class="page-item arw_item"><a class="page-link" href="#" title="First page" aria-label="Go to first page"><i class="arrow two first" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="Previous page" aria-label="Go to previous page"><i class="arrow one prev" aria-hidden="true"></i></a></li>
				<li class="page-item active"><span class="page-link" aria-current="page" aria-label="Current page 1">1</span></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 2">2</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 3">3</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 4">4</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 5">5</a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="Next page" aria-label="Go to next page"><i class="arrow one next" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="Last page" aria-label="Go to last page"><i class="arrow two last" aria-hidden="true"></i></a></li>
			</ul>
		</nav>
		
	</div>
</section>
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_bookmark.js') }}"></script>
@endpush
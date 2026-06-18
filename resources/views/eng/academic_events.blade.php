@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_wrap" aria-labelledby="conference-heading">
	<div class="inner">
		<h1 class="sub_title" id="conference-heading">{{ $sName }}</h1>
		
		<div class="academic_event_head">
			<div class="imgfit" aria-hidden="true"><img src="/images/img_sample_conference_top.jpg" alt=""></div>
			<div class="txt">
				<a href="/academic_event/conference/view">
					<p class="eng_title c_iden">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
					<h2>2025년 대한기능의학회 추계학술대회</h2>
					<ul class="info_list">
						<li class="i1"><strong>일시</strong>2026년 11월 16일 (일)</li>
						<li class="i2"><strong>사전등록</strong>2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
						<li class="i3"><strong>장소</strong>고려대학교 의과대학 본관 2층 유광사홀</li>
					</ul>
				</a>
				<div class="btns">
					<a href="#this" target="_blank" title="새창 열림" class="btn btn_wkk btn_outlink">홈페이지 바로가기</a>
					<a href="#this" target="_blank" title="새창 열림" class="btn btn_wrr btn_outlink">사전등록 바로가기</a>
				</div>
				<button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
			</div>
		</div>
		
		<div class="academic_event_body">
			<div class="bdbtit">
				<h3>최신 {{ $sName }} 목록</h3>
				<ul class="tabs" role="tablist">
                    <li class="on"><a href="#this" role="tab" aria-selected="true">전체보기</a></li>
                    <li><a href="#this" role="tab" aria-selected="false">모집 예정</a></li>
                    <li><a href="#this" role="tab" aria-selected="false">모집 중</a></li>
                    <li><a href="#this" role="tab" aria-selected="false">신청마감</a></li>
                </ul>
			</div>
			<div class="board_top">
				<div class="left">
					<label for="event-year" class="sound_only">행사년도 선택</label>
					<select name="event-year" id="event-year-before" class="years">
						<option value="">행사년도</option>
					</select>
				</div>
				<div class="right">
					<form class="search_area">
                        <label for="event-search" class="sound_only">대회명 검색</label>
                        <input type="text" id="event-search" class="text" placeholder="대회명을 입력해주세요">
                        <button type="submit" class="btn_search">검색</button>
                    </form>
				</div>
			</div>
			<ul class="list">
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state end"><span class="sound_only">상태:</span>신청마감</span>
                        <h4>2025년 대한기능의학회 추계학술대회</h4>
                        <p class="summary">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
                        <ul class="details">
                            <li><strong>일시</strong> 2026년 11월 16일 (일)</li>
                            <li><strong>사전등록</strong> 2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
                            <li><strong>장소</strong> 고려대학교 의과대학 본관 2층 유광사홀</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state expected"><span class="sound_only">상태:</span>모집예정</span>
                        <h4>2025년 대한기능의학회 추계학술대회</h4>
                        <p class="summary">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
                        <ul class="details">
                            <li><strong>일시</strong> 2026년 11월 16일 (일)</li>
                            <li><strong>사전등록</strong> 2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
                            <li><strong>장소</strong> 고려대학교 의과대학 본관 2층 유광사홀</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state ing"><span class="sound_only">상태:</span>모집 중</span>
                        <h4>2025년 대한기능의학회 추계학술대회</h4>
                        <p class="summary">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
                        <ul class="details">
                            <li><strong>일시</strong> 2026년 11월 16일 (일)</li>
                            <li><strong>사전등록</strong> 2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
                            <li><strong>장소</strong> 고려대학교 의과대학 본관 2층 유광사홀</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state end"><span class="sound_only">상태:</span>신청마감</span>
                        <h4>2025년 대한기능의학회 추계학술대회</h4>
                        <p class="summary">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
                        <ul class="details">
                            <li><strong>일시</strong> 2026년 11월 16일 (일)</li>
                            <li><strong>사전등록</strong> 2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
                            <li><strong>장소</strong> 고려대학교 의과대학 본관 2층 유광사홀</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state expected"><span class="sound_only">상태:</span>모집예정</span>
                        <h4>2025년 대한기능의학회 추계학술대회</h4>
                        <p class="summary">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
                        <ul class="details">
                            <li><strong>일시</strong> 2026년 11월 16일 (일)</li>
                            <li><strong>사전등록</strong> 2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
                            <li><strong>장소</strong> 고려대학교 의과대학 본관 2층 유광사홀</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
                </li>
				<li>
                    <a href="/academic_event/conference/view">
                        <span class="state ing"><span class="sound_only">상태:</span>모집 중</span>
                        <h4>2025년 대한기능의학회 추계학술대회</h4>
                        <p class="summary">Geroscience and functional medicine. 노화치료의 시대에 기능의학의 역할</p>
                        <ul class="details">
                            <li><strong>일시</strong> 2026년 11월 16일 (일)</li>
                            <li><strong>사전등록</strong> 2026년 11월 1일 (월) ~ 2026년 11월 9일 (일)</li>
                            <li><strong>장소</strong> 고려대학교 의과대학 본관 2층 유광사홀</li>
                        </ul>
                    </a>
                    <button type="button" class="bookmark" aria-label="이 행사를 북마크에 추가" aria-pressed="false"></button>
                </li>
			</ul>
		</div>

		<nav class="board-pagination" aria-label="게시판 페이지 이동">
			<ul class="pagination">
				<li class="page-item arw_item"><a class="page-link" href="#" title="첫 페이지" aria-label="첫 페이지로 이동"><i class="arrow two first" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="이전 페이지" aria-label="이전 페이지로 이동"><i class="arrow one prev" aria-hidden="true"></i></a></li>
				<li class="page-item active"><span class="page-link" aria-current="page" aria-label="현재 페이지 1">1</span></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="2페이지로 이동">2</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="3페이지로 이동">3</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="4페이지로 이동">4</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="5페이지로 이동">5</a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="다음 페이지" aria-label="다음 페이지로 이동"><i class="arrow one next" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="마지막 페이지" aria-label="마지막 페이지로 이동"><i class="arrow two last" aria-hidden="true"></i></a></li>
			</ul>
		</nav>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/script_bookmark.js') }}"></script>
@endpush
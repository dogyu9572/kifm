@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@php
	$calendarPayload = e(json_encode($calendarSchedules ?? [], JSON_UNESCAPED_UNICODE));
	$memberCard = $memberCard ?? ['is_logged_in' => false];
	$certification = $memberCard['certification'] ?? [
		'conference_count' => 0,
		'conference_required' => 3,
		'conference_short' => 3,
		'progress_percent' => 0,
	];
	$conferenceCount = (int) ($certification['conference_count'] ?? 0);
	$conferenceRequired = max(1, (int) ($certification['conference_required'] ?? 3));
	$conferenceShort = max(0, (int) ($certification['conference_short'] ?? 0));
@endphp

@section('content')
<main class="main_wrap" data-calendar-schedules="{!! $calendarPayload !!}">
<h1 class="sound_only">대한기능의학회 메인</h1>

<!-- main_visual -->
<section class="main_visual_wrap" aria-labelledby="visual-title">
	<h2 class="sound_only" id="visual-title">메인 비주얼</h2>
	<div class="inner flex">
		<div class="mvisual">
			<div class="swiper mvisual-swiper">
				<div class="swiper-wrapper">
					@forelse ($banners as $banner)
						@php
							$bannerImage = $banner->desktop_image
								? (str_starts_with($banner->desktop_image, 'http') ? $banner->desktop_image : \Illuminate\Support\Facades\Storage::disk('public')->url($banner->desktop_image))
								: asset('images/img_main_visual_01.jpg');
							$bannerAlt = $banner->title ?: '메인 비주얼';
						@endphp
						<div class="swiper-slide">
							@if ($banner->url)
								<a href="{{ $banner->url }}" target="{{ $banner->url_target ?: '_self' }}">
									<img src="{{ $bannerImage }}" alt="{{ $bannerAlt }}">
								</a>
							@else
								<img src="{{ $bannerImage }}" alt="{{ $bannerAlt }}">
							@endif
							@if ($banner->main_text || $banner->sub_text)
								<div class="txt">
									<p>{{ $banner->sub_text }} @if ($banner->main_text)<strong>{!! nl2br(e($banner->main_text)) !!}</strong>@endif</p>
								</div>
							@endif
						</div>
					@empty
						<div class="swiper-slide"><img src="/images/img_main_visual_01.jpg" alt="메인 비주얼">
							<div class="txt">
								<p>미래의학의 새로운 패러다임인 <strong>기능의학 학회에<br>여러분을 초대합니다.</strong></p>
							</div>
						</div>
					@endforelse
				</div>
				<div class="mvisual-control">
					<div class="paging"></div>
					<button type="button" class="arrow prev" aria-label="이전 슬라이드"></button>
					<button type="button" class="arrow next" aria-label="다음 슬라이드"></button>
					<button type="button" class="papl" aria-label="슬라이드 정지" aria-pressed="false"></button>
				</div>
			</div>
		</div>
		<div class="right">
			@if (empty($memberCard['is_logged_in']))
				<div class="log_area before">
					<h2><strong>로그인 후</strong> 맞춤정보를 확인해보세요</h2>
					<form method="POST" action="{{ route('member.login.store') }}" class="inputs" novalidate>
						@csrf
						<label for="main-login-id" class="sound_only">아이디</label>
						<input type="text" id="main-login-id" name="login_id" class="text w100p" placeholder="아이디" value="{{ old('login_id') }}" required autocomplete="username">
						<label for="main-login-password" class="sound_only">비밀번호</label>
						<input type="password" id="main-login-password" name="password" class="text w100p" placeholder="비밀번호" required autocomplete="current-password">
						<button type="submit" class="btn">로그인</button>
						<ul class="mem_links">
							<li><a href="{{ route('member.find_id') }}">아이디 찾기</a></li>
							<li><a href="{{ route('member.find_pw') }}">비밀번호 찾기</a></li>
							<li><a href="{{ route('member.register') }}">회원가입</a></li>
						</ul>
					</form>
				</div>
			@else
				<div class="log_area after">
					<ul class="member_type">
						@foreach ($memberCard['member_level_labels'] ?? ['회원'] as $idx => $label)
							<li class="t{{ min($idx + 1, 3) }}">{{ $label }}</li>
						@endforeach
					</ul>
					<div class="name">
						<h2>안녕하세요, {{ $memberCard['name'] }} 선생님!</h2>
						<a href="{{ route('mypage.profile_edit') }}" class="more"><span class="sound_only">마이페이지로 이동</span></a>
					</div>
					<div class="member_info">
						<div class="tit">
							<strong>인정의 자격 정보</strong>
							@if (! empty($memberCard['certification_period']))
								<div class="date">{{ $memberCard['certification_period'] }}</div>
							@endif
						</div>
						<dl class="flex flex_between">
							<dt>참석 현황</dt>
							<dd><strong class="c_iden">{{ $conferenceCount }}</strong>/{{ $conferenceRequired }}회</dd>
						</dl>
						<div class="state_line"><div class="bar"></div></div>
						@if ($conferenceShort > 0)
							<div class="info flex_end">
								<div class="r"><p class="excl">{{ $conferenceShort }}회 부족</p></div>
							</div>
						@endif
					</div>
					<div class="btns">
						<a href="{{ route('mypage.profile_edit') }}" class="btn btn_wbb">마이페이지</a>
						<a href="{{ route('mypage.online_training') }}" class="btn btn_wkk">강의실 입장</a>
					</div>
				</div>
			@endif
			<ul class="page_links">
				<li class="i1"><a href="{{ route('member.register') }}">회원가입 안내</a></li>
				<li class="i2"><a href="{{ route('academic_event.conference') }}">학술대회</a></li>
				<li class="i3"><a href="{{ route('online_academy.index') }}">온라인 아카데미</a></li>
				<li class="i4"><a href="{{ route('subcommittee.index') }}">대한기능의학 위원회</a></li>
			</ul>
		</div>
	</div>
	<div class="inner">
		<div class="book_area">
			<h3 class="book_label">학술지</h3>
			<div class="book_slide swiper">
				<div class="swiper-wrapper">
					@forelse ($journalPosts as $post)
						@php
							$journalFields = is_string($post->custom_fields ?? null)
								? (json_decode($post->custom_fields, true) ?: [])
								: ($post->custom_fields ?? []);
							$journalUrl = $journalFields['link_url'] ?? route('archives.journals');
						@endphp
						<div class="swiper-slide"><a href="{{ $journalUrl }}" target="_blank" rel="noopener">{{ $post->title }}</a></div>
					@empty
						<div class="swiper-slide"><a href="{{ route('archives.journals') }}">등록된 학술지가 없습니다.</a></div>
					@endforelse
				</div>
			</div>
			<div class="book_control">
				<button type="button" class="arrow prev" aria-label="이전 슬라이드"></button>
				<button type="button" class="arrow next" aria-label="다음 슬라이드"></button>
			</div>
		</div>
	</div>
</section>

<!-- 학술대회 일정 / 학회 일정 -->
<section class="mcon mc01" aria-labelledby="schedule-title">
	<h2 class="sound_only" id="schedule-title">일정</h2>
	<div class="inner">
		<div class="left">
			<div class="mtit">
				<h3>학술대회 일정</h3>
				<div class="arrows flex">
					<button type="button" class="arrow prev" aria-label="학술대회 일정 이전 슬라이드"></button>
					<button type="button" class="arrow next" aria-label="학술대회 일정 다음 슬라이드"></button>
				</div>
			</div>
			<div class="schedule">
				<ul class="month">
					@foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
						<li><button type="button">{{ $month }}</button></li>
					@endforeach
				</ul>
				<div class="schedule_slide">
					<div class="swiper-wrapper">
						@forelse ($eventScheduleItems as $item)
							<div class="swiper-slide {{ $item['type_class'] }}">
								<a href="{{ $item['url'] }}" class="card-link" data-schedule-month="{{ \Carbon\Carbon::parse($item['start_date'])->format('M') }}">
									<span class="day" aria-hidden="true"><strong>{{ $item['day'] }}</strong>일 ({{ $item['weekday'] }})</span>
									<h4 class="title">{{ $item['title'] }}</h4>
									<time datetime="{{ $item['end_date'] ? $item['start_date'].'/'.$item['end_date'] : $item['start_date'] }}" class="full-date"><span class="sound_only">기간: </span>{{ $item['date_text'] }}</time>
									<span class="type">{{ $item['type_label'] }}</span>
								</a>
							</div>
						@empty
							<div class="swiper-slide empty">
								<p class="schedule_empty">등록된 학술행사가 없습니다.</p>
							</div>
						@endforelse
					</div>
				</div>
			</div>
		</div>
		<div class="right">
			<div class="mtit">
				<h3>학회 일정</h3>
			</div>
			<div class="month_area">
				<div class="select_month">
					<button type="button" class="arrow prev" aria-label="이전달"></button>
					<button type="button" class="arrow next" aria-label="다음달"></button>
					<span class="tomonth"></span>
				</div>
				<div class="month">
					<table>
						<caption>학회 일정 달력</caption>
						<thead>
							<tr>
								<th>일</th>
								<th>월</th>
								<th>화</th>
								<th>수</th>
								<th>목</th>
								<th>금</th>
								<th>토</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 공지사항 / 온라인 아카데미 / 회원자료실 -->
<section class="mcon mc02" aria-labelledby="notice-title">
	<h2 class="sound_only" id="notice-title">공지사항 및 배너 링크</h2>
	<div class="inner">
		<div class="long notice">
			<div class="mtit"><h3>공지사항</h3><a href="{{ route('member_plaza.society_notices') }}" class="more" aria-label="대한기능의학회 공지사항으로 이동"></a></div>
			<ul class="list">
				@forelse ($noticePosts as $post)
					<li class="{{ $post->is_notice ? 'notice' : '' }}"><a href="{{ route('member_plaza.society_notices_show', $post->id) }}">{{ $post->title }}<span class="date">{{ \Carbon\Carbon::parse($post->created_at)->format('Y.m.d') }}</span></a></li>
				@empty
					<li><a href="{{ route('member_plaza.society_notices') }}">등록된 공지사항이 없습니다.<span class="date">-</span></a></li>
				@endforelse
			</ul>
		</div>
		<div class="short academy">
			<div class="mtit"><h3>온라인 아카데미</h3></div>
			<div class="main_gall">
				@if ($academyCourse)
					<a href="{{ route('online_academy.show', $academyCourse) }}"><span class="imgfit"><img src="{{ $academyCourse->thumbnail_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($academyCourse->thumbnail_path) : asset('images/img_sample_mc02_01.jpg') }}" alt=""></span><span class="txt"><p>{{ $academyCourse->title }}</p></span></a>
				@else
					<a href="{{ route('online_academy.index') }}"><span class="imgfit"><img src="/images/img_sample_mc02_01.jpg" alt=""></span><span class="txt"><p>등록된 온라인 아카데미가 없습니다.</p></span></a>
				@endif
			</div>
		</div>
		<div class="short archives">
			<div class="mtit"><h3>회원자료실</h3></div>
			<div class="main_gall">
				@if ($memberArchivePost)
					<a href="{{ route('archives.members_show', $memberArchivePost->id) }}"><span class="imgfit"><img src="{{ $memberArchivePost->thumbnail ? \Illuminate\Support\Facades\Storage::disk('public')->url($memberArchivePost->thumbnail) : asset('images/img_sample_mc02_02.jpg') }}" alt=""></span><span class="txt"><p>{{ $memberArchivePost->title }}</p></span></a>
				@else
					<a href="{{ route('archives.members') }}"><span class="imgfit"><img src="/images/img_sample_mc02_02.jpg" alt=""></span><span class="txt"><p>등록된 회원자료실 게시글이 없습니다.</p></span></a>
				@endif
			</div>
		</div>
	</div>
</section>

<!-- 달력 클릭시 팝업 -->
<div class="popup calendar_event_popup" hidden>
	<div class="dm"></div>
	<div class="inbox">
		<div class="event_tit"></div>
		<ul class="schedule_list scroll auto"></ul>
		<div class="btns_btm">
			<button type="button" class="btn btn_wkk btn_close_btm">닫기</button>
		</div>
	</div>
</div>

<!-- 로그인시 팝업 - 위원회 미가입 -->
@if($showCommitteeJoinPopup ?? false)
<div class="popup popup_login_start" data-main-auto-open hidden>
	<div class="dm"></div>
	<div class="inbox">
		<div class="tit_center">회원님의 더 깊이 있는 <br class="mo_vw">연구와 교류를 응원합니다</div>
		<div class="gbox tac">대한기능의학회의 학술적 발전을 위해 산하 위원회 활동을 권장해 드립니다.<br>지금 바로 회원님께 꼭 맞는 위원회를 확인해 보세요.</div>
		<div class="flex_center">
			<a href="{{ route('subcommittee.index') }}" class="btn btn_sanha_link">산하위원회 가기</a>
		</div>
		<div class="btns_btm mt0">
			<button type="button" class="btn btn_kwg btn_close_btm" data-main-popup-hide-today>오늘 하루 보지 않기</button>
			<button type="button" class="btn btn_wkk btn_close_btm">닫기</button>
		</div>
	</div>
</div>
@endif

</main>

@endsection

@section('popups')
@if($popups->count() > 0)
	@foreach($popups as $popup)
		@if($popup->popup_display_type === 'normal')
			<div
				data-main-normal-popup
				data-popup-url="{{ route('popup.show', $popup->id) }}"
				data-popup-id="{{ $popup->id }}"
				data-popup-width="{{ $popup->width }}"
				data-popup-height="{{ $popup->height }}"
				data-popup-left="{{ $popup->position_left ?? 100 }}"
				data-popup-top="{{ $popup->position_top ?? 100 }}"
				hidden
			></div>
		@else
			<div
				class="popup-layer popup-fixed"
				id="popup-{{ $popup->id }}"
				data-main-layer-popup
				data-popup-id="{{ $popup->id }}"
				data-display-type="layer"
				data-popup-width="{{ $popup->width }}"
				data-popup-top="{{ $popup->position_top }}"
				data-popup-left="{{ $popup->position_left }}"
				hidden
			>

				<div class="popup-body">
					@if($popup->popup_type === 'image' && $popup->popup_image)
						@if($popup->url)
							<a href="{{ $popup->url }}" target="{{ $popup->url_target }}">
								<img src="{{ asset('storage/' . $popup->popup_image) }}" alt="{{ $popup->title }}">
							</a>
						@else
							<img src="{{ asset('storage/' . $popup->popup_image) }}" alt="{{ $popup->title }}">
						@endif
					@elseif($popup->popup_type === 'html' && $popup->popup_content)
						{!! $popup->popup_content !!}
					@endif
				</div>

				<div class="popup-footer">
					<label class="popup-today-label" data-popup-id="{{ $popup->id }}">
						<input type="checkbox" class="popup-today-close" data-popup-id="{{ $popup->id }}">
						1일 동안 보지 않음
					</label>
					<button type="button" class="popup-footer-close-btn" data-popup-id="{{ $popup->id }}">닫기</button>
				</div>
			</div>
		@endif
	@endforeach
@endif
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('js/frontend/home.js') }}"></script>
@endpush

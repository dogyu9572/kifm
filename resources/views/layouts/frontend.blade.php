<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="robots" content="index, follow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@php
		$page_type = $page_type ?? '';
		$siteUrl = config('app.url');
		$jsonName = (isset($gNum) && $gNum == 'main') ? '대한기능의학회' : '대한기능의학회 | ' . ($__env->yieldContent('title') ?? '');
		$jsonDesc = $__env->yieldContent('description') ?? '';
		$jsonKeywords = $__env->yieldContent('keywords') ?? '대한기능의학회';
	@endphp
	@yield('meta_tags')
	<title>@if(isset($gNum) && $gNum == 'intro'){{ $jsonName }}@else @yield('title') @endif</title>
	<meta name="title" content="@if(isset($gNum) && $gNum == 'main'){{ $jsonName }}@else @yield('title') @endif" />
	<meta name="subject" content="{{ $jsonName }}" />
    <meta name="description" content="@yield('description', '기능의학 학회에  여러분을 초대합니다.')">
    <meta name="author" content="대한기능의학회">
	<meta name="copyright" content="대한기능의학회" />
    <meta property="og:title" content="대한기능의학회@if(isset($gNum) && $gNum == 'main')@else | @yield('title', '')@endif">
	<meta property="og:subject" content="대한기능의학회" />
    <meta property="og:description" content="@yield('description', '기능의학 학회에  여러분을 초대합니다.')">
    <meta property="og:image" content="/images/og_image.jpg">
	<link rel="icon" href="/images/favicon.png" type="image/x-icon"/>
    <meta property="og:site_name" content="대한기능의학회">
    <link rel="canonical" href="{{ $siteUrl }}" />
	<meta property="og:type" content="website">
    <meta property="og:url" content="{{ $siteUrl }}">
	

	<!-- SEO,GEO 용 사이트 소개 표 - 추가되는 부분은 sga_plus로 각 페이지에서 관리합니다. -->
	<script type="application/ld+json">
	{
		"{{'@'}}context": "https://schema.org",
		"{{'@'}}type": "WebPage",
		"name": "{{ $jsonName }}",
		"description": "{{ $jsonDesc }}",
	    "keywords": "{{ $jsonKeywords }}",
		"url": "{{ url()->current() }}",
		"inLanguage": "ko-KR",
		"publisher": {
			"{{'@'}}type": "Organization",
			"name": "{{ $jsonName }}",
			"url": "{{ $siteUrl }}",
			"logo": {
				"{{'@'}}type": "ImageObject",
				"url": "{{ $siteUrl }}/images/logo.png"
			}
		},
		"breadcrumb": {
			"{{'@'}}type": "BreadcrumbList",
			"itemListElement": [
				{ "{{'@'}}type": "ListItem", "position": 1, "name": "홈", "item": "{{ $siteUrl }}" },
				{ "{{'@'}}type": "ListItem", "position": 2, "name": "{{ $gName ?? '' }}", "item": "{{ $siteUrl }}/{{ request()->segment(1) }}" }
				@if(isset($gNum) && ($gNum == '01' || $gNum == '02' || ($page ?? '') == 'view'))
				,{ "{{'@'}}type": "ListItem", "position": 3, "name": "{{ $sName ?? '' }}", "item": "{{ strtok(url()->current(), '?') }}" }
				@endif
			]
		},
		/*{
			"@type": "SiteNavigationElement",
			"@id": "{{ $siteUrl }}/#main-navi",
			"name": "대한기능의학회 메인 네비게이션",
			"itemListElement": [
				{ "@type": "ListItem", "position": 1,  "name": "소개",  "url": "{{ $siteUrl }}/studies/introduction" },
			]
		},*/
		@yield('sga_plus', '')
	}
	</script>

	@php
		$css_path			= public_path('css/frontend/styles.css');
		$reactive_path		= public_path('css/frontend/reactive.css');
		$ver_styles			= file_exists($css_path)      ? filemtime($css_path)      : date('YmdHis');
		$ver_reactive		= file_exists($reactive_path) ? filemtime($reactive_path) : date('YmdHis');

		$css_aca_path		= public_path('css/frontend/styles_academic.css');
		$reactive_aca_path	= public_path('css/frontend/reactive_academic.css');
		$ver_styles_aca		= file_exists($css_aca_path)      ? filemtime($css_aca_path)      : date('YmdHis');
		$ver_reactive_aca	= file_exists($reactive_aca_path) ? filemtime($reactive_aca_path) : date('YmdHis');
	@endphp
    <link rel="preload" href="/css/font/Pretendard-Regular.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="/css/font/Pretendard-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/css/font/Pretendard-SemiBold.woff2" as="font" type="font/woff2" crossorigin>

	<link rel="stylesheet" href="/css/frontend/font.css" media="all">
    <link rel="stylesheet" href="/css/frontend/swiper.css?v={{ $ver_styles }}">
	@if(isset($gNum) && $page_type !== 'academic_conference')
    <link rel="stylesheet" href="/css/frontend/styles.css?v={{ $ver_styles }}">
	<link rel="stylesheet" href="/css/frontend/reactive.css?v={{ $ver_reactive }}">
	@endif
	@if(isset($gNum) && $page_type == 'academic_conference')
    <link rel="stylesheet" href="/css/frontend/styles_academic.css?v={{ $ver_styles_aca }}">
	<link rel="stylesheet" href="/css/frontend/reactive_academic.css?v={{ $ver_reactive_aca }}">
	@endif
    <!-- page Styles -->
    @yield('styles')
    @if(($gNum ?? '') == '03')
    <link rel="stylesheet" href="{{ asset('css/frontend/popup.css') }}">
    @endif

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/js/frontend/swiper.js"></script>
    <script src="/js/frontend/com.js"></script>
    <script src="/js/frontend/channel-talk.js"></script>
</head>
<body>
	<div class="blind_link"><a href="#mainContent">본문 바로가기</a></div>
	@if(isset($gNum) && $gNum !== 'intro' && $gNum !== 'print' && $page_type!== 'academic_conference')
	<!-- 일반인, 전문인, 온라인 아카데미 -->
    <header class="header {{ (isset($gNum) && $gNum == 'main') ? 'main' : '' }} {{ (isset($gNum) && $gNum == '03' && ($page ?? '') == 'view' || $gNum == '01' || $gNum == '02') ? 'white_mode' : '' }}">
		<div class="top">
			<div class="inner">
				<ul class="type" role="tablist" aria-label="회원 유형 선택">
					@php $page_type = $page_type ?? ''; @endphp
					<li role="presentation" class="c1 {{ $page_type == 'general' ? 'on' : '' }}">
						<a href="javascript:alert('준비중입니다.')" role="tab" aria-selected="{{ $page_type == 'general' ? 'true' : 'false' }}" {!! $page_type == 'general' ? 'aria-current="page"' : '' !!}>일반인</a>
					</li>
					<li role="presentation" class="c2 {{ $page_type == 'professional' ? 'on' : '' }}">
						<a href="/" role="tab" aria-selected="{{ $page_type == 'professional' ? 'true' : 'false' }}" {!! $page_type == 'professional' ? 'aria-current="page"' : '' !!}>전문인</a>
					</li>
					<li role="presentation" class="c3 {{ $page_type == 'online_academy' ? 'on' : '' }}">
						<a href="/online_academy/" role="tab" aria-selected="{{ $page_type == 'online_academy' ? 'true' : 'false' }}" {!! $page_type == 'online_academy' ? 'aria-current="page"' : '' !!}>온라인 아카데미</a>
					</li>
				</ul>
				@if(isset($gNum) && $gNum !== 'online_academy')
				<ul class="member" aria-label="회원 메뉴">
					@guest
					<li class="i1"><a href="{{ route('member.login') }}">로그인</a></li>
					<li class="i2"><a href="{{ route('member.register') }}">회원가입</a></li>
					@else
					<li class="i1">
						<form action="{{ route('member.logout') }}" method="POST" class="member-nav-logout-form">
							@csrf
							<button type="submit" class="member-nav-link">로그아웃</button>
						</form>
					</li>
					@if(auth()->user()?->role === 'user')
					<li class="i2"><a href="{{ route('mypage.profile_edit') }}">마이페이지</a></li>
					@endif
					@endguest
				</ul>
				@endif
			</div>
		</div>
		@if(isset($gNum) && $gNum !== 'online_academy')
		<div class="btm">
			<div class="inner">
				<div class="left">
					<a href="/" class="logo" aria-label="대한기능의학회 홈으로 이동"><img src="/images/logo.png" alt="대한기능의학회 로고"></a>
				</div>
				<div class="center">
					<form method="GET" action="{{ route('total_search') }}" class="search_area">
						<label for="site-search" class="sound_only">검색어 입력</label>
						<input type="search" id="site-search" name="q" class="text" value="{{ request('q', request('keyword', '')) }}" placeholder="검색어를 입력해주세요" autocomplete="off">
						<button type="submit" class="btn" aria-label="검색 실행">검색</button>
					</form>
				</div>
				<div class="right">
					<button type="button" class="btn_search_mobile mo_vw" aria-label="모바일 검색창 열기">검색</button>
					<button type="button" class="btn_chatbot" aria-label="챗봇 열기">챗봇</button>
					<button type="button" class="btn_menu" aria-label="전체 메뉴 열기" aria-controls="main-navi" aria-expanded="false">
						<span class="line t" aria-hidden="true"></span>
						<span class="line b" aria-hidden="true"></span>
					</button>
				</div>
			</div>
		</div>
		<nav class="sitemap" id="main-navi" aria-label="주 메뉴">
			<div class="bg" aria-hidden="true"></div>
			<div class="menu_wrap">
				<ul class="flex inner">
					<li class="menu {{ ($gNum ?? '') == '01' ? 'on' : '' }}">
						<a href="/introduction/overview" id="main-menu-01" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '01' ? 'true' : 'false' }}" @if(($gNum ?? '') == '01') aria-current="page" @endif>학회소개</a>
						<ul class="snb" aria-labelledby="main-menu-01">
							<li><a href="/introduction/overview" @if(($gNum ?? '') == '01' && ($sNum ?? '') == '01') class="on" aria-current="page" @endif>학회개요</a></li>
							<li><a href="/introduction/greeting" @if(($gNum ?? '') == '01' && ($sNum ?? '') == '02') class="on" aria-current="page" @endif>인사말</a></li>
							<li><a href="/introduction/history" @if(($gNum ?? '') == '01' && ($sNum ?? '') == '03') class="on" aria-current="page" @endif>학회 연혁</a></li>
							<li><a href="/introduction/bylaws" @if(($gNum ?? '') == '01' && ($sNum ?? '') == '04') class="on" aria-current="page" @endif>회칙</a>
								<ul class="depth">
									<li class="{{ ($gNum == '01' && $sNum == '04' && ($dNum ?? '') == '01') ? 'on' : '' }}"><a href="/introduction/bylaws" @if($gNum == '01' && $sNum == '04' && ($dNum ?? '') == '01') aria-current="page" @endif>- 대한기능의학회 회칙</a></li>
									<li class="{{ ($gNum == '01' && $sNum == '04' && ($dNum ?? '') == '02') ? 'on' : '' }}"><a href="/introduction/bylaws_operation" @if($gNum == '01' && $sNum == '04' && ($dNum ?? '') == '02') aria-current="page" @endif>- 업무 및 운영 내규</a></li>
									<li class="{{ ($gNum == '01' && $sNum == '04' && ($dNum ?? '') == '03') ? 'on' : '' }}"><a href="/introduction/bylaws_protocol" @if($gNum == '01' && $sNum == '04' && ($dNum ?? '') == '03') aria-current="page" @endif>- 업무 프로토콜</a></li>
								</ul>
							</li>
							<li><a href="/introduction/officers" @if(($gNum ?? '') == '01' && ($sNum ?? '') == '05') class="on" aria-current="page" @endif>임원진</a></li>
							<li><a href="/introduction/location" @if(($gNum ?? '') == '01' && ($sNum ?? '') == '06') class="on" aria-current="page" @endif>오시는 길</a></li>
						</ul>
					</li>
					<li class="menu {{ ($gNum ?? '') == '02' ? 'on' : '' }}">
						<a href="/academic_event/annual_schedule" id="main-menu-02" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '02' ? 'true' : 'false' }}" @if(($gNum ?? '') == '02') aria-current="page" @endif>학술행사</a>
						<ul class="snb" aria-labelledby="main-menu-02">
							<li><a href="/academic_event/annual_schedule" @if(($gNum ?? '') == '02' && ($sNum ?? '') == '01') class="on" aria-current="page" @endif>KIFM 연간일정</a></li>
							<li><a href="/academic_event/academic_history" @if(($gNum ?? '') == '02' && ($sNum ?? '') == '02') class="on" aria-current="page" @endif>학술대회 연혁</a></li>
							<li><a href="/academic_event/conference" @if(($gNum ?? '') == '02' && ($sNum ?? '') == '03') class="on" aria-current="page" @endif>학술대회</a></li>
							<li><a href="/academic_event/training_course" @if(($gNum ?? '') == '02' && ($sNum ?? '') == '04') class="on" aria-current="page" @endif>연수강좌</a></li>
						</ul>
					</li>
					<li class="menu {{ ($gNum ?? '') == '03' ? 'on' : '' }}"><a href="/subcommittee" id="main-menu-03" @if(($gNum ?? '') == '03') aria-current="page" @endif>산하위원회</a></li>
					<li class="menu {{ ($gNum ?? '') == '04' ? 'on' : '' }}">
						<a href="/archives/general" id="main-menu-04" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '04' ? 'true' : 'false' }}" @if(($gNum ?? '') == '04') aria-current="page" @endif>학회 자료실</a>
						<ul class="snb" aria-labelledby="main-menu-04">
							<li><a href="/archives/general"  @if(($gNum ?? '') == '04' && ($sNum ?? '') == '01') class="on" aria-current="page" @endif>일반 자료실</a></li>
							<li><a href="/archives/academic" @if(($gNum ?? '') == '04' && ($sNum ?? '') == '02') class="on" aria-current="page" @endif>학술 자료실</a></li>
							<li><a href="/archives/members"  @if(($gNum ?? '') == '04' && ($sNum ?? '') == '03') class="on" aria-current="page" @endif>회원 자료실</a></li>
							<li><a href="/archives/journals" @if(($gNum ?? '') == '04' && ($sNum ?? '') == '04') class="on" aria-current="page" @endif>학술지</a></li>
						</ul>
					</li>
					<li class="menu {{ ($gNum ?? '') == '05' ? 'on' : '' }}">
						<a href="/member_plaza/society_notices" id="main-menu-05" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '05' ? 'true' : 'false' }}" @if(($gNum ?? '') == '05') aria-current="page" @endif>회원광장</a>
						<ul class="snb" aria-labelledby="main-menu-05">
							<li><a href="/member_plaza/society_notices" @if(($gNum ?? '') == '05' && ($sNum ?? '') == '01') class="on" aria-current="page" @endif>학회 공지</a></li>
							<li><a href="/member_plaza/other_notices" @if(($gNum ?? '') == '05' && ($sNum ?? '') == '02') class="on" aria-current="page" @endif>기타 공지</a></li>
							<li><a href="/member_plaza/society_album" @if(($gNum ?? '') == '05' && ($sNum ?? '') == '03') class="on" aria-current="page" @endif>학회 앨범</a></li>
							<li><a href="/member_plaza/fee_payment_guide" @if(($gNum ?? '') == '05' && ($sNum ?? '') == '04') class="on" aria-current="page" @endif>회비 납부 안내</a></li>
						</ul>
					</li>
					<li class="menu {{ ($gNum ?? '') == '06' ? 'on' : '' }}"><a href="/our_neighborhood_doctor" id="main-menu-06" @if(($gNum ?? '') == '06') aria-current="page" @endif>우리동네 주치의</a></li>
					<li class="menu {{ ($gNum ?? '') == '07' ? 'on' : '' }}"><a href="/online_academy" id="main-menu-07" @if(($gNum ?? '') == '07') aria-current="page" @endif>온라인 아카데미</a></li>
				</ul>
				<button type="button" class="btn_menu_close" aria-label="전체 메뉴 닫기">전체 메뉴 닫기</button>
			</div>
		</nav>
		@endif
	</header>
	<!-- //일반인, 전문인, 온라인 아카데미 -->
    @endif
	@if(isset($gNum) && $page_type == 'academic_conference' && $gNum !== 'intro')
	<!-- 학술대회 -->
	@php $academicConferenceBaseUrl = $conferenceBaseUrl ?? url('/academic_conference'); @endphp
	<header class="header {{ (isset($gNum) && $gNum == 'main') ? 'main' : '' }}">
		<div class="inner">
			<a href="{{ $academicConferenceBaseUrl }}" class="logo" aria-label="대한기능의학회 학술대회 홈으로 이동"><img src="/images/logo.png" alt="대한기능의학회 로고"></a>
			<ul class="gnb">
				<li class="menu {{ ($gNum ?? '') == '01' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/invitation" id="academic-menu-01" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '01' ? 'true' : 'false' }}" @if(($gNum ?? '') == '01') aria-current="page" @endif>KIFM</a>
				</li>
				<li class="menu {{ ($gNum ?? '') == '02' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/program" id="academic-menu-02" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '02' ? 'true' : 'false' }}" @if(($gNum ?? '') == '02') aria-current="page" @endif>Program</a>
				</li>
				<li class="menu {{ ($gNum ?? '') == '03' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/registration" id="academic-menu-03" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '03' ? 'true' : 'false' }}" @if(($gNum ?? '') == '03') aria-current="page" @endif>Registration</a>
				</li>
				<li class="menu {{ ($gNum ?? '') == '04' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/abstract" id="academic-menu-04" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '04' ? 'true' : 'false' }}" @if(($gNum ?? '') == '04') aria-current="page" @endif>Abstract</a>
				</li>
				<li class="menu {{ ($gNum ?? '') == '05' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/notice" id="academic-menu-05" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '05' ? 'true' : 'false' }}" @if(($gNum ?? '') == '05') aria-current="page" @endif>Notice</a>
				</li>
				<li class="menu {{ ($gNum ?? '') == '06' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/sponsors" id="academic-menu-06" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '06' ? 'true' : 'false' }}" @if(($gNum ?? '') == '06') aria-current="page" @endif>Sponsors</a>
				</li>
				<li class="menu {{ ($gNum ?? '') == '07' ? 'on' : '' }}">
					<a href="{{ $academicConferenceBaseUrl }}/exhibition" id="academic-menu-07" aria-haspopup="true" aria-expanded="{{ ($gNum ?? '') == '07' ? 'true' : 'false' }}" @if(($gNum ?? '') == '07') aria-current="page" @endif>Exhibition</a>
				</li>
			</ul>
			<div class="member">
				@guest
				<a href="{{ route('member.login') }}" class="btn i1">로그인</a>
				@else
				@if(auth()->user()?->role === 'user')
				<a href="{{ route('mypage.profile_edit') }}" class="btn i1">마이페이지</a>
				@endif
				<form action="{{ route('member.logout') }}" method="POST" class="member-nav-logout-form member-nav-logout-form--academic">
					@csrf
					<button type="submit" class="btn i2">로그아웃</button>
				</form>
				@endguest
			</div>
		</div>
	</header>
	<!-- //학술대회 -->
	@endif
	<div class="container_wrap {{ (isset($gNum) && $gNum == 'main') ? 'main' : '' }} {{ (isset($gNum) && $gNum !== 'main') ? 'sub_wrap' : '' }} {{ (isset($gNum) && $gNum !== 'main') ? 'g'.$gNum : '' }} {{ (isset($gNum) && $gNum == 'intro') ? 'mt0' : '' }} {{ (isset($gNum) && $gNum == 'print') ? 'print_wrap' : '' }} {{ (isset($page_type) && $page_type == 'online_academy') ? 'online_academy_wrap' : '' }} {{ (isset($gNum) && $page_type == 'academic_conference') ? 'academic_conference_wrap' : '' }} {{ (isset($gNum) && $page_type == 'academic_conference' && $gNum == 'main') ? 'main' : '' }}">
		@if(isset($gNum) && $gNum !== 'main' && $gNum !== 'intro' && $gNum !== 'print' && $gNum !== 'total_search')
		<div class="svisual g{{ $gNum }} s{{ $sNum }} {{ ($gNum == 'online_academy' && $sNum != '00') ? 'hide' : '' }}">
			@if(isset($gNum) && $gNum !== 'online_academy')
			
			<div class="title inner">
				<strong>{{ $gName }}</strong>
				@if(isset($geName) && $geName)
					<span>{{ str_replace(['_view', '_list'], '', $geName) }}</span>
				@endif
				<div class="location">
					<a href="/home" class="home" aria-label="홈으로 이동">홈으로</a>
					<span>{{ $gName }}</span>
					<span>{{ $sName }}</span>
				</div>
				@yield('svisual_other')
			</div>
			
			
			@endif
			@if(isset($gNum) && $gNum == 'online_academy')
			<div class="title inner">
				<strong>대한기능의학회 <br class="mo_vw">온라인 아카데미</strong>
				<span>기능의학에 대한 심도있고 <br class="mo_vw">다양한 강의 및 교육 자료를 제공해드립니다.<br/>강의 수강 가능 여부 및 기간은 회원 등급별로 <br class="mo_vw">다르게 운영되고 있습니다.</span>
				<form method="GET" action="{{ route('total_search') }}" class="search_area">
					<label for="online-total-search" class="sound_only">검색어 입력</label>
					<input type="search" id="online-total-search" name="q" class="text" value="{{ request('q', request('keyword', '')) }}" placeholder="학술지, 게시글, 키워드 등을 입력해 주세요.">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
			@endif
		</div>
		@endif
		
		@if(isset($gNum) && $gNum !== 'intro' && $gNum !== 'main' && $gNum !== '98' && $gNum !== 'total_search' && $page_type !== 'academic_conference')
		<nav class="sub_menu_area inner" id="sub-navi" aria-label="서브 메뉴">
			<div class="menu set_g">
				<button type="button" class="btn" aria-expanded="false" aria-controls="sub-gnb-list">{{ $gName }}</button>
				<ul id="sub-gnb-list">
					<li class="{{ ($gNum ?? '') == '01' ? 'on' : '' }}"><a href="/introduction/overview" @if(($gNum ?? '') == '01') aria-current="page" @endif>학회소개</a></li>
					<li class="{{ ($gNum ?? '') == '02' ? 'on' : '' }}"><a href="/academic_event/annual_schedule" @if(($gNum ?? '') == '02') aria-current="page" @endif>학술행사</a></li>
					<li class="{{ ($gNum ?? '') == '03' ? 'on' : '' }}"><a href="/subcommittee" @if(($gNum ?? '') == '03') aria-current="page" @endif>산하위원회</a></li>
					<li class="{{ ($gNum ?? '') == '04' ? 'on' : '' }}"><a href="/archives/general" @if(($gNum ?? '') == '04') aria-current="page" @endif>학회 자료실</a></li>
					<li class="{{ ($gNum ?? '') == '05' ? 'on' : '' }}"><a href="/member_plaza/society_notices" @if(($gNum ?? '') == '05') aria-current="page" @endif>회원광장</a></li>
					<li class="{{ ($gNum ?? '') == '06' ? 'on' : '' }}"><a href="/our_neighborhood_doctor" @if(($gNum ?? '') == '06') aria-current="page" @endif>우리동네 주치의</a></li>
					<li class="{{ ($gNum ?? '') == '07' ? 'on' : '' }}"><a href="/online_academy" @if(($gNum ?? '') == '07') aria-current="page" @endif>온라인 아카데미</a></li>
				</ul>
			</div>
			<div class="menu set_s">
				<button type="button" class="btn" aria-expanded="false" aria-controls="sub-snb-list" >{{ $sName }}</button>
				<ul id="sub-snb-list">
					@if(isset($gNum) && $gNum == '01')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/introduction/overview" @if(($sNum ?? '') == '01') aria-current="page" @endif>학회개요</a></li>
						<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/introduction/greeting" @if(($sNum ?? '') == '02') aria-current="page" @endif>인사말</a></li>
						<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/introduction/history" @if(($sNum ?? '') == '03') aria-current="page" @endif>학회 연혁</a></li>
						<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/introduction/bylaws" @if(($sNum ?? '') == '04') aria-current="page" @endif>회칙</a></li>
						<li class="{{ ($sNum ?? '') == '05' ? 'on' : '' }}"><a href="/introduction/officers" @if(($sNum ?? '') == '05') aria-current="page" @endif>임원진</a></li>
						<li class="{{ ($sNum ?? '') == '06' ? 'on' : '' }}"><a href="/introduction/location" @if(($sNum ?? '') == '06') aria-current="page" @endif>오시는 길</a></li>
					@endif
					@if(isset($gNum) && $gNum == '02')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/academic_event/annual_schedule" @if(($sNum ?? '') == '01') aria-current="page" @endif>KIFM 연간일정</a></li>
						<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/academic_event/academic_history" @if(($sNum ?? '') == '02') aria-current="page" @endif>학술대회 연혁</a></li>
						<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/academic_event/conference" @if(($sNum ?? '') == '03') aria-current="page" @endif>학술대회</a></li>
						<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/academic_event/training_course" @if(($sNum ?? '') == '04') aria-current="page" @endif>연수강좌</a></li>
					@endif
					@if(isset($gNum) && $gNum == '03')
						<li class="{{ request()->routeIs('subcommittee.index') ? 'on' : '' }}"><a href="{{ route('subcommittee.index') }}" @if(request()->routeIs('subcommittee.index')) aria-current="page" @endif>산하위원회</a></li>
						@foreach ($navCommittees ?? [] as $navC)
							<li class="{{ isset($committee) && $committee->id === $navC->id ? 'on' : '' }}"><a href="{{ route('subcommittee.notice', $navC) }}" @if(isset($committee) && $committee->id === $navC->id) aria-current="page" @endif>{{ $navC->name }}</a></li>
						@endforeach
					@endif
					@if(isset($gNum) && $gNum == '04')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/archives/general" @if(($sNum ?? '') == '01') aria-current="page" @endif>일반 자료실</a></li>
						<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/archives/academic" @if(($sNum ?? '') == '02') aria-current="page" @endif>학술 자료실</a></li>
						<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/archives/members" @if(($sNum ?? '') == '03') aria-current="page" @endif>회원 자료실</a></li>
						<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/archives/journals" @if(($sNum ?? '') == '04') aria-current="page" @endif>학술지</a></li>
					@endif
					@if(isset($gNum) && $gNum == '05')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/member_plaza/society_notices" @if(($sNum ?? '') == '01') aria-current="page" @endif>학회 공지</a></li>
						<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/member_plaza/other_notices" @if(($sNum ?? '') == '02') aria-current="page" @endif>기타 공지</a></li>
						<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/member_plaza/society_album" @if(($sNum ?? '') == '03') aria-current="page" @endif>학회 앨범</a></li>
						<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/member_plaza/fee_payment_guide" @if(($sNum ?? '') == '04') aria-current="page" @endif>회비 납부 안내</a></li>
					@endif
					@if(isset($gNum) && $gNum == '06')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/our_neighborhood_doctor" @if(($sNum ?? '') == '01') aria-current="page" @endif>우리동네 주치의</a></li>
					@endif
					@if(isset($gNum) && $gNum == '07')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/online_academy/" @if(($sNum ?? '') == '01') aria-current="page" @endif>온라인 아카데미</a></li>
					@endif
					@if(isset($gNum) && $gNum == '00')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/member/login" @if(($sNum ?? '') == '01') aria-current="page" @endif>로그인</a></li>
						<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/member/find_id" @if(($sNum ?? '') == '02') aria-current="page" @endif>아이디 찾기</a></li>
						<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/member/find_pw" @if(($sNum ?? '') == '03') aria-current="page" @endif>비밀번호 찾기</a></li>
						<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/member/register" @if(($sNum ?? '') == '04') aria-current="page" @endif>회원가입</a></li>
						
					@endif
					@if(isset($gNum) && $gNum == '99')
						<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/mypage/profile_edit" @if(($sNum ?? '') == '01') aria-current="page" @endif>개인정보 관리</a></li>
						<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/mypage/participation_history" @if(($sNum ?? '') == '02') aria-current="page" @endif>참가내역 관리</a></li>
						<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/mypage/online_training" @if(($sNum ?? '') == '03') aria-current="page" @endif>온라인 교육 수강내역</a></li>
						<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/mypage/inquiry" @if(($sNum ?? '') == '04') aria-current="page" @endif>1:1 문의</a></li>
						<li class="{{ ($sNum ?? '') == '05' ? 'on' : '' }}"><a href="/mypage/favorite" @if(($sNum ?? '') == '05') aria-current="page" @endif>즐겨찾는 메뉴</a></li>
						<li class="{{ ($sNum ?? '') == '06' ? 'on' : '' }}"><a href="/mypage/bookmark" @if(($sNum ?? '') == '06') aria-current="page" @endif>북마크</a></li>
						<li class="{{ ($sNum ?? '') == '07' ? 'on' : '' }}"><a href="/mypage/hospital_information" @if(($sNum ?? '') == '07') aria-current="page" @endif>병원 정보 관리</a></li>
						<li class="{{ ($sNum ?? '') == '08' ? 'on' : '' }}"><a href="/mypage/executive_activities" @if(($sNum ?? '') == '08') aria-current="page" @endif>회원 활동(임원)</a></li>
						<li class="{{ ($sNum ?? '') == '09' ? 'on' : '' }}"><a href="/mypage/committee_participation" @if(($sNum ?? '') == '09') aria-current="page" @endif>위원회 참여 현황</a></li> <!-- 일반회원 -->
						<li class="{{ ($sNum ?? '') == '10' ? 'on' : '' }}"><a href="/mypage/committee_participation_admin" @if(($sNum ?? '') == '10') aria-current="page" @endif>위원회 참여 현황</a></li> <!-- 관리자 회원 -->
					@endif
				</ul>
			</div>

			{{-- 3단계: 상세메뉴 (있을 때만) --}}
			@if(isset($dNum) && $dNum)
			<div class="menu set_d">
				<button type="button" class="btn" aria-expanded="false" aria-controls="sub-dnb-list">{{ $dName }}</button>
				<ul id="sub-dnb-list" class="depth3_menu">
					@if(isset($gNum) && $gNum == '01' && ($sNum ?? '') == '04')
						<li class="{{ ($dNum ?? '') == '01' ? 'on' : '' }}"><a href="/introduction/bylaws" @if(($dNum ?? '') == '01') aria-current="page" @endif>대한기능의학회 회칙</a></li>
						<li class="{{ ($dNum ?? '') == '02' ? 'on' : '' }}"><a href="/introduction/bylaws_operation" @if(($dNum ?? '') == '02') aria-current="page" @endif>업무 및 운영 내규</a></li>
						<li class="{{ ($dNum ?? '') == '03' ? 'on' : '' }}"><a href="/introduction/bylaws_protocol" @if(($dNum ?? '') == '03') aria-current="page" @endif>업무 프로토콜</a></li>
					@endif
					@if(isset($gNum) && $gNum == '03' && isset($committee))
						<li class="{{ request()->routeIs('subcommittee.notice') || request()->routeIs('subcommittee.notice_show') ? 'on' : '' }}"><a href="{{ route('subcommittee.notice', $committee) }}" @if(request()->routeIs('subcommittee.notice') || request()->routeIs('subcommittee.notice_show')) aria-current="page" @endif>공지사항</a></li>
						<li class="{{ request()->routeIs('subcommittee.discussion') || request()->routeIs('subcommittee.discussion_show') || request()->routeIs('subcommittee.discussion_write') ? 'on' : '' }}"><a href="{{ route('subcommittee.discussion', $committee) }}" @if(request()->routeIs('subcommittee.discussion') || request()->routeIs('subcommittee.discussion_show') || request()->routeIs('subcommittee.discussion_write')) aria-current="page" @endif>토론장</a></li>
						<li class="{{ request()->routeIs('subcommittee.archives') || request()->routeIs('subcommittee.archives_show') ? 'on' : '' }}"><a href="{{ route('subcommittee.archives', $committee) }}" @if(request()->routeIs('subcommittee.archives') || request()->routeIs('subcommittee.archives_show')) aria-current="page" @endif>자료실</a></li>
					@endif
				</ul>
			</div>
			@endif
		</nav>
		@endif
		
		@if(isset($gNum) && $page_type == 'academic_conference' && $gNum !== 'intro' && $gNum !== 'main' && $gNum !== '05' && $gNum !== '06' && $gNum !== '07')
		<nav class="sub_menu_area inner">
			<ul id="sub-snb-list">
				@if(isset($gNum) && $gNum == '01')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/invitation" @if(($sNum ?? '') == '01') aria-current="page" @endif>초대의 글</a></li>
					<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/committee" @if(($sNum ?? '') == '02') aria-current="page" @endif>조직위원회</a></li>
					<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/venue" @if(($sNum ?? '') == '03') aria-current="page" @endif>행사장 안내</a></li>
				@endif
				@if(isset($gNum) && $gNum == '02')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/program" @if(($sNum ?? '') == '01') aria-current="page" @endif>program</a></li>
					<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/speakers" @if(($sNum ?? '') == '02') aria-current="page" @endif>speakers</a></li>
				@endif
				@if(isset($gNum) && $gNum == '03')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/registration" @if(($sNum ?? '') == '01') aria-current="page" @endif>Information</a></li>
					<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/registration/reg" @if(($sNum ?? '') == '02') aria-current="page" @endif>Registration</a></li>
					<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/registration/check_member" @if(($sNum ?? '') == '03') aria-current="page" @endif>Registration Check</a></li>
				@endif
				@if(isset($gNum) && $gNum == '04')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/abstract" @if(($sNum ?? '') == '01') aria-current="page" @endif>Information</a></li>
					<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/abstract/submission" @if(($sNum ?? '') == '02') aria-current="page" @endif>Abstract Submission</a></li>
					<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/abstract/check" @if(($sNum ?? '') == '03') aria-current="page" @endif>Abstract Check</a></li>
				@endif
				@if(isset($gNum) && $gNum == '05')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/notice" @if(($sNum ?? '') == '01') aria-current="page" @endif>Notice</a></li>
				@endif
				@if(isset($gNum) && $gNum == '06')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/sponsors" @if(($sNum ?? '') == '01') aria-current="page" @endif>Sponsors</a></li>
				@endif
				@if(isset($gNum) && $gNum == '07')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/exhibition" @if(($sNum ?? '') == '01') aria-current="page" @endif>Exhibition</a></li>
				@endif

				@if(isset($gNum) && $gNum == 'academic_conference')
					<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/onsite_info" @if(($sNum ?? '') == '01') aria-current="page" @endif>Information</a></li>
					<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/onsite_member_registration" @if(($sNum ?? '') == '02') aria-current="page" @endif>Registration</a></li>
					<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ $academicConferenceBaseUrl }}/onsite_check_registration" @if(($sNum ?? '') == '03') aria-current="page" @endif>Registration Check</a></li>
				@endif
			</ul>
		</nav>
		@endif

		@if(session('alert'))
			<div data-global-page-alert="{{ session('alert') }}"></div>
		@endif
		@yield('content')
	</div> 
	
	@if(isset($gNum) && $gNum !== 'intro' && $gNum !== 'print')
    <!-- 팝업 영역 -->
    @yield('popups')
    @if(isset($committeePopups) && $committeePopups->isNotEmpty())
        @include('subcommittee.partials.committee_ad_popups', ['committeePopups' => $committeePopups])
    @endif
    <script src="{{ asset('js/frontend/popup.js') }}"></script>
    @if(($gNum ?? '') == '03')
    <script src="{{ asset('js/frontend/committee-popups.js') }}"></script>
    @endif
    @stack('scripts')
	
	@if(isset($gNum) && $gNum !== 'intro' && $gNum !== 'print' && $page_type!== 'academic_conference')
	<!-- 일반인, 전문인, 온라인 아카데미 -->
	<footer class="footer">
		<div class="point" aria-hidden="true"></div>
		<div class="quick">
			<a href="https://www.youtube.com/@%EB%8C%80%ED%95%9C%EA%B8%B0%EB%8A%A5%EC%9D%98%ED%95%99%ED%9A%8C" target="_blank" aria-label="새창으로 이동" class="btn btn_youtube">대한기능의학회 유튜브로 이동</a>
			<button type="button" class="btn gotop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" aria-label="페이지 맨 위로 이동" aria-hidden="true">TOP</button>
		</div>
	
		<section class="fbanner {{ ($gNum ?? '') == '01' && ($sNum ?? '') == '01' ? 'mt0' : '' }}" aria-label="배너 슬라이드">
			<div class="inner">
				<div class="fbanner_slide" id="fbanner-swiper">
					<ul class="swiper-wrapper" role="list">
						<li class="swiper-slide" role="listitem"><img src="/images/img_fbanner01.png" alt="아주대학교병원"></li>
						<li class="swiper-slide" role="listitem"><img src="/images/img_fbanner02.png" alt="영남대학교병원"></li>
						<li class="swiper-slide" role="listitem"><img src="/images/img_fbanner03.png" alt="강북삼성병원"></li>
						<li class="swiper-slide" role="listitem"><img src="/images/img_fbanner04.png" alt="반에이치의원"></li>
						<li class="swiper-slide" role="listitem"><img src="/images/img_fbanner05.png" alt="첨단재생의료산업협회"></li>
						<li class="swiper-slide" role="listitem"><img src="/images/img_fbanner06.png" alt="녹십자아이메드"></li>
					</ul>
				</div>
				<div class="fbanner_controls" aria-label="슬라이드 컨트롤">
					<button type="button" class="arrow prev" aria-label="이전 슬라이드"></button>
					<button type="button" class="arrow next" aria-label="다음 슬라이드"></button>
					<button type="button" class="papl" aria-label="슬라이드 정지" aria-pressed="false"></button>
				</div>
			</div>
		</section>
		
		<section class="info" aria-label="대한기능의학회 연락처 정보">
			<div class="inner">
				<div class="address_area">
					<address>
						<ul class="office_info" aria-label="회사 정보">
							<li class="w100p"><strong class="sound_only">주소</strong> <span>경기도 수원시 영통구 월드컵로 164 (원천동, 아주대학병원) 1031호</span></li>
							<li><strong>대표자</strong> <span>김범택</span></li>
							<li><strong>사업자번호</strong> <span>26-82-00017</span></li>
							<li><strong>전화번호</strong> <span><a href="tel:01084414884">010-8441-4884</a></span></li>
						</ul>
					</address>
					<p class="copyright">Copyright ⓒ Korean Society for Functional Medicine. All rights reserved.</p>
					<nav class="footer_menus" aria-label="푸터 메뉴">
						<ul class="flex">
							<li><a href="/terms/privacy_policy">개인정보처리방침</a></li>
							<li><a href="/terms/email_collection_refusal">이메일무단수집거부</a></li>
						</ul>
					</nav>
				</div>
			</div>
		</section>
	</footer>
	<!-- //일반인, 전문인, 온라인 아카데미 -->
	@endif
	
	@if(isset($gNum) && $page_type == 'academic_conference' && $gNum !== 'intro')
	<!-- 학술대회 -->
	<footer class="footer">
		<div class="point" aria-hidden="true"></div>
		<div class="quick">
			<button type="button" class="btn gotop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" aria-label="페이지 맨 위로 이동" aria-hidden="true">TOP</button>
		</div>
		<section class="info" aria-label="대한기능의학회 연락처 정보">
			<div class="inner">
				<div class="address_area">
					<address>
						<ul class="office_info" aria-label="회사 정보">
							<li class="w100p"><strong class="sound_only">주소</strong> <span>경기도 수원시 영통구 월드컵로 164 (원천동, 아주대학병원) 1031호</span></li>
							<li><strong>대표자 : </strong> <span>김범택</span></li>
							<li><strong>사업자번호 : </strong> <span>26-82-00017</span></li>
							<li><strong>전화번호 : </strong> <span><a href="tel:01084414884">010-8441-4884</a></span></li>
						</ul>
					</address>
					<nav class="footer_menus" aria-label="푸터 메뉴">
						<ul class="flex">
							<li><a href="/terms/privacy_policy"><strong class="c_red">개인정보처리방침</strong></a></li>
							<li><a href="/terms/email_collection_refusal">이메일무단수집거부</a></li>
						</ul>
					</nav>
					<p class="copyright">Copyright ⓒ Korean Society for Functional Medicine. All rights reserved.</p>
				</div>
				<div class="contact">
					<div class="tit">기능의학회 사무국</div>
					<ul>
						<li><label for="footer_tel">TEL</label><a href="tel:01084414484" id="footer_tel">010-8441-4484</a></li>
						<li><label for="footer_email">TEL</label><a href="mailto:0182253645@naver.com" id="footer_tel">0182253645@naver.com</a></li>
					</ul>
				</div>
			</div>
		</section>
	</footer>
	<!-- //학술대회 -->
	@endif
	
	@endif
	@if(($gNum ?? '') == 'print')
	@stack('scripts')
	@endif
</body>
</html>

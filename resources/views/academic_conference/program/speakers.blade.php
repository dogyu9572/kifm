@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="speakers-heading">
	<div class="inner">
		<h1 class="sub_title" id="speakers-heading">{{ $sName }}</h1>
		
		<div class="board_top">
			<div class="left">&nbsp;</div>
			<div class="right flex">
				<form class="search_area">
					<label for="event-search" class="sound_only">검색어 검색</label>
					<input type="text" id="event-search" class="text" placeholder="검색어를 입력하세요">
					<button type="submit" class="btn_search">검색</button>
				</form>
			</div>
		</div>
		
		<ul class="speakers_area">
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_noimage.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
			<li>
				<button type="button" class="speakers_btn" onclick="layerShow('pop_speakers');">
					<span class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers.jpg" alt=""></span>
					<span class="txt">
						<span class="top">
							<span class="session">session 1</span>
							<span class="time"><time datetime="09:00">09:00</time> ~ <time datetime="09:30">09:30</time></span>
						</span>
						<h2>개원가에서 활용 가능한 <br>줄기세포 기반 치료의 현주소</h2>
						<p class="position">용인세브란스병원 가정의학과</p>
						<p class="name">홍길동</p>
						<i class="btn btn_wkk">연자보기</i>
					</span>
				</button>
			</li>
		</ul>
	</div>
</section>

</main>

<div class="popup pop_speakers" id="pop_speakers">
	<div class="dm" onclick="layerHide('pop_speakers');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_speakers');">Close</button>
		<div class="head">
			<div class="imgfit" aria-hidden="true"><img src="/images/img_sample_speakers_pop.jpg" alt=""></div>
			<div class="txt">
				<h2 class="name">홍길동 교수</h2>
				<div class="position">용인세브란스병원 가정의학과</div>
			</div>
		</div>
		<div class="gbox con">
			<dl class="scroll">
				<div>
					<dt>주요 약력</dt>
					<dd>
						<p>2020 - 현재 : 용인세브란스병원 가정의학과 교수</p>
						<p>2018 - 2020 : 대한기능의학회 학술이사</p>
						<p>서울대학교 의과대학 졸업</p>
					</dd>
				</div>
				<div>
					<dt>주요 연구 분야</dt>
					<dd>
						<p>줄기세포 기반 치료</p>
						<p>만성 질환 관리 및 기능 의학</p>
					</dd>
				</div>
				<div>
					<dt>주요 연구 분야</dt>
					<dd>
						<p>줄기세포 기반 치료</p>
						<p>만성 질환 관리 및 기능 의학</p>
					</dd>
				</div>
				<div>
					<dt>주요 연구 분야</dt>
					<dd>
						<p>줄기세포 기반 치료</p>
						<p>만성 질환 관리 및 기능 의학</p>
					</dd>
				</div>
			</dl>
		</div>
		<div class="btns flex_center"><a href="/member/dormant_auth" class="btn btn_wkk">닫기</a></div>
	</div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/script_popup.js') }}"></script>
@endpush
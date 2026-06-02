@php
    $cert = $certification ?? [
        'conference_count' => 0,
        'conference_required' => 3,
        'conference_short' => 3,
        'online_academy_completed' => false,
        'membership_fee_paid' => false,
        'progress_percent' => 0,
    ];
    $conferenceCount = (int) $cert['conference_count'];
    $conferenceRequired = (int) $cert['conference_required'];
    $conferenceShort = (int) $cert['conference_short'];
@endphp
<section class="box right" aria-labelledby="certification-status-title">
    <h2 class="mytit" id="certification-status-title">인정의 유지</h2>
	<!-- 개인정보 관리 -->
    <div class="glbox participation_area" data-progress-percent="{{ $cert['progress_percent'] }}">
        <div class="box w100p">
			<div class="info">
	    <div class="l" id="participation-label">학술대회 참가 <span class="c_iden count"><strong>{{ $conferenceCount }}</strong>/{{ $conferenceRequired }}회</span></div>
	    <div class="r">
	        @if ($conferenceShort > 0)
	            <p class="excl_rev">{{ $conferenceShort }}회 부족</p>
	        @endif
	    </div>
	</div>
	<div class="state_line" role="progressbar" aria-labelledby="participation-label" aria-valuenow="{{ $conferenceCount }}" aria-valuemin="0" aria-valuemax="{{ $conferenceRequired }}">
	    <div class="bar"></div>
	</div>
	@if ($conferenceShort > 0)
	    <p class="excl c_black">
	        <span class="sound_only">알림: </span>
	        인증의 자격 유지를 위해 <strong class="c_red">학술대회 {{ $conferenceShort }}회 추가 참석이 필요합니다.</strong>
	    </p>
	@endif
		</div>
    </div>
	<!-- 인정의 수강 -->
    <div class="glbox participation_area participation_course" data-progress-percent="{{ $cert['progress_percent'] }}">
        <div class="info">
            <div class="l" id="participation-label">인증의 취득 요건 현황 <p>인증의 취득을 위해 아래 조건을 충족해주세요</p></div>
        </div>
        <div class="slice_half">
    			<div class="box flex_center">
    				<div class="tt">정기 연수강좌(2회)</div>
    				<div class="flex">
    					<a href="#this" class="btn btn_wbb">짝수년</a>
    					<a href="#this" class="btn btn_ggg">홀수년</a>
    				</div>
    			</div>
    			<div class="box flex_center">
    				<div class="tt">동계 연수강좌(1회)</div>
    				<div class="flex">
    					<a href="#this" class="btn btn_wrr w100p">강좌 미수료</a>
    				</div>
    			</div>
    		</div>
    </div>
	<!-- 인정의 유지 -->
    <div class="glbox participation_area" data-progress-percent="{{ $cert['progress_percent'] }}">
        <div class="info">
            <div class="l gap0" id="participation-label">자격 유효기간 <span class="day">(2026.04 - 2031.03)</span><p>인증의 갱신을 위해 아래 조건을 충족해주세요</p></div>
            <div class="r d_day btn_wbb">D-1240</div>
        </div>
        <div class="slice_half ptb6">
    			<div class="box">
    				<div class="tt mb0">학술 행사 참여 <div class="count"><strong class="c_iden">2</strong>/4회</div></div>
    				<div class="state_line blue_line"><div class="bar"></div></div>
    			</div>
    			<div class="box">
    				<div class="tt mb0">동계 연수강좌 <div class="count"><strong class="c_iden">1</strong>/3차시</div></div>
    				<div class="state_line red_line"><div class="bar"></div></div>
    			</div>
    		</div>
    </div>

    <ul class="gbox state_tri">
        <li class="i1">
            <h3>학술대회</h3>
            <p><strong>{{ $conferenceCount }}회</strong> <span class="sound_only">중</span> / {{ $conferenceRequired }}회</p>
        </li>
        <li class="i2">
            <h3>온라인 아카데미</h3>
            <p><span class="sound_only">상태: </span><strong>{{ ! empty($cert['online_academy_completed']) ? '수료' : '미수료' }}</strong></p>
        </li>
        <li class="i3">
            <h3>회비</h3>
            <p><span class="sound_only">상태: </span><strong>{{ ! empty($cert['membership_fee_paid']) ? '납부완료' : '미납' }}</strong></p>
        </li>
    </ul>
</section>

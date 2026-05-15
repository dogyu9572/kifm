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
    <h2 class="mytit" id="certification-status-title">인증의 유지</h2>
    <div class="glbox participation_area" data-progress-percent="{{ $cert['progress_percent'] }}">
        <div class="info">
            <div class="l" id="participation-label">학술대회 참가 <span class="c_iden"><strong>{{ $conferenceCount }}</strong>/{{ $conferenceRequired }}회</span></div>
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

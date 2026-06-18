@php
    $cert = $certification ?? [
        'has_certified_member' => false,
        'acquisition' => [],
        'renewal' => [],
        'progress_percent' => 0,
    ];
    $acquisition = $cert['acquisition'] ?? [];
    $renewal = $cert['renewal'] ?? [];
@endphp
<section class="box right" aria-labelledby="certification-status-title">
    <h2 class="mytit" id="certification-status-title">인정의 유지</h2>
    @if (empty($cert['has_certified_member']))
        <div class="glbox participation_area participation_course" data-progress-percent="{{ $cert['progress_percent'] }}">
            <div class="info">
                <div class="l" id="certification-acquisition-label">인정의 취득 요건 현황 <p>인정의 취득을 위해 아래 조건을 충족해주세요</p></div>
            </div>
            <div class="slice_half">
                <div class="box flex_center">
                    <div class="tt">정기 연수강좌 <div class="count"><strong class="c_iden">{{ $acquisition['regular_count'] ?? 0 }}</strong>/{{ $acquisition['regular_required'] ?? 2 }}회</div></div>
                    <div class="flex">
                        <span class="btn {{ ! empty($acquisition['regular_even_completed']) ? 'btn_wbb' : 'btn_ggg' }}">짝수년</span>
                        <span class="btn {{ ! empty($acquisition['regular_odd_completed']) ? 'btn_wbb' : 'btn_ggg' }}">홀수년</span>
                    </div>
                </div>
                <div class="box flex_center">
                    <div class="tt">동계 연수강좌 <div class="count"><strong class="c_iden">{{ $acquisition['winter_count'] ?? 0 }}</strong>/1회</div></div>
                    <div class="flex">
                        <span class="btn {{ ! empty($acquisition['winter_completed']) ? 'btn_wbb' : 'btn_wrr' }} w100p">{{ ! empty($acquisition['winter_completed']) ? '강좌 수료' : '강좌 미수료' }}</span>
                    </div>
                </div>
            </div>
            <p class="excl c_black">
                <span class="sound_only">알림: </span>
                {{ ! empty($acquisition['exam_eligible']) ? '시험 응시 가능 상태입니다.' : '정기 연수강좌와 동계 연수강좌 조건 충족 후 시험 응시가 가능합니다.' }}
            </p>
        </div>
    @else
        <div class="glbox participation_area" data-progress-percent="{{ $cert['progress_percent'] }}">
            <div class="info">
                <div class="l gap0" id="certification-renewal-label">자격 유효기간 <span class="day">({{ $renewal['validity_period'] ?? '-' }})</span><p>인정의 갱신을 위해 아래 조건을 충족해주세요</p></div>
                <div class="r d_day btn_wbb">{{ $renewal['d_day_label'] ?? '-' }}</div>
            </div>
            <div class="slice_half ptb6">
                <div class="box">
                    <div class="tt mb0">학술 행사 참여 <div class="count"><strong class="c_iden">{{ $renewal['general_count'] ?? 0 }}</strong>/{{ $renewal['general_required'] ?? 4 }}회</div></div>
                    <div class="state_line blue_line"><div class="bar"></div></div>
                </div>
                <div class="box">
                    <div class="tt mb0">동계 연수강좌 <div class="count"><strong class="c_iden">{{ $renewal['winter_count'] ?? 0 }}</strong>/{{ $renewal['winter_required'] ?? 1 }}회</div></div>
                    <div class="state_line red_line"><div class="bar"></div></div>
                </div>
            </div>
        </div>
    @endif

</section>

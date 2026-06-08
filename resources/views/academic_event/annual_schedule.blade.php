@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section
	class="scon annual_schedule_wrap"
	aria-labelledby="conference-heading"
	data-annual-schedule
	data-annual-schedules='@json($annualSchedules ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
	data-initial-year="{{ $initialYear ?? now()->year }}"
	data-initial-month="{{ $initialMonth ?? now()->month }}"
>
	<div class="inner">
		<h1 class="sub_title" id="conference-heading">{{ $sName }}</h1>

		<div class="schedule_wrap">
			<div class="schedule_top">
				<div class="years">
					<strong>{{ $initialYear ?? now()->year }}</strong>
					<button type="button" class="btn prev">이전</button>
					<button type="button" class="btn next">다음</button>
				</div>
				<div class="month">
					<button type="button">1월</button>
					<button type="button">2월</button>
					<button type="button">3월</button>
					<button type="button">4월</button>
					<button type="button">5월</button>
					<button type="button">6월</button>
					<button type="button">7월</button>
					<button type="button">8월</button>
					<button type="button">9월</button>
					<button type="button">10월</button>
					<button type="button">11월</button>
					<button type="button">12월</button>
				</div>
				<div class="tag">

					<!-- <label><input type="radio" name="annual_schedule_type" value="all" checked><span>전체</span></label> -->
					<label>
<input type="radio" name="annual_schedule_type" value="academic_conference">
<span class="c1">학술대회</span>
</label>

					<label>
<input type="radio" name="annual_schedule_type" value="training_course">
<span class="c2">연수강좌</span>
</label>

				</div>

			</div>
		</div>
		
		<div class="schedule_month_tbl">
			<table>
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
		
		<div class="mo_vw mo_schedule_month_list">
			<h2 class="sound_only">일정 목록</h2>
			<ul id="mo-schedule-list"></ul>
		</div>

	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/annual-schedule.js') }}"></script>
@endpush

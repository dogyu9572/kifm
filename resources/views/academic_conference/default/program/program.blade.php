@extends('layouts.frontend')
@inject('conference', 'App\Services\Frontend\PublicAcademicConferenceService')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$sessionGroups = $conference->programSessionGroups($event->sessions);
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="program-heading">
	<div class="inner">
		<h1 class="sub_title" id="program-heading">{{ $sName }}</h1>

		<div class="program_area" data-program-area>
			@if ($sessionGroups->isNotEmpty())
				<ul class="tabs full_line">
					@foreach ($sessionGroups as $group)
						<li><a href="#session{{ $group['session']->id }}" data-program-tab>{{ $group['session']->name }}</a></li>
					@endforeach
				</ul>

				<div class="session_wrap">
					@foreach ($sessionGroups as $group)
						<section class="session_area" id="session{{ $group['session']->id }}">
							<div class="session">session {{ $loop->iteration }}</div>
							<h2>{{ $group['session']->name }}</h2>
							@if ($group['chairs'] !== '')
								<div class="chair">Chair : {{ $group['chairs'] }}</div>
							@endif
							<ul class="session_list">
								@foreach ($group['items'] as $item)
									@php
										$startTime = $conference->timeText($item->start_time);
										$endTime = $conference->timeText($item->end_time);
										$title = $conference->programItemTitle($item);
										$presenter = $conference->programItemPresenter($item);
										$description = $conference->programItemDescription($item);
									@endphp
									<li>
										@if ($startTime !== '' || $endTime !== '')
											<div class="time">
												@if ($startTime !== '')
													<time datetime="{{ $conference->timeMachine($item->start_time) }}">{{ $startTime }}</time>
												@endif
												@if ($startTime !== '' && $endTime !== '')
													~
												@endif
												@if ($endTime !== '')
												<time datetime="{{ $conference->timeMachine($item->end_time) }}">{{ $endTime }}</time>
											@endif
										</div>
									@endif
										<strong>{{ $title }}</strong>
										@if ($presenter !== '')
											<div class="presentation">{{ $presenter }}</div>
										@endif
										@if ($description !== '')
											<div>{!! $description !!}</div>
										@endif
									</li>
								@endforeach
							</ul>
						</section>
					@endforeach
				</div>
			@else
				<div class="session_wrap">
					<section class="session_area">
						<h2 class="tac w100p">등록된 프로그램이 없습니다.</h2>
					</section>
				</div>
			@endif
		</div>

		<div class="btns_btm">
			<a href="{{ $conferenceBaseUrl }}/registration/reg" class="btn btn_wbb">사전등록 바로가기</a>
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-program.js') }}"></script>
@endpush

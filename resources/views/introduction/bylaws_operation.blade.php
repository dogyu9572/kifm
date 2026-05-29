@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="bylaws-heading">
	<div class="inner">
		<h1 class="sub_title" id="bylaws-heading">{{ $dName }}</h1>

		<div class="bylaws_top">
			<form method="GET" action="{{ route('introduction.bylaws_operation') }}" data-committee-bylaws-form>
				<label for="committee-regulation-select" class="sound_only">위원회 선택</label>
				<select name="committee_id" id="committee-regulation-select" class="text" data-committee-bylaws-select>
					<option value="">위원회를 선택해 주세요</option>
					@foreach ($committees as $committee)
						<option value="{{ $committee->id }}" @selected($selectedCommitteeId === $committee->id)>{{ $committee->name }}</option>
					@endforeach
				</select>
			</form>
		</div>
		<div class="bylaws_wrap">
			@if ($selectedCommittee)
				@if (trim((string) $selectedCommittee->regulation) !== '')
					{!! $selectedCommittee->regulation !!}
				@else
					<article class="bylaws_item">
						<div class="con">
							<p>등록된 업무 및 운영 내규가 없습니다.</p>
						</div>
					</article>
				@endif
			@elseif ($committees->isEmpty())
				<article class="bylaws_item">
					<div class="con">
						<p>등록된 위원회가 없습니다.</p>
					</div>
				</article>
			@endif
		</div>

	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/introduction-bylaws.js') }}"></script>
@endpush

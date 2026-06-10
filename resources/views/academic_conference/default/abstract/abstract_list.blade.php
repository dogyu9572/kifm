@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>

		<div class="board_list tbl_break_list">
			<table>
				<colgroup>
					<col class="abstract1">
					<col class="abstract2">
					<col class="abstract3">
					<col class="abstract4">
					<col class="abstract5">
					<col class="abstract6">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">접수번호</th>
						<th scope="col">접수 일시</th>
						<th scope="col">발표 구분</th>
						<th scope="col">발표 분야</th>
						<th scope="col">초록 제목</th>
						<th scope="col">상세 보기</th>
					</tr>
				</thead>
				<tbody>
					@foreach(($abstracts ?? collect()) as $abstract)
						<tr>
							<td class="abstract1">{{ $abstract->abstract_no ?: ('ABS-' . $abstract->id) }}</td>
							<td class="abstract2">{{ optional($abstract->submitted_at)->format('Y.m.d H:i') ?: '-' }}</td>
							<td class="abstract3">{{ $abstractPresentationTypes[$abstract->presentation_type] ?? $abstract->presentation_type }}</td>
							<td class="abstract4">{{ $abstract->field?->name ?: '-' }}</td>
							<td class="abstract5 tal">{{ \Illuminate\Support\Str::limit($abstract->title, 80) }}</td>
							<td class="abstract6"><a href="{{ $conferenceBaseUrl }}/abstract/result?abstract_id={{ $abstract->id }}" class="btn btn_kwk">상세보기</a></td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</section>

</main>
@endsection

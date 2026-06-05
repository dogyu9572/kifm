@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$isMemberAbstract = (bool) $abstract->member_id;
	$selectedPresentationType = old('presentation_type', $abstract->presentation_type);
	$selectedFieldId = old('academic_event_field_id', $abstract->academic_event_field_id);
@endphp
<main class="sub_area">

<section class="scon abstract_form_wrap" aria-labelledby="abstract-form-heading">
	<div class="inner">
		<h1 class="sub_title mb0" id="abstract-form-heading">{{ $sName }}</h1>
			
		<div class="member_inbox">
			<div class="inbox input_wrap">
				@if ($errors->any())
					<div class="gbox">
						<p class="c_red" role="alert">입력 내용을 확인해주세요.</p>
					</div>
				@endif
				@if (! $canModifyAbstract)
					<div class="gbox">
						<p class="c_red" role="alert">초록 수정 기간이 종료되었습니다.</p>
					</div>
				@endif
				<form action="{{ route('academic_conference.site.abstract.update', [$event->folder_name, $abstract]) }}" method="post" enctype="multipart/form-data" data-abstract-form>
					@csrf
					@method('PUT')
					<fieldset>
						<legend class="form_tit mt0">제출자 정보</legend>
						<ul class="inputs float">
							<li>
								<label for="user_name" class="tit">이름(국문)</label>
								<input type="text" id="user_name" name="author_name" class="text" value="{{ old('author_name', $abstract->author_name) }}" @readonly($isMemberAbstract)>
								@error('author_name')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="user_name_eng" class="tit">이름(영문)</label>
								<input type="text" id="user_name_eng" name="author_name_en" class="text" value="{{ old('author_name_en', $abstract->author_name_en) }}" @readonly($isMemberAbstract)>
								@error('author_name_en')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="user_phone" class="tit">전화번호</label>
								<input type="tel" id="user_phone" name="author_phone" class="text" value="{{ old('author_phone', $abstract->author_phone) }}" @readonly($isMemberAbstract)>
								@error('author_phone')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="user_tel" class="tit">휴대폰번호</label>
								<input type="tel" id="user_tel" name="author_mobile" class="text" value="{{ old('author_mobile', $abstract->author_mobile) }}" @readonly($isMemberAbstract) inputmode="numeric" autocomplete="tel" maxlength="13">
								@error('author_mobile')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="user_email" class="tit">이메일</label>
								<input type="email" id="user_email" name="author_email" class="text" value="{{ old('author_email', $abstract->author_email) }}" @readonly($isMemberAbstract)>
								@error('author_email')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
						</ul>
					</fieldset>

					<fieldset>
						<legend class="form_tit">초록 제출</legend>
						<ul class="inputs float">
							<li>
								<span class="tit">발표구분</span>
								<div class="select_abstract">
									@foreach ($abstractPresentationTypes as $code => $label)
										<div class="select_check">
											<input type="radio" id="abstract-committee-{{ $code }}" name="presentation_type" value="{{ $code }}" @checked($selectedPresentationType === $code) @disabled(! $canModifyAbstract)>
											<label for="abstract-committee-{{ $code }}"><span>{{ $label }}</span></label>
										</div>
									@endforeach
								</div>
								@error('presentation_type')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="academic_event_field_id" class="tit">발표 분야</label>
								<div class="select_abstract">
									@forelse ($abstractFields as $field)
										<div class="select_check">
											<input type="radio" id="speak-committee-{{ $field->id }}" name="academic_event_field_id" value="{{ $field->id }}" @checked((string) $selectedFieldId === (string) $field->id) @disabled(! $canModifyAbstract)>
											<label for="speak-committee-{{ $field->id }}"><span>{{ $field->name }}</span></label>
										</div>
									@empty
										<p>등록된 발표 분야가 없습니다.</p>
									@endforelse
								</div>
								@error('academic_event_field_id')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="speak-type" class="tit">초록 제목</label>
								<input type="text" id="speak-type" name="title" class="text w100p" value="{{ old('title', $abstract->title) }}" placeholder="초록 제목을 입력해주세요" @readonly(! $canModifyAbstract)>
								@error('title')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="file01" class="tit">초록 양식 업로드</label>
								<div class="file_wrap">
									<div class="flex">
										<input type="text" class="text" data-abstract-file-name readonly>
										<div class="file_input"><input type="file" id="file01" name="attachments[]" data-abstract-file-input @disabled(! $canModifyAbstract)><label for="file01" class="btn_file btn_wkk">파일첨부</label></div>
									</div>
								</div>
								<div class="file_list @if($abstract->files->isEmpty()) none @endif" data-abstract-file-list>
									@foreach ($abstract->files as $file)
										<span data-existing-file>
											<a href="{{ asset('storage/' . $file->stored_path) }}" target="_blank" rel="noopener">{{ $file->original_name }}</a>
											@if ($canModifyAbstract)
												<button type="button" data-remove-existing-file data-file-id="{{ $file->id }}">삭제</button>
											@endif
										</span>
									@endforeach
								</div>
								<div data-abstract-removed-files></div>
								@error('attachments')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
								@error('attachments.*')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
						</ul>
					</fieldset>
					
					<div class="btns_btm flex_center">
						<a href="{{ $conferenceBaseUrl }}/abstract/result" class="btn btn_kwg">뒤로가기</a>
						@if ($canModifyAbstract)
							<button type="submit" class="btn btn_wbb">초록 수정</button>
						@else
							<button type="button" class="btn btn_wbb" disabled>초록 수정</button>
						@endif
					</div>
				</form>
			</div>
			
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/academic-conference-abstract-form.js') }}"></script>
@endpush

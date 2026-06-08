@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
	$selectedPresentationType = old('presentation_type', array_key_first($abstractPresentationTypes));
	$selectedFieldId = old('academic_event_field_id');
@endphp
<main class="sub_area">

<section class="scon abstract_form_wrap" aria-labelledby="abstract-form-heading">
	<div class="inner">
		<h1 class="sub_title mb0" id="abstract-form-heading">{{ $sName }}</h1>

		<div class="member_inbox">
			<div class="gbox after_info print_area">
				<h2 class="tt">발표 양식 다운로드</h2>
				<p>발표양식에 문제가 있거나 기타 문의는 대한기능의학회로 연락바랍니다.</p>
				<ul class="tel_mail_infobox flex_center">
					<li class="i1"><a href="tel:01084414484">010-8441-4484</a></li>
					<li class="i2"><a href="mailto:0182253645@naver.com;">0182253645@naver.com</a></li>
				</ul>
				@if ($abstractBookUrl)
					<div class="btns flex_center">
						<a href="{{ $abstractBookUrl }}" class="btn btn_print" target="_blank" rel="noopener">초록 양식 다운로드</a>
					</div>
				@endif
			</div>

			<div class="inbox input_wrap">
				@error('submission')
					<p class="c_red" role="alert">{{ $message }}</p>
				@enderror
				<form action="{{ route('academic_conference.site.abstract.store_non_member', $event->folder_name) }}" method="post" enctype="multipart/form-data" data-abstract-form>
					@csrf
					<fieldset>
						<legend class="form_tit mt0">제출자 정보</legend>
						<ul class="inputs float">
							<li>
								<label for="user_name" class="tit">한글 이름</label>
								<input type="text" id="user_name" name="author_name" class="text" value="{{ old('author_name') }}" placeholder="한글 이름을 입력해주세요">
								@error('author_name')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="user_name_eng" class="tit">영문 이름</label>
								<input type="text" id="user_name_eng" name="author_name_en" class="text" value="{{ old('author_name_en') }}" placeholder="영문 이름을 입력해주세요">
								@error('author_name_en')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
<!-- 							<li>
								<label for="user_phone" class="tit">전화번호</label>
								<input type="tel" id="user_phone" name="author_phone" class="text" value="{{ old('author_phone') }}" placeholder="전화번호를 입력해주세요">
								@error('author_phone')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li> -->
							<li>
								<label for="user_tel" class="tit">휴대폰번호</label>
								<input type="tel" id="user_tel" name="author_mobile" class="text" value="{{ old('author_mobile') }}" placeholder="휴대폰번호를 입력해주세요" inputmode="numeric" autocomplete="tel" maxlength="13">
								@error('author_mobile')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="user_email" class="tit">이메일</label>
								<input type="email" id="user_email" name="author_email" class="text" value="{{ old('author_email') }}" placeholder="이메일을 입력해주세요">
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
											<input type="radio" id="abstract-committee-{{ $code }}" name="presentation_type" value="{{ $code }}" @checked($selectedPresentationType === $code)>
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
											<input type="radio" id="speak-committee-{{ $field->id }}" name="academic_event_field_id" value="{{ $field->id }}" @checked((string) $selectedFieldId === (string) $field->id)>
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
								<input type="text" id="speak-type" name="title" class="text w100p" value="{{ old('title') }}" placeholder="초록 제목을 입력해주세요">
								@error('title')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="file01" class="tit">초록 양식 업로드</label>
								<div class="file_wrap">
									<div class="flex">
										<input type="text" class="text" data-abstract-file-name readonly>
										<div class="file_input"><input type="file" id="file01" name="attachments[]" data-abstract-file-input required><label for="file01" class="btn_file btn_wkk">파일첨부</label></div>
									</div>
								</div>
								<div class="file_list none" data-abstract-file-list></div>
								@error('attachments')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
								@error('attachments.*')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
							<li>
								<label for="abstract-password" class="tit">초록 접수 비밀번호</label>
								<input type="password" id="abstract-password" name="lookup_password" class="text w100p" placeholder="초록 접수 비밀번호를 입력해주세요">
								@error('lookup_password')
									<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</li>
						</ul>
					</fieldset>

					<div class="btns_btm flex_center">
						<button type="button" class="btn btn_kwg" data-history-back>뒤로가기</button>
						<button type="submit" class="btn btn_wbb">초록신청</button>
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

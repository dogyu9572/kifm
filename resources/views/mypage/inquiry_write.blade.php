@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>

		<div class="glbox board_write inquiry_write_area">
			<form action="{{ isset($post) ? route('mypage.inquiry.update', $post->id) : route('mypage.inquiry.store') }}" method="POST" enctype="multipart/form-data" data-mypage-inquiry-form>
				@csrf
				@if (isset($post))
				@method('PUT')
				@endif
				<table>
					<tbody>
						<tr>
							<th scope="row">제목<span class="c_iden">*</span></th>
							<td>
								<input type="text" name="title" class="text w100p" value="{{ old('title', $post->title ?? '') }}" required>
								@error('title')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</td>
						</tr>
						<tr>
							<th scope="row">내용<span class="c_iden">*</span></th>
							<td>
								<textarea name="content" cols="30" rows="10" class="text w100p" required>{{ old('content', $post->content ?? '') }}</textarea>
								@error('content')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</td>
						</tr>
						<tr>
							<th scope="row">첨부파일</th>
							<td>
								<div class="file_wrap">
									<div class="flex" data-inquiry-file-row>
										<input type="text" class="text" data-abstract-file-name readonly>
										<div class="file_input">
											<input type="file" id="file01" name="attachments[]" data-abstract-file-input>
											<label for="file01" class="btn_file btn_wkk">파일첨부</label>
										</div>
										<button type="button" class="btn btn_plma btn_plus">추가</button>
									</div>
								</div>
								<div class="file_list @if(empty($attachments ?? [])) none @endif" data-abstract-file-list>
									@foreach (($attachments ?? []) as $attachmentIndex => $attachment)
									@if ($attachment['path'] !== '')
									<a href="{{ asset('storage/'.$attachment['path']) }}" download="{{ $attachment['name'] }}" data-existing-attachment-index="{{ $attachmentIndex }}">{{ $attachment['name'] }}</a>
									@else
									<a href="javascript:void(0);" data-existing-attachment-index="{{ $attachmentIndex }}">{{ $attachment['name'] }}</a>
									@endif
									@endforeach
								</div>
								@error('attachments')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
								@error('attachments.*')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</td>
						</tr>
						<tr>
							<th scope="row">자동등록방지<span class="c_iden">*</span></th>
							<td>
								<div class="captcha_area">
									<div class="obj imgfit"><img src="{{ route('subcommittee.captcha.discussion') }}" data-src="{{ route('subcommittee.captcha.discussion') }}" data-captcha-image alt="자동등록방지 코드"></div>
									<button type="button" class="obj btn_re" data-captcha-refresh aria-label="숫자 이미지 변경"></button>
									<input type="text" name="captcha" class="obj text" maxlength="6" autocomplete="off">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="btns_btm flex_center">
					@if (isset($post))
					<a href="{{ route('mypage.inquiry_view', ['id' => $post->id]) }}" class="btn btn_kwg">취소</a>
					@else
					<a href="{{ route('mypage.inquiry') }}" class="btn btn_kwg">목록</a>
					@endif
					<button type="submit" class="btn btn_wbb">{{ isset($post) ? '수정' : '등록' }}</button>
				</div>
			</form>
		</div>
	</div>
</section>

</main>
@endsection


@push('scripts')
<script src="{{ asset('js/frontend/mypage-inquiry-write.js') }}?v=2026061004"></script>
@endpush

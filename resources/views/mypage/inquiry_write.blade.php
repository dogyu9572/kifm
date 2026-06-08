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
			<form action="{{ isset($post) ? route('mypage.inquiry.update', $post->id) : route('mypage.inquiry.store') }}" method="POST">
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
									<div class="flex">
										<input type="text" class="text" data-abstract-file-name readonly>
										<div class="file_input"><input type="file" id="file01" name="attachments[]" data-abstract-file-input><label for="file01" class="btn_file btn_wkk">파일첨부</label></div>
										<button type="button" class="btn btn_plma btn_plus">추가</button>
									</div>
								</div>
								<div class="file_list none" data-abstract-file-list></div>
							</td>
						</tr>
						<tr>
							<th scope="row">자동등록방지*<span class="c_iden">*</span></th>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('change', function(e) {
        if (e.target && e.target.hasAttribute('data-abstract-file-input')) {
            const fileInput = e.target;
            const fileWrap = fileInput.closest('.file_wrap');
            const textInput = fileWrap.querySelector('[data-abstract-file-name]');
            const fileList = fileWrap.closest('td').querySelector('[data-abstract-file-list]');
            const files = fileInput.files;
            
            if (files.length > 0) {
                textInput.value = files[0].name;
                fileList.classList.remove('none');

                Array.from(files).forEach(file => {
                    const link = document.createElement('a');
                    link.href = 'javascript:void(0);';
                    link.textContent = file.name;
                    
                    link._relatedInput = fileInput;
                    link._relatedTextInput = textInput;
                    
                    fileList.appendChild(link);
                });
            }
        }
    });

    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn_plus')) {
            const currentFlex = e.target.closest('.flex');
            const fileWrap = currentFlex.closest('.file_wrap');
            const cloneFlex = currentFlex.cloneNode(true);
            const textInput = cloneFlex.querySelector('[data-abstract-file-name]');
            const fileInput = cloneFlex.querySelector('[data-abstract-file-input]');
            
            if(textInput) textInput.value = '';
            if(fileInput) fileInput.value = '';

            const uniqueId = 'file_' + new Date().getTime() + '_' + Math.floor(Math.random() * 1000);
            const label = cloneFlex.querySelector('label');
            if(fileInput && label) {
                fileInput.id = uniqueId;
                label.setAttribute('for', uniqueId);
            }

            const plusBtn = cloneFlex.querySelector('.btn_plus');
            plusBtn.textContent = '삭제';
            plusBtn.classList.remove('btn_plus');
            plusBtn.classList.add('btn_minus');

            fileWrap.appendChild(cloneFlex);
        }

        if (e.target && e.target.classList.contains('btn_minus')) {
            const currentFlex = e.target.closest('.flex');
            const fileInput = currentFlex.querySelector('[data-abstract-file-input]');
            const fileList = currentFlex.closest('td').querySelector('[data-abstract-file-list]');
            
            if (fileInput && fileList) {
                const links = fileList.querySelectorAll('a');
                links.forEach(link => {
                    if (link._relatedInput === fileInput) {
                        link.remove();
                    }
                });
                if (fileList.children.length === 0) {
                    fileList.classList.add('none');
                }
            }
            
            currentFlex.remove();
        }

        if (e.target && e.target.closest('[data-abstract-file-list] a')) {
            const targetLink = e.target;
            const fileList = targetLink.closest('[data-abstract-file-list]');
            
            if (targetLink._relatedInput) targetLink._relatedInput.value = '';
            if (targetLink._relatedTextInput) targetLink._relatedTextInput.value = '';

            targetLink.remove();
            
            if (fileList.children.length === 0) {
                fileList.classList.add('none');
            }
        }
    });
});
</script>
@endpush
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
			<table>
				<tbody>
					<tr>
						<th scope="row">제목<span class="c_red">*</span></th>
						<td><input type="text" class="text w100p" value="홍길동"></td>
					</tr>
					<tr>
						<th scope="row">내용</th>
						<td><textarea name="" id="" cols="30" rows="10" class="text w100p"></textarea></td>
					</tr>
					<tr>
						<th scope="row" class="vac">첨부파일</th>
						<td>
							<div class="file_wrap">
								<div class="flex">
									<input type="text" class="text">
									<div class="file_input"><input type="file" id="file01"><label for="file01" class="btn_file btn_wkk">파일첨부</label></div>
									<button type="button" class="btn_plma btn_plus">+</button>
								</div>
							</div>
							<div class="file_list">
								<a href="javascript:void(0);">첨부파일이 들어가는 공간입니다.</a>
								<a href="javascript:void(0);">첨부파일이 들어가는 공간입니다.</a>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">자동등록방지<span class="c_red">*</span></th>
						<td>
							<div class="captcha_area">
								<div class="imgfit obj"><img src="/images/img_sample_captcha.jpg" alt=""></div>
								<button class="btn_re obj"></button>
								<input type="text" class="text obj">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const MAX_SINGLE_SIZE = 5 * 1024 * 1024;
    const MAX_TOTAL_SIZE = 20 * 1024 * 1024;

    $(document).on('click', '.btn_plus', function() {
        const $currentRow = $(this).closest('.flex');
        const $newRow = $currentRow.clone();

        $newRow.find('input[type="text"]').val('');
        $newRow.find('input[type="file"]').val('');
        
        const newId = 'file_' + Date.now();
        $newRow.find('input[type="file"]').attr('id', newId);
        $newRow.find('label').attr('for', newId);

        const $btn = $newRow.find('.btn_plma');
        $btn.removeClass('btn_plus').addClass('btn_minus').text('-');

        $('.file_wrap').append($newRow);
    });

    $(document).on('click', '.btn_minus', function() {
        $(this).closest('.flex').remove();
    });

    $(document).on('change', 'input[type="file"]', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.size > MAX_SINGLE_SIZE) {
            alert("파일 한 개당 용량은 5MB를 초과할 수 없습니다.");
            $(this).val('');
            return;
        }

        let totalSize = 0;
        $('input[type="file"]').each(function() {
            if (this.files[0]) {
                totalSize += this.files[0].size;
            }
        });

        if (totalSize > MAX_TOTAL_SIZE) {
            alert("전체 파일 총 용량은 20MB를 초과할 수 없습니다.");
            $(this).val('');
            return;
        }

        $(this).closest('.flex').find('.text').val(file.name);
    });
// file_list 클릭시 파일 삭제
	function checkFileListEmpty() {
        $('.file_list').each(function() {
            if ($(this).find('a').length === 0) {
                $(this).addClass('none');
            } else {
                $(this).removeClass('none');
            }
        });
    }

    checkFileListEmpty();

    $(document).on('click', '.file_list a', function(e) {
        e.preventDefault();
        $(this).remove();
        checkFileListEmpty();
    });
});
</script>
@endpush
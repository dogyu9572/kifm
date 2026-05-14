@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
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
				<div class="btns flex_center">
					<a href="" class="btn btn_print">초록 양식 다운로드</a>
				</div>
			</div>
			
			<div class="inbox input_wrap">
				<form action="/academic_conference/abstract/complete" method="post">
					<fieldset>
						<legend class="form_tit mt0">결제자 정보</legend>
						<ul class="inputs float">
							<li>
								<label for="user_name" class="tit">이름(국문)</label>
								<input type="text" id="user_name" name="user_name" class="text" placeholder="이름(국문)을 입력해주세요">
							</li>
							<li>
								<label for="user_name_eng" class="tit">이름(영문)</label>
								<input type="text" id="user_name_eng" name="user_name_eng" class="text" placeholder="이름(영문)을 입력해주세요">
							</li>
							<li>
								<label for="user_phone" class="tit">전화번호</label>
								<input type="tel" id="user_phone" name="user_phone" class="text" placeholder="전화번호를 입력해주세요">
							</li>
							<li>
								<label for="user_tel" class="tit">휴대폰번호</label>
								<input type="tel" id="user_tel" name="user_tel" class="text" placeholder="휴대폰번호를 입력해주세요">
							</li>
							<li>
								<label for="user_email" class="tit">이메일</label>
								<input type="email" id="user_email" name="user_email" class="text" placeholder="이메일을 입력해주세요">
							</li>
						</ul>
					</fieldset>

					<fieldset>
						<legend class="form_tit">초록 제출</legend>
						<ul class="inputs float">
							<li>
								<label for="abstract-committee01" class="tit">발표구분</label>
								<div class="select_abstract">
									<div class="select_check"><input type="checkbox" id="abstract-committee01" name="abstract-committee"><label for="abstract-committee01"><span>기조강연</span></label></div>
									<div class="select_check"><input type="checkbox" id="abstract-committee02" name="abstract-committee"><label for="abstract-committee02"><span>키노트 강연</span></label></div>
									<div class="select_check"><input type="checkbox" id="abstract-committee03" name="abstract-committee"><label for="abstract-committee03"><span>초청강연</span></label></div>
									<div class="select_check"><input type="checkbox" id="abstract-committee04" name="abstract-committee"><label for="abstract-committee04"><span>구두발표</span></label></div>
									<div class="select_check"><input type="checkbox" id="abstract-committee05" name="abstract-committee"><label for="abstract-committee05"><span>포스터 발표</span></label></div>
								</div>
							</li>
							<li>
								<label for="speak-committee01" class="tit">발표구분</label>
								<div class="select_abstract">
									<div class="select_check"><input type="checkbox" id="speak-committee01" name="speak-committee"><label for="speak-committee01"><span>분야명 1</span></label></div>
									<div class="select_check"><input type="checkbox" id="speak-committee02" name="speak-committee"><label for="speak-committee02"><span>분야명 2</span></label></div>
									<div class="select_check"><input type="checkbox" id="speak-committee03" name="speak-committee"><label for="speak-committee03"><span>분야명 3</span></label></div>
									<div class="select_check"><input type="checkbox" id="speak-committee04" name="speak-committee"><label for="speak-committee04"><span>분야명 4</span></label></div>
									<div class="select_check"><input type="checkbox" id="speak-committee05" name="speak-committee"><label for="speak-committee05"><span>분야명 5</span></label></div>
								</div>
							</li>
							<li>
								<label for="speak-type" class="tit">초록 제목</label>
								<input type="text" id="speak-type" class="text w100p" placeholder="초록 제목을 입력해주세요">
							</li>
							<li>
								<label for="file01" class="tit">초록 양식 업로드</label>
								<div class="file_wrap">
									<div class="flex">
										<input type="text" class="text">
										<div class="file_input"><input type="file" id="file01"><label for="file01" class="btn_file btn_wkk">파일첨부</label></div>
									</div>
								</div>
								<div class="file_list">
									<a href="javascript:void(0);">첨부파일이 들어가는 공간입니다.</a>
								</div>
							</li>
							<li>
								<label for="abstract-password" class="tit">초록 접수 비밀번호</label>
								<input type="text" id="abstract-password" class="text w100p" placeholder="초록 접수 비밀번호를 입력해주세요">
							</li>
						</ul>
					</fieldset>
					
					<div class="btns_btm flex_center">
						<button type="button" class="btn btn_kwg" onclick="history.back()">뒤로가기</button>
						<button type="button" class="btn btn_wbb" onclick="location.href='/academic_conference/abstract/complete'">초록신청</button>
					</div>
				</form>
			</div>
			
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 설정: 단일 파일 최대 용량 (5MB)
    const MAX_SINGLE_SIZE = 5 * 1024 * 1024;

    // 파일 선택 이벤트
    $(document).on('change', 'input[type="file"]', function() {
        const file = this.files[0];
        if (!file) return;

        // 1. 단일 파일 용량 체크
        if (file.size > MAX_SINGLE_SIZE) {
            alert("파일 용량은 5MB를 초과할 수 없습니다.");
            $(this).val(''); // 파일 선택 취소
            $(this).closest('.flex').find('.text').val(''); // 텍스트창 초기화
            return;
        }

        // 2. 파일명을 텍스트 입력창에 표시
        $(this).closest('.flex').find('.text').val(file.name);
    });

    // 기존 첨부파일 목록(file_list) 관리
    function checkFileListEmpty() {
        $('.file_list').each(function() {
            // 내부 요소가 없거나 비어있으면 숨김 처리
            if ($(this).children().length === 0) {
                $(this).addClass('none');
            } else {
                $(this).removeClass('none');
            }
        });
    }

    // 초기 실행
    checkFileListEmpty();

    // 첨부파일 삭제 버튼 클릭 시
    $(document).on('click', '.file_list a', function(e) {
        e.preventDefault();
        if(confirm("해당 파일을 삭제하시겠습니까?")) {
            $(this).remove();
            checkFileListEmpty();
        }
    });
});
</script>
@endpush
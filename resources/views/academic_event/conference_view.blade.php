@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<div class="sub_title">{{ $sName }}</div>

<section class="scon academic_event_view_top" aria-labelledby="conference-view-heading">
	<div class="inner">
		<div class="live"><span class="sound_only">강의 형태:</span>온라인 라이브</div>
		<h1 id="conference-view-heading">2026년 2월 8일 <br aria-hidden="true"><strong>심화 연수강좌</strong></h1>
		<p>Advanced Learning Course for Beginners in Functional Medicine II - Module on Immunity, <br class="pc_vw" aria-hidden="true">
			Cardiometabolic health and Detoxification <br class="pc_vw" aria-hidden="true">
			기능의학 입문자를 위한 심화 학습코스 II - 면역, 심혈관-대사, 해독 모듈
		</p>
		<x-frontend.bookmark-button content-type="academic_event_conference_static" content-id="1" title="2026년 2월 8일 심화 연수강좌" :menu-label="$sName" :url="route('academic_event.conference_view')" label="이 행사를 북마크에 추가" />
	</div>
</section>

<section class="scon academic_event_view_detail conference_view_wrap" aria-labelledby="conference-detail-heading">
    <h2 class="sound_only" id="conference-detail-heading">{{ $sName }} 상세내용</h2>
    <div class="inner">
        <div class="inbox">
        	<ul class="tabs" role="tablist">
        	    <li role="presentation" class="on"><button type="button" role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1">사전등록 안내 및 인증의제도</button></li>
        	    <li role="presentation"><button type="button" role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2">1차 교육 안내</button></li>
        	    <li role="presentation"><button type="button" role="tab" aria-selected="false" aria-controls="panel-3" id="tab-3">2차 교육 안내</button></li>
        	    <li role="presentation"><button type="button" role="tab" aria-selected="false" aria-controls="panel-4" id="tab-4">3차 교육 안내</button></li>
        	</ul>
        	
        	<div class="con_area">
        		<div class="con" id="panel-1" role="tabpanel" aria-labelledby="tab-1">
        		    <h3 class="btit">사전등록 안내</h3>
        		    <p>사전등록 기간 및 교육 일정과 비용을 반드시 확인해 주세요.<br/><strong>사전등록일시: 2026년 11월 1일 (월) ~ 2026년 11월 9일(일)</strong></p>
        		    <div class="tbl tac">
        		        <table>
        		            <caption>사전등록 안내 구분표: 구분별 등록 비용(정회원, 비회원, 기타) 정보 제공</caption>
        		            <thead>
        		                <tr>
        		                    <th scope="col">구분</th>
        		                    <th scope="col">정회원</th>
        		                    <th scope="col">비회원</th>
        		                    <th scope="col">간호사/공보의/군의관</th>
        		                </tr>
        		            </thead>
        		            <tbody>
        		                <tr>
        		                    <th scope="row">전체 등록</th>
        		                    <td>25만원</td>
        		                    <td>50만원</td>
        		                    <td>13만원</td>
        		                </tr>
        		                <tr>
        		                    <th scope="row">각 차시별</th>
        		                    <td>차시당 10만원</td>
        		                    <td>차시당 20만원</td>
        		                    <td>차시당 5만원</td>
        		                </tr>
        		                <tr>
        		                    <th scope="row">현장등록</th>
        		                    <td colspan="3"><div class="flex_center"><p class="excl"><span class="sound_only">안내:</span>당일 등록이 불가합니다.</p></div></td>
        		                </tr>
        		            </tbody>
        		        </table>
        		    </div>
        		
        		    <h3 class="btit">인증제도</h3>
        		    <p>심화학습코스1(홀수 연도)과 심화학습코스2(짝수 연도)를 모두 수강하셔야 인증의 자격이 주어집니다.</p>
        		    <div class="gbox">
        		        <span class="sound_only">인증의 신청 자격 및 절차 목록:</span>
        		        <ol class="num_list">
        		            <li>회원가입 및 가입비 납부를 통하여 정회원이 된 회원만이 인증의 신청 자격을 얻는다.</li>
        		            <li>회원이 동계심화연수강좌 6회 (연 3회씩 총 2년)를 받으면 인증서를 학회로부터 제공받을 수 있다</li>
        		            <li>인증의 인증서를 받은 회원은 본 학회 홈페이지 내 우리동네 주치의에 등록 자격을 갖는다</li>
        		        </ol>
        		    </div>
        		
        		    <h3 class="btit">인증혜택</h3>
        		    <p>대한기능의학회 홈페이지에서 제공하는 ‘우리동네 병원 찾기’ 페이지에 병원을 소개해드립니다.</p>
        		    <div class="gbox">
        		        <span class="sound_only">병원 정보 공개 및 관리 유의사항:</span>
        		        <ol class="num_list">
        		            <li>병원명, 주소, 원장명 (사진 포함) 공개</li>
        		            <li>병원 이전 등으로 정보가 변경되었을 경우 반드시 학회로 연락하여 주시기 바랍니다</li>
        		        </ol>
        		    </div>
        		</div>
        		<div class="con" id="panel-2" role="tabpanel" aria-labelledby="tab-2">
					<img src="/images/img_sample_academic_event_view.jpg" alt="">
				</div>
        		<div class="con" id="panel-3" role="tabpanel" aria-labelledby="tab-3">
					<img src="/images/img_sample_academic_event_view.jpg" alt="">
				</div>
        		<div class="con" id="panel-4" role="tabpanel" aria-labelledby="tab-4">
					<img src="/images/img_sample_academic_event_view.jpg" alt="">
				</div>
        	</div>
			
			<aside class="abso_application">
				<div class="head">
					<h3 class="tit">지금 바로 신청하세요</h3>
					<p class="limit">사전등록 마감 D-5</p>
				</div>
				<div class="cont">
					<a href="javascript:void(0);" class="btn btn_wrr btn_outlink" onclick="layerShow('benefits_guide');">사전등록 바로가기</a>
					<a href="#this" download title="파일 다운로드" class="btn btn_kwg btn_download">교제 다운로드</a>
					<ul class="file_list">
						<li><a href="#this">첨부파일이 들어가는 공간입니다.</a></li>
						<li><a href="#this">첨부파일이 들어가는 공간입니다.</a></li>
					</ul>
				</div>
			</aside>
        </div>
	</div>
</section>

<aside class="popup" id="benefits_guide" role="dialog" aria-labelledby="benefits-title" aria-modal="true">
    <div class="dm" onclick="layerHide('benefits_guide');"></div>
    <div class="inbox">
        <h2 id="benefits-title" class="ptit">회원 결제 혜택 안내</h2>
        <div class="gbox tac flex_center">
            <p>로그인 시 회원 전용 할인 혜택이 즉시 적용됩니다.<br/>지금 로그인하시겠습니까?</p>
        </div>
        <div class="btns">
            <button type="button" class="btn btn_kwk" onclick="layerHide('benefits_guide');">비회원으로 등록</button>
            <a href="/member/login" class="btn btn_wbb">회원 로그인</a>
        </div>
    </div>
</aside>
	
</main>

@endsection

@push('scripts')
<script src="{{ asset('js/frontend/script_bookmark.js') }}"></script>
<script>
// 탭
	const $tabButtons = $('.tabs [role="tab"]');
    const $tabPanels = $('.con_area [role="tabpanel"]');
    $tabPanels.hide().filter('#panel-1').show();
    $tabButtons.on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const targetPanelId = $this.attr('aria-controls');
        $tabButtons.attr('aria-selected', 'false').parent('li').removeClass('on');
        $this.attr('aria-selected', 'true').parent('li').addClass('on');
        $tabPanels.hide();
        $('#' + targetPanelId).show();
    });
    $tabButtons.on('keydown', function(e) {
        let index = $tabButtons.index(this);
        let targetIndex;
        switch (e.keyCode) {
            case 37:
                targetIndex = index - 1;
                if (targetIndex < 0) targetIndex = $tabButtons.length - 1;
                $tabButtons.eq(targetIndex).focus().trigger('click');
                break;
            case 39:
                targetIndex = index + 1;
                if (targetIndex >= $tabButtons.length) targetIndex = 0;
                $tabButtons.eq(targetIndex).focus().trigger('click');
                break;
        }
    });
// num_list
    $('ol.num_list').each(function() {
        $(this).find('> li').each(function(index) {
            let num = index + 1;
            let displayNum = num < 10 ? '0' + num : num;
            $(this).prepend('<span class="num" aria-hidden="true">' + displayNum + '</span>');
        });
    });
// abso_application 고정
    const $window = $(window);
    const $header = $('header');
    const $viewTop = $('.academic_event_view_top');
    const $detail = $('.academic_event_view_detail');
    const $absoApp = $('.abso_application');
    $window.on('scroll.stickyApp', function() {
        const scrollTop = $window.scrollTop();
        const headerHeight = $header.outerHeight();
        const topMargin = parseInt($viewTop.css('margin-bottom')) || 0;
        const detailOffsetTop = $detail.offset().top;
        const detailHeight = $detail.outerHeight();
        const appHeight = $absoApp.outerHeight();
        const fixStartPoint = detailOffsetTop - 190; 
        const unfixPoint = (detailOffsetTop + detailHeight) - appHeight - 190;
        if (scrollTop >= fixStartPoint) {
            if (scrollTop >= unfixPoint) {
                $detail.addClass('unfixed').removeClass('fixed');
            } else {
                $detail.addClass('fixed').removeClass('unfixed');
            }
        } else {
            $detail.removeClass('fixed unfixed');
        }
    });
    $window.trigger('scroll.stickyApp');
// 팝업
	var lastFocusedElement;
	function layerShow(id) {
		lastFocusedElement = document.activeElement;
		$("#" + id).fadeIn(300, function() {
			$(this).find(".btn_kwk").focus();
		});
	}
	function layerHide(id) {
		$("#" + id).fadeOut(300, function() {
			if (lastFocusedElement) lastFocusedElement.focus();
		});
	}
</script>
@endpush

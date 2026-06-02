@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@inject('trainingCourse', 'App\Services\Frontend\PublicTrainingCourseService')
<main class="sub_area">

<div class="sub_title">{{ $sName }}</div>

<section class="scon academic_event_view_top" aria-labelledby="conference-view-heading">
	<div class="inner">
	<div class="live"><span class="sound_only">강의 형태:</span>{{ $trainingCourse->methodLabel($training->training_method) }}</div>
	<h1 id="conference-view-heading">{!! nl2br(e($training->title)) !!}</h1>
	<p>{{ $training->introduction ?: $trainingCourse->headlineText($training) }}</p>
	<x-frontend.bookmark-button content-type="academic_event_training_course" :content-id="$training->id" :title="$training->title" :menu-label="$sName" :url="$trainingCourse->detailUrl($training)" label="이 행사를 북마크에 추가" />
	</div>
</section>

<section class="scon academic_event_view_detail conference_view_wrap" aria-labelledby="conference-detail-heading">
    <h2 class="sound_only" id="conference-detail-heading">{{ $sName }} 상세내용</h2>
    <div class="inner">
        <div class="inbox">
	<ul class="tabs" role="tablist">
	    <li role="presentation" class="on"><button type="button" role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1">개요</button></li>
	    <li role="presentation"><button type="button" role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2">프로그램</button></li>
	    <li role="presentation"><button type="button" role="tab" aria-selected="false" aria-controls="panel-3" id="tab-3">사전등록 안내 및 인증의 제도</button></li>
	</ul>

	<div class="con_area">
	<div class="con" id="panel-1" role="tabpanel" aria-labelledby="tab-1">
				@if ($training->overview)
					{!! $training->overview !!}
				@else
					<img src="/images/img_sample_academic_event_view.jpg" alt="">
				@endif
		</div>
		<div class="con" id="panel-2" role="tabpanel" aria-labelledby="tab-2">
					@if ($training->program)
						{!! $training->program !!}
					@else
						<p>등록된 프로그램이 없습니다.</p>
					@endif
		</div>
	<div class="con" id="panel-3" role="tabpanel" aria-labelledby="tab-3">
				<h3 class="btit">사전등록 안내</h3>
				@if ($training->registration_info)
					{!! $training->registration_info !!}
				@else
			<p>사전등록 기간 및 교육 일정과 비용을 반드시 확인해 주세요.</p>
				@endif
			</div>
	</div>

		<aside class="abso_application">
			<div class="head">
				<h3 class="tit">지금 바로 신청하세요</h3>
				<p class="limit">{{ $trainingCourse->status($training)['label'] }}</p>
			</div>
				<div class="cont">
					<a href="{{ $trainingCourse->paymentUrl($training) }}" class="btn btn_wrr btn_outlink">사전등록 바로가기</a>
					@if ($trainingCourse->fileUrl($training->textbook_file_path))
						<a href="{{ route('academic_event.training_course_textbook.download', $training) }}" title="파일 다운로드" class="btn btn_kwg btn_download">교제 다운로드</a>
					@endif
					@if ($training->attachments->isNotEmpty())
						<ul class="file_list">
							@foreach ($training->attachments as $attachment)
								<li><a href="{{ route('academic_event.training_course_attachment.download', $attachment) }}">{{ $attachment->original_name ?: basename($attachment->file_path) }}</a></li>
							@endforeach
						</ul>
					@endif
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
	const $inbox = $detail.find('.inbox');
	const $absoApp = $('.abso_application');
	function handleStickyLayout() {
		const scrollTop = $window.scrollTop();
		const windowHeight = $window.height();
		const windowWidth = $window.width();
		const detailOffsetTop = $detail.offset().top;
		const detailHeight = $detail.outerHeight();
		const appHeight = $absoApp.outerHeight();
		if (windowWidth <= 767) {
			$inbox.css('padding-bottom', (appHeight + 20) + 'px');
			const mobileUnfixPoint = (detailOffsetTop + detailHeight) - windowHeight;
			if (scrollTop >= mobileUnfixPoint) {
				$detail.addClass('unfixed').removeClass('fixed');
			} else {
				$detail.removeClass('unfixed').addClass('fixed'); 
			}
		} else {
			$inbox.css('padding-bottom', '');
			const headerHeight = $header.outerHeight() || 0;
			const topMargin = parseInt($viewTop.css('margin-bottom')) || 0;
			const targetOffset = headerHeight + topMargin;
			const fixStartPoint = detailOffsetTop - targetOffset;
			const unfixPoint = (detailOffsetTop + detailHeight) - appHeight - targetOffset;
			if (scrollTop >= fixStartPoint) {
				if (scrollTop >= unfixPoint) {
					$detail.addClass('unfixed').removeClass('fixed');
				} else {
					$detail.addClass('fixed').removeClass('unfixed');
				}
			} else {
				$detail.removeClass('fixed unfixed');
			}
		}
	}
	$window.on('scroll.stickyApp resize.stickyApp', handleStickyLayout);
	$(window).on('load', function() { handleStickyLayout(); });
	handleStickyLayout();
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

//mobile
	
</script>
@endpush

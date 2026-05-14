@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon online_academy_view" aria-labelledby="online-academy-heading">
	<div class="inner">
		
		<div class="board_view nbd_t">
			<div class="view_top">
				<button type="button" class="btn_back">뒤로</button>
			</div>
			<div class="tit_area">
				<h1 class="tit" id="online-academy-heading">2026년 심혈관 질환 마스터 클래스 (기초 과정)</h1>
				<div class="sub_tit">본 강의는 심혈관 질환 마스터 클래스에 대한 기초 강의입니다.</div>
				<a href="#this" class="btn_abso btn_kwk btn_download">강의록 다운로드</a>
			</div>
			<div class="state_line">
				<div class="line"><div class="bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div></div>
				<div class="flex">
					<div class="left">수강률 <strong class="percent_val">100%</strong></div>
					<div class="right"><strong>150분</strong> / 150분</div>
				</div>
			</div>
			<div class="cont">
				<div class="video"><img src="/images/img_sample_online_academy_view.jpg" alt=""></div>
				<article class="txt">
					<div class="tit">강의 내용</div>
					<p>기능의학 (Functional medicine)은 기능 의학은 질병의 근본 원인을 식별하고 해결하는 데 중점을 둔 의학인데요.<br/>기능의학이 무엇인지 아주대병원 가정의학과 김범택(대한기능의학회 이사장)과 함께 들어보시죠.</p>
					<ul class="label">
						<li>기능의학의 필요성과 개인 경험</li>
						<li>기능의학과 알레르기 치료 접근</li>
						<li>기능의학의 생리학적 원리와 치료 목표</li>
						<li>기능의학과 생물학적 개별성의 중요성</li>
						<li>기능의학에서의 주요 치료 접근법</li>
					</ul>
					<div class="tit">키워드</div>
					<ul class="keyword">
						<li># Heart Transplantation</li>
						<li># Heart Transplantation</li>
						<li># Heart Transplantation</li>
						<li># Heart Transplantation</li>
					</ul>
					<div class="btn_area">
						<div class="txtbox" aria-hidden="true">학습을 모두 마치셨나요?<br/>아래 버튼을 누르면 간단한 테스트 후 수강 완료 처리가 됩니다.</div>
						<a href="/online_academy/test" class="btn btn_test" disabled aria-disabled="true">시험보기</a>
					</div>
				</article>
			</div>
		</div>
		
	</div>
</section>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
// 상태바
    $('.state_line').each(function() {
        const $this = $(this);
        const percentText = $this.find('.left strong').text();
        const percentValue = percentText.replace(/[^0-9]/g, '');
        
        $this.find('.bar').css('width', percentValue + '%');
    });
// 수강률 100%일 때 시험보기 활성화
    const $container = $('.online_academy_view');
    const $percentText = $('.state_line .left strong');
    const $testBtnArea = $('.btn_area');
    const $textBox = $('.txtbox');
    const $testBtn = $('.btn_test');

    function checkPercent() {
        const currentPercent = $percentText.text().replace(/\s/g, '');
        
        if (currentPercent === '100%') {
            $container.addClass('percent100');
            $testBtnArea.addClass('end');
            
            $textBox.removeAttr('aria-hidden'); 
            $testBtn.removeClass('disabled')
                    .removeAttr('disabled')
                    .attr('aria-disabled', 'false');
            
            return true;
        }
        return false;
    }

    if ($percentText.length > 0) {
        if (!checkPercent()) {
            const observer = new MutationObserver(function() {
                if (checkPercent()) {
                    observer.disconnect();
                }
            });

            observer.observe($percentText[0], {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
    }

    $testBtn.on('click', function(e) {
        if ($(this).attr('aria-disabled') === 'true') {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@extends('layouts.frontend')
@section('title', $gName)
@section('gName', $gName)

@section('content')
<main class="main_wrap">
<h1 class="sound_only">대한기능의학회 인트로</h1>

<!-- main_visual -->
<section class="intro_wrap" aria-labelledby="intro-title">
	<div class="link_btns">
		<a href="javascript:alert('준비중입니다.')" class="i1">English</a>
		<a href="https://kyobo050.medone.co.kr/html/" target="_blank" class="i2">Journal</a>
	</div>
	<div class="intro_contents">
		<img src="/images/logo.png" alt="대한기능의학회">
		<h2 class="title">질병의 증상을 넘어, <br class="mo_vw"><strong class="c_iden">삶의 근본적인 건강</strong>을 디자인합니다.</h2>
		<p class="tit">환자 중심의 맞춤형 의학, 대한기능의학회가 <br class="mo_vw">앞선 전문성으로 내일의 건강을 제시합니다.</p>
		<div class="links">
			<a href="javascript:alert('준비중입니다.')" class="i1"><span class="bg_box"><span class="txt"><h3>일반인 홈페이지</h3><p>뿌리부터 치료하는 기능의학 알아보기</p></span><i aria-hidden="true"></i><span aria-hidden="true" class="blur_bg"></span></span></a>
			<a href="/home" class="i2"><span class="bg_box"><span class="txt"><h3>전문인 홈페이지</h3><p>기능의학의 깊이를 더하는 학문적 동행</p></span><i aria-hidden="true"></i><span aria-hidden="true" class="blur_bg"></span></span></a>
		</div>
	</div>
	<div class="intro_footer inner">
		<ul>
			<li><strong>주소</strong>경기도 수원시 영통구 월드컵로 164 (원천동, 아주대학병원) 1031호</li>
			<li><strong>대표자</strong>김범택</li>
			<li><strong>사업자번호</strong>262-82-00017</li>
			<li><strong>전화번호</strong>010-8441-4884</li>
		</ul>
		<p class="copyright">Copyright ⓒ Korean Society for Functional Medicine. All rights reserved.</p>
	</div>
</section>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const introContents = document.querySelector('.intro_contents');
    const introFooter = document.querySelector('.intro_footer');
    function updateHeight() {
        if (introContents && introFooter) {
            const footerHeight = introFooter.offsetHeight;
            introContents.style.height = `calc(100vh - ${footerHeight}px)`;
        }
    }
    updateHeight();
    window.addEventListener('resize', updateHeight);
});
</script>

@endsection
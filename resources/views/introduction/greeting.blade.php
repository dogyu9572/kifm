@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="greeting-heading">
    <div class="inner">
        <h1 class="sub_title" id="greeting-heading">{{ $sName }}</h1>
        
        <div class="greeting_area">
            <div class="imgfit">
                <img src="/images/img_greeting.jpg" alt="" aria-hidden="true">
                <div class="name">대한기능의학회 학회장 <span class="signature"><img src="/images/txt_name_gretting.svg" alt="김범택 서명"></span></div>
            </div>

            <div class="txt">
                <span class="eng_title">KOREAN INSTITUTE FOR FUNCTIONAL MEDICINE</span>
                <h2><strong class="c_iden">기능의학의</strong> 새로운 기준을 <br aria-hidden="true"> 만들어가겠습니다.</h2>
                
                <div class="desc">
                    <p>기능의학이 국내에 소개된 지 10여 년, <br class="pc_vw" aria-hidden="true">
						그 사이 많은 의료진이 만성질환의 근본 원인을 찾고 환자의 삶을 바꾸기 위해 끊임없이 노력해왔습니다.<br aria-hidden="true">
						접근 방식은 달라도, 그 중심에는 언제나 '더 나은 치료'를 향한 열정이 있었습니다.
					</p>
					<p><strong>대한기능의학회는 그 열정이 모이는 곳입니다.</strong></p>
					<p>우리는 글로벌 기준의 교육과 국내 임상 데이터를 기반으로, <br class="pc_vw" aria-hidden="true">
						기능의학이 한국 의료의 신뢰받는 한 축으로 자리잡을 수 있도록 토대를 다지고 있습니다.<br aria-hidden="true">
						회원 간의 활발한 교류와 학문적 성장이 이루어지는 플랫폼으로서, 학회는 여러분과 함께 진화하겠습니다.
					</p>
					<p>기능의학의 미래는 지금 이 자리에 함께하는 여러분이 만들어갑니다. 여러분의 적극적인 참여와 연대를 기다립니다.</p>
                </div>
            </div>
        </div>
    </div>
</section>
	
</main>

@endsection
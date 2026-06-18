@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon location_wrap" aria-labelledby="location-heading">
    <div class="inner">
        <h1 class="sub_title" id="location-heading">{{ $sName }}</h1>
        
        <div class="location_area">
            <div class="map_area" role="application" aria-label="오시는 길 지도">
                <div id="daumRoughmapContainer1776319832852" class="root_daum_roughmap root_daum_roughmap_landing">
                    <p class="sound_only">지도가 로딩 중입니다. 지도의 상세 위치 정보는 하단의 주소 텍스트를 참조해 주세요.</p>
                </div>
                <script charset="UTF-8" class="daum_roughmap_loader_script" src="https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js"></script>
                <script charset="UTF-8">
                    new daum.roughmap.Lander({
                        "timestamp" : "1776319832852",
                        "key" : "ksina3er9go",
                        "mapWidth" : "720",
                        "mapHeight" : "310"
                    }).render();
                </script>
            </div>

            <div class="txt_area">
                <h2>대한기능의학회</h2>
                <ul>
                    <li class="i1"><span class="sound_only">주소:</span>경기도 수원시 영통구 월드컵로 164 (원천동, 아주대학병원) 1004호</li>
                    <li class="i2"><span class="sound_only">전화번호:</span><a href="tel:01084414884" title="전화 걸기">010-8441-4884</a></li>
                    <li class="i3"><span class="sound_only">이메일:</span><a href="mailto:0182253645@naver.com" title="메일 보내기">0182253645@naver.com</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
	
</main>

@endsection
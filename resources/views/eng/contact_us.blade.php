@extends('layouts.frontend')
@section('title', $geName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">
<section class="scon location_wrap" aria-labelledby="location-heading">
    <div class="inner">
        <h1 class="sub_title" id="location-heading">{{ $geName }}</h1>
        
        <div class="location_area">
            <div class="map_area" role="application" aria-label="Map to our location">
                <div id="daumRoughmapContainer1776319832852" class="root_daum_roughmap root_daum_roughmap_landing">
                    <p class="sound_only">Map is loading. Please refer to the address text below for detailed location information.</p>
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
                <h2>Korean Society for Functional Medicine</h2>
                <ul>
                    <li class="i1"><span class="sound_only">Address:</span>Room 1004, Ajou University Hospital, 164 World Cup-ro, Yeongtong-gu, Suwon-si, Gyeonggi-do</li>
                    <li class="i2"><span class="sound_only">Phone:</span><a href="tel:01084414884" title="Call">010-8441-4884</a></li>
                    <li class="i3"><span class="sound_only">Email:</span><a href="mailto:0182253645@naver.com" title="Send email">0182253645@naver.com</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
	
</main>
@endsection
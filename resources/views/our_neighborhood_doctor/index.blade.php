@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon our_doctor_wrap" aria-labelledby="our-neighborhood-doctor-heading">
    <div class="inner">
        <h1 class="sub_title" id="our-neighborhood-doctor-heading">{{ $sName }}</h1>
        
        <form class="board_top search_wrap" role="search" aria-label="의료진 검색">
            <fieldset>
                <legend class="sound_only">검색 조건 선택</legend>
                <div class="search_field">
                    <label for="subject-select" class="sound_only">진료 과목 선택</label>
                    <select name="subject" id="subject-select" class="text">
                        <option value="">진료 과목</option>
                    </select>
                </div>
                <div class="search_field">
                    <label for="sido-select" class="sound_only">시/도 선택</label>
                    <select name="sido" id="sido-select" class="text">
                        <option value="">시/도</option>
                    </select>
                </div>
                <div class="search_field">
                    <label for="gugun-select" class="sound_only">군/구 선택</label>
                    <select name="gugun" id="gugun-select" class="text">
                        <option value="">군/구</option>
                    </select>
                </div>
            </fieldset>

            <div class="search_area">
                <label for="doctor-search-input" class="sound_only">검색어 입력</label>
                <input type="text" id="doctor-search-input" class="text" placeholder="검색어를 입력해 주세요.">
                <button type="submit" class="btn_search">검색</button>
            </div>
        </form>
		
		<div class="national_map_wrap">
			<div class="map_svg_area flex_center">
				<div class="svg_area svg_national">
					<div class="point_tit point_national">전국</div>
					@include('our_neighborhood_doctor.map_national')
				</div>
				<div class="svg_area svg_local">
					<button type="button" class="point_tit point_local">전국 지도보기 <strong>서울시</strong></button>
					@include('our_neighborhood_doctor.map_01')
			        @include('our_neighborhood_doctor.map_02')
			        @include('our_neighborhood_doctor.map_03')
			        @include('our_neighborhood_doctor.map_04')
			        @include('our_neighborhood_doctor.map_05')
			        @include('our_neighborhood_doctor.map_06')
			        @include('our_neighborhood_doctor.map_07')
			        @include('our_neighborhood_doctor.map_08')
			        @include('our_neighborhood_doctor.map_09')
			        @include('our_neighborhood_doctor.map_10')
			        @include('our_neighborhood_doctor.map_11')
			        @include('our_neighborhood_doctor.map_12')
			        @include('our_neighborhood_doctor.map_13')
			        @include('our_neighborhood_doctor.map_14')
			        @include('our_neighborhood_doctor.map_15')
			        @include('our_neighborhood_doctor.map_16')
				</div>
			</div>
			<div class="hospital_list">
				<ul>
					<li>
						<button type="button" class="btn_open" onclick="layerShow('pop_doctor', this);">
							<div class="type">내과</div>
							<h2>서울중앙병원</h2>
							<p class="i1"><span class="sound_only">원장 이름 : </span>홍길동 원장</p>
							<p class="i2"><span class="sound_only">주소 : </span>서울시 송파구 올림픽로 212 2층 세온내과 의원</p>
							<p class="i3"><span class="sound_only">전화번호 : </span><a href="tel:0264717575">02-6471-7575</a></p>
						</button>
					</li>
					<li>
						<button type="button" class="btn_open" onclick="layerShow('pop_doctor', this);">
							<div class="type">내과</div>
							<h2>서울중앙병원</h2>
							<p class="i1"><span class="sound_only">원장 이름 : </span>홍길순 원장</p>
							<p class="i2"><span class="sound_only">주소 : </span>서울시 송파구 올림픽로 212 2층 세온내과 의원</p>
							<p class="i3"><span class="sound_only">전화번호 : </span><a href="tel:0264717575">02-6471-7575</a></p>
						</button>
					</li>
					<li>
						<button type="button" class="btn_open" onclick="layerShow('pop_doctor', this);">
							<div class="type">내과</div>
							<h2>서울중앙병원</h2>
							<p class="i1"><span class="sound_only">원장 이름 : </span>홍길자 원장</p>
							<p class="i2"><span class="sound_only">주소 : </span>서울시 송파구 올림픽로 212 2층 세온내과 의원</p>
							<p class="i3"><span class="sound_only">전화번호 : </span><a href="tel:0264717575">02-6471-7575</a></p>
						</button>
					</li>
					<li>
						<button type="button" class="btn_open" onclick="layerShow('pop_doctor', this);">
							<div class="type">내과</div>
							<h2>서울중앙병원</h2>
							<p class="i1"><span class="sound_only">원장 이름 : </span>홍길덕 원장</p>
							<p class="i2"><span class="sound_only">주소 : </span>서울시 송파구 올림픽로 212 2층 세온내과 의원</p>
							<p class="i3"><span class="sound_only">전화번호 : </span><a href="tel:0264717575">02-6471-7575</a></p>
						</button>
					</li>
				</ul>
			</div>
		</div>
    </div>
</section>
	
</main>

<div class="popup pop_doctor" id="pop_doctor">
	<div class="dm" onclick="layerHide('pop_doctor');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_doctor');">Close</button>
		<h2 class="ptit">우리동네 주치의</h2>
		<div class="scroll">
			<div class="con gbox">
				<div class="doctor_top">
					<div class="imgfit"><img src="/images/img_sample_doctor.jpg" alt=""></div>
					<div class="txt">
						<div class="top">
							<div class="name_hospital">세온내과의원</div>
							<div class="name_doctor">홍길동 선생님</div>
							<a href="#this" target="_blank" class="btn_outlink">홈페이지 바로가기</a>
						</div>
						<div class="btm">
							<p class="i1"><span class="sound_only">주소 : </span>서울시 송파구 올림픽로 212 2층 세온내과 의원</p>
							<p class="i2"><span class="sound_only">전화번호 : </span><a href="tel:0264717575">02-6471-7575</a></p>
						</div>
					</div>
				</div>
				<div class="doctor_con">
					<h3 class="tit">병원소개</h3>
					<p>관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다. 관리자에서 입력한 병원소개 글이 노출됩니다.</p>
					<h3 class="tit">병원 위치 보기</h3>
					<div class="pop_map">
						<div id="daumRoughmapContainer1776648816237" class="root_daum_roughmap root_daum_roughmap_landing"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<!-- 카카오지도 - 팝업 -->
<script charset="UTF-8" class="daum_roughmap_loader_script" src="https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js"></script>

<script>
$(document).ready(function() {
// 지도
    let currentIndex = "";

    const localNames = {
        "01": "서울",
        "02": "경기",
        "03": "인천",
        "04": "강원",
        "05": "충북",
        "06": "충남",
        "07": "세종",
        "08": "경북",
        "09": "대구",
        "10": "전북",
        "11": "광주",
        "12": "전남",
        "13": "경남",
        "14": "울산",
        "15": "부산",
        "16": "제주"
    };

    $('.national_map_svg').on('mouseenter', 'g.map > g, g.name > g', function() {
        const classList = $(this).attr('class').split(' ');
        const targetClass = classList.find(c => c.includes('map') || c.includes('name'));
        const index = targetClass.replace(/[^0-9]/g, '');
        $(`.national_map_svg .map${index}, .national_map_svg .name${index}`).addClass('hover');
    }).on('mouseleave', 'g.map > g, g.name > g', function() {
        $('.national_map_svg g').removeClass('hover');
    });

    $('.national_map_svg').on('click', 'g.map > g, g.name > g', function() {
        const classList = $(this).attr('class').split(' ');
        const targetClass = classList.find(c => c.includes('map') || c.includes('name'));
        currentIndex = targetClass.replace(/[^0-9]/g, '');
        
        const selectedName = localNames[currentIndex] || "";
        $('.point_local strong').text(selectedName);

        $('g').removeClass('hover');

        $('.svg_national').hide();
        $('.svg_local').addClass('show');
        $('.point_local').addClass('show');
        
        $('.svg_local .map_svg').removeClass('show');
        $(`.map_svg${currentIndex}`).addClass('show');
    });

    $('.point_local').on('click', function(){
        $('g').removeClass('hover').removeClass('click');
        
        $('.svg_national').show();
        $('.point_national').removeClass('hide');
        $('.svg_local').removeClass('show');
        $('.svg_local .map_svg').removeClass('show');
        $(this).removeClass('show');
    });

    $('.svg_local').on('mouseenter', 'g.map > g, g.name > g', function() {
        const classList = $(this).attr('class').split(' ');
        const targetClass = classList.find(c => c.includes('map') || c.includes('name'));
        const index = targetClass.replace(/[^0-9]/g, '');
        const $parentSvg = $(this).closest('svg');
        $parentSvg.find(`.map${index}, .name${index}`).addClass('hover');
    }).on('mouseleave', 'g.map > g, g.name > g', function() {
        $('.svg_local g').removeClass('hover');
    });

    $('.svg_local').on('click', 'g.map > g, g.name > g', function() {
        const classList = $(this).attr('class').split(' ');
        const targetClass = classList.find(c => c.includes('map') || c.includes('name'));
        const index = targetClass.replace(/[^0-9]/g, '');
        const $parentSvg = $(this).closest('svg');
        
        $parentSvg.find('g').removeClass('click');
        $parentSvg.find(`.map${index}, .name${index}`).addClass('click');
    });
// 팝업
	var lastFocusedElement;

    window.layerShow = function(id, obj) {
        lastFocusedElement = document.activeElement;

        if (obj) {
            var $btn = $(obj);
            var hospitalName = $btn.find('h2').text();
            var doctorName = $btn.find('.i1').text().replace('원장 이름 : ', '').trim();
            var address = $btn.find('.i2').text().replace('주소 : ', '').trim();
            var tel = $btn.find('.i3').text().replace('전화번호 : ', '').trim();
            var telHref = $btn.find('.i3 a').attr('href');

            var $pop = $("#" + id);
            $pop.find('.name_hospital').text(hospitalName);
            $pop.find('.name_doctor').text(doctorName);
            $pop.find('.btm .i1').html('<span class="sound_only">주소 : </span>' + address);
            $pop.find('.btm .i2').html('<span class="sound_only">전화번호 : </span><a href="' + telHref + '">' + tel + '</a>');
        }

        $("#" + id).fadeIn(300, function() {
            $(this).find(".btn_close").focus();
            
            // 팝업이 다 열린 후 지도를 생성 (id가 pop_doctor일 때만 실행)
            if (id === 'pop_doctor') {
                drawMap();
            }
        });
    };

    window.layerHide = function(id) {
        $("#" + id).fadeOut(300, function() {
            // 지도가 중복 생성되는 것을 방지하기 위해 닫을 때 컨테이너 비우기
            if (id === 'pop_doctor') {
                $('#daumRoughmapContainer1776648816237').empty();
            }
            if (lastFocusedElement) lastFocusedElement.focus();
        });
    };

    // 지도 그리기 함수
    function drawMap() {
        // 중복 실행 방지를 위해 비우고 새로 생성
        $('#daumRoughmapContainer1776648816237').empty();
        
        new daum.roughmap.Lander({
            "timestamp" : "1776648816237",
            "key" : "me5vcjov52w",
            "mapWidth" : "880", // 필요에 따라 컨테이너 크기에 맞춰 조절
            "mapHeight" : "256"
        }).render();
    }
});
</script>
@endpush
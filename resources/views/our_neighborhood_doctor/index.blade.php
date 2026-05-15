@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
    $filterSido = $filters['sido'] ?? '';
    $filterSigungu = $filters['sigungu'] ?? '';
    $filterCategoryId = $filters['doctor_category_id'] ?? '';
    $filterKeyword = $filters['keyword'] ?? '';
    $filterMapIndex = $filters['map_index'] ?? '';
    $sigunguList = $filterSido !== '' && isset($sigungu_by_sido[$filterSido])
        ? $sigungu_by_sido[$filterSido]
        : [];
    $showLocalMap = $selected_map_index !== null && $selected_map_index !== '';
@endphp
<main class="sub_area">

<section class="scon our_doctor_wrap" aria-labelledby="our-neighborhood-doctor-heading">
    <div class="inner">
        <h1 class="sub_title" id="our-neighborhood-doctor-heading">{{ $sName }}</h1>

        <form
            class="board_top search_wrap"
            role="search"
            aria-label="의료진 검색"
            method="GET"
            action="{{ route('our_neighborhood_doctor.index') }}"
            id="our-doctor-search-form"
        >
            <fieldset>
                <legend class="sound_only">검색 조건 선택</legend>
                <div class="search_field">
                    <label for="subject-select" class="sound_only">진료 과목 선택</label>
                    <select name="doctor_category_id" id="subject-select" class="text">
                        <option value="">진료 과목</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $filterCategoryId === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="search_field">
                    <label for="sido-select" class="sound_only">시/도 선택</label>
                    <select name="sido" id="sido-select" class="text">
                        <option value="">시/도</option>
                        @foreach ($sidos as $sidoOption)
                            <option value="{{ $sidoOption }}" @selected($filterSido === $sidoOption)>{{ $sidoOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="search_field">
                    <label for="gugun-select" class="sound_only">군/구 선택</label>
                    <select name="sigungu" id="gugun-select" class="text">
                        <option value="">군/구</option>
                        @foreach ($sigunguList as $sigunguOption)
                            <option value="{{ $sigunguOption }}" @selected($filterSigungu === $sigunguOption)>{{ $sigunguOption }}</option>
                        @endforeach
                        @if ($filterSigungu !== '' && ! in_array($filterSigungu, $sigunguList, true))
                            <option value="{{ $filterSigungu }}" selected>{{ $filterSigungu }}</option>
                        @endif
                    </select>
                </div>
            </fieldset>

            <div class="search_area">
                <label for="doctor-search-input" class="sound_only">검색어 입력</label>
                <input
                    type="text"
                    name="keyword"
                    id="doctor-search-input"
                    class="text"
                    placeholder="검색어를 입력해 주세요."
                    value="{{ $filterKeyword }}"
                >
                <button type="submit" class="btn_search">검색</button>
            </div>
            <input type="hidden" name="map_index" id="map-index-input" value="{{ $filterMapIndex }}">
        </form>

        <div
            class="national_map_wrap"
            id="our-doctor-map-root"
            data-selected-map-index="{{ $selected_map_index ?? '' }}"
            data-local-map-label="{{ $local_map_label ?? '서울' }}"
            data-sigungu-json="{{ json_encode($sigungu_by_sido, JSON_UNESCAPED_UNICODE) }}"
        >
            <div class="map_svg_area flex_center">
                <div class="svg_area svg_national @if($showLocalMap) is-map-hidden @endif">
                    <div class="point_tit point_national">전국</div>
                    @include('our_neighborhood_doctor.map_national')
                </div>
                <div class="svg_area svg_local @if($showLocalMap) show @endif">
                    <button type="button" class="point_tit point_local @if($showLocalMap) show @endif">
                        전국 지도보기 <strong>{{ $local_map_label }}</strong>
                    </button>
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
                    @forelse ($doctors as $doctor)
                        @php
                            $categoryLabel = $doctor->doctorCategories->first()?->name ?? '';
                            $fullAddress = trim(($doctor->address ?? '') . ' ' . ($doctor->address_detail ?? ''));
                            $phoneDigits = preg_replace('/\D+/', '', (string) $doctor->phone);
                            $phoneHref = $phoneDigits !== '' ? 'tel:' . $phoneDigits : '#';
                        @endphp
                        <li>
                            <button
                                type="button"
                                class="btn_open js-doctor-popup-open"
                                data-doctor-id="{{ $doctor->id }}"
                                data-popup-url="{{ route('our_neighborhood_doctor.popup', $doctor) }}"
                            >
                                @if ($categoryLabel !== '')
                                    <div class="type">{{ $categoryLabel }}</div>
                                @endif
                                <h2>{{ $doctor->hospital_name }}</h2>
                                <p class="i1">
                                    <span class="sound_only">원장 이름 : </span>{{ $doctor->doctor_name }}
                                </p>
                                <p class="i2">
                                    <span class="sound_only">주소 : </span>{{ $fullAddress }}
                                </p>
                                <p class="i3">
                                    <span class="sound_only">전화번호 : </span>
                                    <a href="{{ $phoneHref }}">{{ $doctor->phone }}</a>
                                </p>
                            </button>
                        </li>
                    @empty
                        <li class="our_doctor_empty">
                            <p>검색 조건에 해당하는 우리동네 주치의가 없습니다.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</section>

</main>

@php
    $roughmapTimestamp = (string) config('local_doctor_map.roughmap.timestamp', '1776648816237');
    $roughmapKey = (string) config('local_doctor_map.roughmap.key', 'me5vcjov52w');
@endphp
<div class="popup pop_doctor" id="pop_doctor" data-roughmap-timestamp="{{ $roughmapTimestamp }}" data-roughmap-key="{{ $roughmapKey }}">
    <div class="dm js-doctor-popup-close"></div>
    <div class="inbox">
        <button type="button" class="btn_close js-doctor-popup-close">Close</button>
        <h2 class="ptit">우리동네 주치의</h2>
        <div class="scroll">
            <div class="con gbox">
                <div class="doctor_top">
                    <div class="imgfit">
                        <img src="/images/img_sample_doctor.jpg" alt="" class="js-popup-photo">
                    </div>
                    <div class="txt">
                        <div class="top">
                            <div class="name_hospital js-popup-hospital"></div>
                            <div class="name_doctor js-popup-doctor"></div>
                            <a href="#" target="_blank" rel="noopener" class="btn_outlink js-popup-homepage">홈페이지 바로가기</a>
                        </div>
                        <div class="btm">
                            <p class="i1 js-popup-address"></p>
                            <p class="i2 js-popup-phone"></p>
                        </div>
                    </div>
                </div>
                <div class="doctor_con">
                    <h3 class="tit">병원소개</h3>
                    <div class="js-popup-introduction"></div>
                    <h3 class="tit">병원 위치 보기</h3>
                    <div class="pop_map">
                        <div
                            id="daumRoughmapContainer{{ $roughmapTimestamp }}"
                            class="root_daum_roughmap root_daum_roughmap_landing js-roughmap-container"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script charset="UTF-8" class="daum_roughmap_loader_script" src="https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js"></script>
<script src="{{ asset('js/frontend/our-neighborhood-doctor.js') }}"></script>
@endpush

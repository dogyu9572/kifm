@extends('layouts.frontend')
@section('title', $gName)
@section('content')
<main class="sub_area">

<section class="scon total_search_wrap" aria-labelledby="total-search-heading">
    <div class="inner">
        <h1 class="sub_title no_ico" id="total-search-heading">‘<strong class="c_iden">기능의학</strong>’ 에 대한 검색 결과입니다.</h1>

        <nav class="tabs_nav" aria-label="검색 결과 필터">
            <ul class="tabs full_line mb" role="tablist">
                <li class="on" role="presentation"><a href="#this" role="tab" aria-selected="true">전체 (128)</a></li>
                <li role="presentation"><a href="#this" role="tab" aria-selected="false">학술대회 (2)</a></li>
                <li role="presentation"><a href="#this" role="tab" aria-selected="false">연수교육 (5)</a></li>
                <li role="presentation"><a href="#this" role="tab" aria-selected="false">온라인 아카데미 (102)</a></li>
                <li role="presentation"><a href="#this" role="tab" aria-selected="false">게시판 (3)</a></li>
            </ul>
        </nav>
        
        <article class="search_group type1">
            <div class="tit_area">
				<h2 class="group_title">학술대회 <span class="count">(총 <span class="c_red">2</span>건)</span></h2>
				<a href="#this" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                <li>
                    <a href="#this">
                        <span class="state ing"><span class="sound_only">현재 상태:</span>모집 중</span>
                        <h3>2026년 대한기능의학회 춘계 학술대회</h3>
                        <div class="info_flex">
                            <p><span class="label">일시:</span> 2026.04.12~2026.05.13</p>
                            <p><span class="label">장소:</span> 서울 코엑스 컨퍼런스룸</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#this">
                        <span class="state ing">모집 중</span>
                        <h3>2026년 대한기능의학회 춘계 학술대회</h3>
                        <div class="info_flex">
                            <p><span class="label">일시:</span> 2026.04.12~2026.05.13</p>
                            <p><span class="label">장소:</span> 서울 코엑스 컨퍼런스룸</p>
                        </div>
                    </a>
                </li>
            </ul>
        </article>
        
        <article class="search_group type1">
            <div class="tit_area">
				<h2 class="group_title">연수교육 <span class="count">(총 <span class="c_red">5</span>건)</span></h2>
				<a href="#this" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                <li>
                    <a href="#this">
                        <span class="state ing"><span class="sound_only">현재 상태:</span>모집 중</span>
                        <h3>기능의학 핵심 기초과정 제15기</h3>
                        <div class="info_flex">
                            <p><span class="label">일시:</span> 2026.04.12~2026.05.13</p>
                            <p><span class="label">장소:</span> 서울 코엑스 컨퍼런스룸</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#this">
                        <span class="state ing">모집 중</span>
                        <h3>영양치료 전문가 심화교육 (비타민/미네랄)</h3>
                        <div class="info_flex">
                            <p><span class="label">일시:</span> 2026.04.12~2026.05.13</p>
                            <p><span class="label">장소:</span> 서울 코엑스 컨퍼런스룸</p>
                        </div>
                    </a>
                </li>
            </ul>
        </article>
        
        <article class="search_group type1">
            <div class="tit_area">
				<h2 class="group_title">온라인 아카데미 <span class="count">(총 <span class="c_red">102</span>건)</span></h2>
				<a href="#this" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                <li>
                    <a href="#this">
                        <span class="state ing"><span class="sound_only">현재 상태:</span>모집 중</span>
                        <h3>기능의학 핵심 기초과정 제15기</h3>
                        <div class="info_flex">
                            <p><span class="label">일시:</span> 2026.04.12~2026.05.13</p>
                            <p><span class="label">장소:</span> 서울 코엑스 컨퍼런스룸</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#this">
                        <span class="state ing">모집 중</span>
                        <h3>영양치료 전문가 심화교육 (비타민/미네랄)</h3>
                        <div class="info_flex">
                            <p><span class="label">일시:</span> 2026.04.12~2026.05.13</p>
                            <p><span class="label">장소:</span> 서울 코엑스 컨퍼런스룸</p>
                        </div>
                    </a>
                </li>
            </ul>
        </article>
        
        <article class="search_group type2">
            <div class="tit_area">
				<h2 class="group_title">공지사항 <span class="count">(총 <span class="c_red">2</span>건)</span></h2>
				<a href="#this" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                <li>
                    <a href="#this">
                        <h3>기능의학적 생활 습관 개선을 위한 환자용 안내문</h3>
                        <p>진료 시 환자분들께 배포하실 수 있는 기능의학 기반 생활 습관 가이드라인입니다. 식단 및 스트레스 관리 항목을 포함하고 있으며.,...</p>
                    </a>
                </li>
                <li>
                    <a href="#this">
                        <h3>기능의학적 생활 습관 개선을 위한 환자용 안내문</h3>
                        <p>진료 시 환자분들께 배포하실 수 있는 기능의학 기반 생활 습관 가이드라인입니다. 식단 및 스트레스 관리 항목을 포함하고 있으며.,...</p>
                    </a>
                </li>
            </ul>
        </article>
        
        <article class="search_group type2">
            <div class="tit_area">
				<h2 class="group_title">공지사항 <span class="count">(총 <span class="c_red">2</span>건)</span></h2>
				<a href="#this" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                <li>
                    <a href="#this">
                        <h3>기능의학적 생활 습관 개선을 위한 환자용 안내문</h3>
                        <p>진료 시 환자분들께 배포하실 수 있는 기능의학 기반 생활 습관 가이드라인입니다. 식단 및 스트레스 관리 항목을 포함하고 있으며.,...</p>
                    </a>
                </li>
                <li>
                    <a href="#this">
                        <h3>기능의학적 생활 습관 개선을 위한 환자용 안내문</h3>
                        <p>진료 시 환자분들께 배포하실 수 있는 기능의학 기반 생활 습관 가이드라인입니다. 식단 및 스트레스 관리 항목을 포함하고 있으며.,...</p>
                    </a>
                </li>
            </ul>
        </article>
        
        <article class="search_group type2">
            <div class="tit_area">
				<h2 class="group_title">공지사항 <span class="count">(총 <span class="c_red">2</span>건)</span></h2>
				<a href="#this" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                <li>
                    <a href="#this">
                        <h3>기능의학적 생활 습관 개선을 위한 환자용 안내문</h3>
                        <p>진료 시 환자분들께 배포하실 수 있는 기능의학 기반 생활 습관 가이드라인입니다. 식단 및 스트레스 관리 항목을 포함하고 있으며.,...</p>
                    </a>
                </li>
                <li>
                    <a href="#this">
                        <h3>기능의학적 생활 습관 개선을 위한 환자용 안내문</h3>
                        <p>진료 시 환자분들께 배포하실 수 있는 기능의학 기반 생활 습관 가이드라인입니다. 식단 및 스트레스 관리 항목을 포함하고 있으며.,...</p>
                    </a>
                </li>
            </ul>
        </article>
        
    </div>
</section>
    
</main>
@endsection
@extends('layouts.frontend')
@section('title', $gName)
@section('content')
<main class="sub_area">

<section class="scon total_search_wrap" aria-labelledby="total-search-heading">
    <div class="inner">
        <h1 class="sub_title no_ico" id="total-search-heading">
            @if($keyword !== '')
                ‘<strong class="c_iden">{{ $keyword }}</strong>’ 에 대한 검색 결과입니다.
            @else
                전체 검색 결과입니다.
            @endif
        </h1>

        <nav class="tabs_nav" aria-label="검색 결과 필터">
            <ul class="tabs full_line mb" role="tablist" data-total-search-tabs>
                <li class="on" role="presentation"><a href="#total-search-heading" role="tab" aria-selected="true">전체 ({{ $totalCount }})</a></li>
                @foreach($contentGroups as $group)
                <li role="presentation"><a href="#{{ $group['anchor_id'] }}" role="tab" aria-selected="false">{{ $group['label'] }} ({{ $group['total'] }})</a></li>
                @endforeach
                @foreach($boardGroups as $group)
                <li role="presentation"><a href="#{{ $group['anchor_id'] }}" role="tab" aria-selected="false">{{ $group['label'] }} ({{ $group['total'] }})</a></li>
                @endforeach
            </ul>
        </nav>

        @foreach($contentGroups as $group)
        <article class="search_group type1" id="{{ $group['anchor_id'] }}" data-total-search-group>
            <div class="tit_area">
				<h2 class="group_title">{{ $group['label'] }} <span class="count">(총 <span class="c_red">{{ $group['total'] }}</span>건)</span></h2>
				<a href="{{ $group['list_url'] }}" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                @forelse($group['items'] as $item)
                <li>
                    <a href="{{ $item['url'] }}">
                        <span class="state {{ $item['status_class'] }}"><span class="sound_only">현재 상태:</span>{{ $item['status_label'] }}</span>
                        <h3>{{ $item['title'] }}</h3>
                        <div class="info_flex">
                            <p><span class="label">{{ $item['primary_label'] }}</span> {{ $item['primary_text'] }}</p>
                            <p><span class="label">{{ $item['secondary_label'] }}</span> {{ $item['secondary_text'] }}</p>
                        </div>
                    </a>
                </li>
                @empty
                <li>
                    <a href="#this">
                        <span class="state end"><span class="sound_only">현재 상태:</span>검색결과 없음</span>
                        <h3>검색 결과가 없습니다.</h3>
                    </a>
                </li>
                @endforelse
            </ul>
        </article>
        @endforeach

        @foreach($boardGroups as $group)
        <article class="search_group type2" id="{{ $group['anchor_id'] }}" data-total-search-group>
            <div class="tit_area">
				<h2 class="group_title">{{ $group['label'] }} <span class="count">(총 <span class="c_red">{{ $group['total'] }}</span>건)</span></h2>
				<a href="{{ $group['list_url'] }}" class="more" aria-label="더보기">더보기</a>
			</div>
            <ul class="list">
                @forelse($group['items'] as $item)
                <li>
                    <a href="{{ $item['url'] }}">
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['summary'] !== '' ? $item['summary'] : '내용이 없습니다.' }}</p>
                    </a>
                </li>
                @empty
                <li>
                    <a href="#this">
                        <h3>검색 결과가 없습니다.</h3>
                        <p>다른 검색어로 다시 검색해 주세요.</p>
                    </a>
                </li>
                @endforelse
            </ul>
        </article>
        @endforeach

    </div>
</section>

</main>
@endsection

@extends('layouts.frontend')
@section('title', $egName)
@section('gName', $gName)
@section('sName', $geName)
@section('content')
<main class="sub_area">

<section class="scon core_values_wrap" aria-labelledby="core-values-heading">
	<div class="inner">
		<h1 class="sub_title" id="core-values-heading">{{ $geName }}</h1>

		<div class="history_top">
			<div class="history_title">Beyond Symptoms to <strong class="c_iden">Causes</strong>, <br class="mo_vw">Beyond Disease to <strong class="c_iden">People</strong></div>
			<p>The Korean Society for Functional Medicine presents a new paradigm <br class="mo_vw">of medicine that transcends the limits of modern healthcare.</p>
		</div>
	</div>
	<div class="history_img" aria-hidden="true"></div>
	<div class="inner">
		<div class="history_body">
			<h2 class="sound_only">학회 연혁 목록</h2>
			<ul class="history_tabs">
				<li><a href="#history1">2023 ~ Now</a></li>
				<li><a href="#history2">2019 ~ 2022</a></li>
				<li><a href="#history3">2015 ~ 2018</a></li>
				<li><a href="#history4">2013 ~ 2014</a></li>
			</ul>

			@php
				$historyArticles = [
					['id' => 'history1', 'label' => '2023 ~ Now', 'key' => 'history1'],
					['id' => 'history2', 'label' => '2019 ~ 2022', 'key' => 'history2'],
					['id' => 'history3', 'label' => '2015 ~ 2018', 'key' => 'history3'],
					['id' => 'history4', 'label' => '2013 ~ 2014', 'key' => 'history4'],
				];
			@endphp
			@foreach ($historyArticles as $article)
				<article id="{{ $article['id'] }}">
					<h3>{{ $article['label'] }}</h3>
					<dl>
						@foreach ($historyGroups[$article['key']] ?? [] as $item)
							<div>
								<dt>{{ $item['year'] }}. {{ str_pad((string) $item['month'], 2, '0', STR_PAD_LEFT) }}</dt>
								@foreach ($item['contents'] ?? [] as $content)
									<dd>{!! $content !!}</dd>
								@endforeach
							</div>
						@endforeach
					</dl>
				</article>
			@endforeach
		</div>
	</div>
</section>

</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $header = $('.header');
    const $historyBody = $('.history_body');
    const $tabs = $('.history_tabs');
    const $tabItems = $('.history_tabs li');
    const $tabLinks = $('.history_tabs a');
    const $articles = $('article[id^="history"]');
    function scrollTabToFront($activeItem) {
        if (!$activeItem.length) return;
        let tabsScrollLeft = $tabs.scrollLeft();
        let tabsOffsetLeft = $tabs.offset().left;
        let itemOffsetLeft = $activeItem.offset().left;
        let targetScrollLeft = tabsScrollLeft + itemOffsetLeft - tabsOffsetLeft;
        $tabs.stop().animate({
            scrollLeft: targetScrollLeft
        }, 300);
    }

    $(window).on('scroll', function() {
        let scrollTop = $(window).scrollTop();
        let headerH = $header.outerHeight();
        let tabsH = $tabs.outerHeight();
        let triggerPoint = headerH + tabsH + 50;
        
        if (scrollTop >= $historyBody.offset().top - headerH) {
            $historyBody.addClass('fixed');
        } else {
            $historyBody.removeClass('fixed');
        }
        
        $articles.each(function(index) {
            let targetPos = $(this).offset().top - triggerPoint;
            let nextTargetPos = $articles.eq(index + 1).length
                                ? $articles.eq(index + 1).offset().top - triggerPoint
                                : $(document).height();
            if (scrollTop >= targetPos && scrollTop < nextTargetPos) {
                $articles.removeClass('on');
                $(this).addClass('on');

                $tabItems.removeClass('on');
                let $currentTab = $tabItems.eq(index);
                $currentTab.addClass('on');
                scrollTabToFront($currentTab);
            }
        });
        
        if (scrollTop < $articles.first().offset().top - triggerPoint) {
            $articles.removeClass('on');
            $tabItems.removeClass('on');
        }
    });

    $tabLinks.on('click', function(e) {
        e.preventDefault();
        const targetId = $(this).attr('href');
        const $targetElement = $(targetId);
        
        if ($targetElement.length) {
            let headerH = $header.outerHeight();
            let tabsH = $tabs.outerHeight();
            let movePos = $targetElement.offset().top - (headerH + tabsH);
            
            $('html, body').stop().animate({
                scrollTop: movePos
            }, 500);
            let $currentTab = $(this).parent('li');
            scrollTabToFront($currentTab);

            if (history.pushState) {
                history.pushState(null, null, window.location.pathname);
            }
        }
    });
});
</script>
@endpush
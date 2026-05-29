$(document).ready(function(){
//헤더
	function checkHeader() {
		if ($(window).scrollTop() > 50) {
			$(".header").addClass("fixed");
		} else {
			$(".header").removeClass("fixed");
		}
	}
	$(window).on('scroll', checkHeader);
	checkHeader();
	$(".header").mouseover(function(){
		$(".header").stop(false,true).addClass("hover");
	});
	$(".header").mouseleave(function(){
		$(".header").stop(false,true).removeClass("hover");
	});
//햄버거
	function setSitemapTabindex(isOpen) {
		const sitemapLinks = document.querySelectorAll('.header .sitemap a, .header .sitemap button');
		sitemapLinks.forEach(link => {
			link.setAttribute('tabindex', isOpen ? '0' : '-1');
		});
	}
	setSitemapTabindex(false);
	$(".btn_menu").click(function(){
		$("html,body").stop(false,true).toggleClass("over_h");
		$(".header").stop(false,true).toggleClass("on");
		
		var isOpen = $(".header").hasClass("on");
		$(this).attr("aria-expanded", isOpen);
		
		if (isOpen) {
			$(this).attr("aria-label", "전체 메뉴 닫기");
		} else {
			$(this).attr("aria-label", "전체 메뉴 열기");
		}
		setSitemapTabindex(isOpen);
	});
	$(".header .sitemap .bg, .header .sitemap .btn_menu_close").click(function(){
		$("html,body").removeClass("over_h");
		$(".header").removeClass("on");
		setSitemapTabindex(false);
		$(".btn_menu").attr("aria-expanded", false).attr("aria-label", "전체 메뉴 열기");
		$(".header .btm .center").removeClass("on");
	});

//footer
	//gotop
	var speed = 500;
	$(".gotop").css("cursor", "pointer").click(function(){
		$('body, html').animate({scrollTop:0}, speed);
	});

	$(window).scroll(function() {
		const isIndexPage = window.location.pathname === '/';

		if (isIndexPage) {
			const $mainService = $(".main_service");
			if ($mainService.length > 0) {
				const serviceTop = $mainService.offset().top;
				if ($(window).scrollTop() >= serviceTop) {
					$(".footer").addClass("fixed");
				} else {
					$(".footer").removeClass("fixed");
				}
			}
		} else {
			if ($(window).scrollTop() > 100) {
				$(".footer").addClass("fixed");
			} else {
				$(".footer").removeClass("fixed");
			}
		}
	});

	$(window).on("scroll resize", function () {
		let windowBottom = $(window).scrollTop() + $(window).height();
		let $point = $(".footer .point");
		if ($point.length > 0) {
			let pointTop = $point.offset().top;
			if (windowBottom >= pointTop) {
				$(".footer").addClass("unfixed");
			} else {
				$(".footer").removeClass("unfixed");
			}
		}
	});
	//footer_slide
    const swiper = new Swiper('#fbanner-swiper', {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        slidesPerView: 'auto',
        spaceBetween: 0,
        a11y: {
            prevSlideMessage: '이전 슬라이드',
            nextSlideMessage: '다음 슬라이드',
        }
    });
    $('.fbanner .prev').on('click', function () {
        swiper.slidePrev();
    });
    $('.fbanner .next').on('click', function () {
        swiper.slideNext();
    });
    let isPlaying = true;
    $('.fbanner .papl').on('click', function () {
        const $btn = $(this);

        if (isPlaying) {
            swiper.autoplay.stop();
            $btn.attr('aria-label', '슬라이드 재생')
                .attr('aria-pressed', 'true')
                .addClass('is_paused');
        } else {
            swiper.autoplay.start();
            $btn.attr('aria-label', '슬라이드 정지')
                .attr('aria-pressed', 'false')
                .removeClass('is_paused');
        }
        isPlaying = !isPlaying;
    });

//sub_menu
	$('.svisual .sub_menu_area .menu .btn').on('click', function() {
		const $btn = $(this);
		const $parentMenu = $btn.closest('.menu');
		const $menuList = $btn.next('ul');
		const isExpanded = $btn.attr('aria-expanded') === 'true';
		$('.svisual .sub_menu_area .menu .btn').not($btn).each(function() {
			$(this).attr('aria-expanded', 'false').closest('.menu').removeClass('on');
			$(this).next('ul').slideUp();
		});
		$btn.attr('aria-expanded', !isExpanded);
		$parentMenu.toggleClass('on', !isExpanded);
		
		$menuList.slideToggle(300, function() {
			if (!isExpanded) {
				$menuList.find('li:first-child a').focus();
			}
		});
	});

	$('.svisual .sub_menu_area .menu ul').on('keydown', function(e) {
		if (e.keyCode === 27) {
			const $btn = $(this).prev('.btn');
			const $parentMenu = $(this).closest('.menu');
			$(this).slideUp(200, function() {
				$btn.attr('aria-expanded', 'false').focus();
				$parentMenu.removeClass('on');
			});
		}
	});

//mobile
	$(".header .sitemap .menu > a").click(function(e){
		if (window.matchMedia("(max-width: 1023px)").matches && $(this).next(".snb").length > 0) {
			e.preventDefault();
			$(this).next(".snb").stop(true,true).toggle().parent().stop(true,true).toggleClass("open").siblings().removeClass("on open").children(".snb").hide();
		}
	});
	$(".btn_search_mobile").click(function(){
		$(".header .btm .center").fadeToggle("fast");
	});
//브라우저 사이즈
	let vh = window.innerHeight * 0.01; 
	document.documentElement.style.setProperty('--vh', `${vh}px`);
//화면 리사이즈시 변경 
	window.addEventListener('resize', () => {
		let vh = window.innerHeight * 0.01; 
		document.documentElement.style.setProperty('--vh', `${vh}px`);
	});
	window.addEventListener('touchend', () => {
		let vh = window.innerHeight * 0.01;
		document.documentElement.style.setProperty('--vh', `${vh}px`);
	});
//아이폰 노치 설정
	(function(){
		const ua = navigator.userAgent;
		const isIOS = /iPhone|iPad|iPod/i.test(ua);
		const isWebView = !/Safari/i.test(ua);

		if (isIOS && isWebView) {
			document.body.classList.add('ios_safe');
		}
	})();

	const captchaRefreshButton = document.querySelector('[data-captcha-refresh]');
	const captchaImage = document.querySelector('[data-captcha-image]');

	if (captchaRefreshButton && captchaImage) {
		captchaRefreshButton.addEventListener('click', function() {
			const baseUrl = captchaImage.getAttribute('data-src') || captchaImage.getAttribute('src');
			const separator = baseUrl.indexOf('?') === -1 ? '?' : '&';
			captchaImage.setAttribute('src', baseUrl + separator + 't=' + Date.now());
		});
	}

	if (document.querySelector('.online_academy_wrap')) {
		const academyScript = document.createElement('script');
		academyScript.src = '/js/frontend/online-academy.js';
		document.body.appendChild(academyScript);
	}
});

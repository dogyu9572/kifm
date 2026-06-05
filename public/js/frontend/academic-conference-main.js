(function ($) {
    $(function () {
        $('[data-visual-bg]').each(function () {
            var bg = $(this).data('visual-bg');

            if (bg) {
                this.style.backgroundImage = "url('" + bg + "')";
            }
        });

        if (typeof Swiper === 'undefined') {
            return;
        }

        $('.mc01_slide').each(function () {
            var slideCount = $(this).find('.swiper-slide').length;

            new Swiper(this, {
                slidesPerView: 1.2,
                spaceBetween: 12,
                loop: slideCount > 4,
                centeredSlides: false,
                breakpoints: {
                    768: {
                        slidesPerView: 2.5,
                        spaceBetween: 24,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 28,
                    },
                    1440: {
                        slidesPerView: 4,
                        spaceBetween: 32,
                    },
                },
                navigation: {
                    nextEl: '.mc01 .arrow.next',
                    prevEl: '.mc01 .arrow.prev',
                },
            });
        });

        $('.sponsors_slide').each(function () {
            var $parent = $(this).closest('.slide_area');
            var slideCount = $(this).find('.swiper-slide').length;
            var sponsorSwiper = new Swiper(this, {
                slidesPerView: 2,
                spaceBetween: 10,
                loop: slideCount > 5,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                    1200: {
                        slidesPerView: 5,
                        spaceBetween: 18,
                    },
                    1440: {
                        slidesPerView: 5,
                        spaceBetween: 24,
                    },
                },
                navigation: {
                    nextEl: $parent.find('.arrow.next')[0],
                    prevEl: $parent.find('.arrow.prev')[0],
                },
            });

            $parent.find('.papl.pause').on('click', function () {
                sponsorSwiper.autoplay.stop();
                $(this).hide();
                $parent.find('.papl.play').show().focus();
            });

            $parent.find('.papl.play').on('click', function () {
                sponsorSwiper.autoplay.start();
                $(this).hide();
                $parent.find('.papl.pause').show().focus();
            });
            $parent.find('.papl.play').hide();
        });
    });
})(jQuery);

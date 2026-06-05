(function ($) {
    'use strict';

    function bindProgramTabs() {
        var $window = $(window);
        var $header = $('header');
        var $programArea = $('[data-program-area]');

        if (! $programArea.length) {
            return;
        }

        var $tabsArea = $programArea.find('.tabs');
        var $tabs = $tabsArea.find('li');
        var $sections = $programArea.find('.session_area');
        var initialProgramTop = $programArea.offset().top;

        $programArea.find('[data-program-tab]').on('click', function (e) {
            e.preventDefault();

            var $targetSection = $($(this).attr('href'));
            if (! $targetSection.length) {
                return;
            }

            var headerHeight = $header.outerHeight() || 0;
            var tabsHeight = $tabsArea.outerHeight() || 0;
            var scrollTarget = $targetSection.offset().top - headerHeight - tabsHeight - 10;

            $('html, body').stop().animate({
                scrollTop: scrollTarget
            }, 500);
        });

        $window.on('resize', function () {
            initialProgramTop = $programArea.offset().top;
        });

        $window.on('scroll', function () {
            var scrollTop = $window.scrollTop();
            var headerHeight = $header.outerHeight() || 0;
            var tabsHeight = $tabsArea.outerHeight() || 0;

            if (scrollTop + headerHeight >= initialProgramTop) {
                $programArea.addClass('fixed');
                $programArea.css('top', headerHeight + 'px');
            } else {
                $programArea.removeClass('fixed');
                $programArea.css('top', '');
            }

            var currentIndex = -1;
            var triggerPoint = headerHeight + tabsHeight + 20;

            $sections.each(function (index) {
                if (scrollTop >= $(this).offset().top - triggerPoint) {
                    currentIndex = index;
                }
            });

            if (currentIndex !== -1) {
                var $activeTab = $tabs.eq(currentIndex);
                
                // 활성화된 탭이 변경될 때만 스크롤 실행 (버벅임 방지)
                if (!$activeTab.hasClass('on')) {
                    $tabs.removeClass('on');
                    $activeTab.addClass('on');

                    // [수정] 1023px 이하일 때 가로 스크롤 이동 (패딩 감안)
                    if ($window.width() <= 1023) {
                        // .tabs의 padding-left 값을 가져옴 (숫자만 추출, 없으면 0)
                        var tabsPaddingLeft = parseInt($tabsArea.css('padding-left'), 10) || 0;
                        
                        // 현재 탭의 부모(.tabs) 내부에서의 상대적 왼쪽 위치
                        var tabLeft = $activeTab.position().left;
                        var currentScrollLeft = $tabsArea.scrollLeft();
                        
                        // [핵심] 현재 스크롤 위치 + 탭의 위치에서 패딩만큼을 빼주어 여백을 유지함
                        var targetScrollLeft = currentScrollLeft + tabLeft - tabsPaddingLeft;

                        // 부드럽게 가로 스크롤 이동
                        $tabsArea.stop().animate({
                            scrollLeft: targetScrollLeft
                        }, 300);
                    }
                }
            } else {
                $tabs.removeClass('on');
            }
        });
    }

    function bindSpeakerPopups() {
        var lastFocusedElement = null;

        function openPopup(id) {
            var $popup = $('#' + id);

            if (! $popup.length) {
                return;
            }

            lastFocusedElement = document.activeElement;
            $popup.fadeIn(300, function () {
                $(this).find('.btn_close').trigger('focus');
            });
        }

        function closePopup($popup) {
            $popup.fadeOut(300, function () {
                if (lastFocusedElement) {
                    lastFocusedElement.focus();
                }
            });
        }

        $(document).on('click', '[data-speaker-popup-target]', function () {
            openPopup($(this).data('speaker-popup-target'));
        });

        $(document).on('click', '[data-speaker-popup-close]', function () {
            closePopup($(this).closest('[data-speaker-popup]'));
        });

        $(document).on('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }

            var $visiblePopup = $('[data-speaker-popup]:visible').last();
            if ($visiblePopup.length) {
                closePopup($visiblePopup);
            }
        });
    }

    $(function () {
        bindProgramTabs();
        bindSpeakerPopups();
    });
})(jQuery);
(function ($) {
    'use strict';

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function parseDate(value) {
        if (!value) {
            return null;
        }

        var parts = String(value).split('-').map(function (part) {
            return parseInt(part, 10);
        });

        if (parts.length < 3 || parts.some(isNaN)) {
            return null;
        }

        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function formatDateKey(date) {
        return [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0')
        ].join('-');
    }

    function formatPeriod(start, end) {
        if (!start) {
            return '';
        }
        if (end && end !== start) {
            return start + ' ~ ' + end;
        }

        return start;
    }

    function schedulesForDate(schedules, dateStr) {
        var target = parseDate(dateStr);
        if (!target) {
            return [];
        }

        return schedules.filter(function (schedule) {
            var start = parseDate(schedule.start);
            var end = parseDate(schedule.end || schedule.start);

            return start && end && target >= start && target <= end;
        });
    }

    function initMainVisual() {
        if (typeof Swiper === 'undefined' || !document.querySelector('.mvisual-swiper')) {
            return;
        }

        var mvisualSwiper = new Swiper('.mvisual-swiper', {
            loop: document.querySelectorAll('.mvisual-swiper .swiper-slide').length > 1,
            speed: 800,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },
            pagination: {
                el: '.mvisual-control .paging',
                type: 'fraction',
                formatFractionCurrent: function (number) { return String(number).padStart(2, '0'); },
                formatFractionTotal: function (number) { return String(number).padStart(2, '0'); }
            },
            navigation: {
                nextEl: '.mvisual-control .next',
                prevEl: '.mvisual-control .prev'
            }
        });

        $('.mvisual-control .papl').on('click', function () {
            var isPressed = $(this).attr('aria-pressed') === 'true';
            if (isPressed) {
                mvisualSwiper.autoplay.start();
                $(this).attr('aria-pressed', 'false').attr('aria-label', '슬라이드 정지').removeClass('stop');
            } else {
                mvisualSwiper.autoplay.stop();
                $(this).attr('aria-pressed', 'true').attr('aria-label', '슬라이드 재생').addClass('stop');
            }
        });
    }

    function initBookSlide() {
        if (typeof Swiper === 'undefined' || !document.querySelector('.book_slide')) {
            return;
        }

        new Swiper('.book_slide', {
            direction: 'vertical',
            loop: document.querySelectorAll('.book_slide .swiper-slide').length > 1,
            spaceBetween: 8,
            slidesPerView: 1,
            navigation: {
                nextEl: '.book_area .next',
                prevEl: '.book_area .prev'
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            }
        });
    }

    function initScheduleSlide() {
        if (typeof Swiper === 'undefined' || !document.querySelector('.schedule_slide')) {
            return;
        }

        var allScheduleItems = [];
        $('.schedule_slide .swiper-slide').each(function () {
            allScheduleItems.push($(this).prop('outerHTML'));
        });

        var scheduleSwiper = new Swiper('.schedule_slide', {
            loop: document.querySelectorAll('.schedule_slide .swiper-slide').length > 3,
            spaceBetween: 8,
            slidesPerView: 'auto',
            breakpoints: {
                768: {
                    slidesPerView: 3,
                    spaceBetween: 16
                }
            },
            navigation: {
                nextEl: '.arrows .next',
                prevEl: '.arrows .prev'
            }
        });

        function rebuildScheduleSlides(month) {
            var filteredItems = allScheduleItems.filter(function (html) {
                var item = $(html);
                var link = item.find('.card-link');
                var itemMonth = link.data('schedule-month');

                return !itemMonth || itemMonth === month;
            });

            if (filteredItems.length === 0) {
                filteredItems = [
                    '<div class="swiper-slide empty"><p class="schedule_empty">해당 월 학술행사가 없습니다.</p></div>'
                ];
            }

            scheduleSwiper.removeAllSlides();
            scheduleSwiper.appendSlide(filteredItems);
            scheduleSwiper.update();
            scheduleSwiper.slideTo(0);
        }

        $('.schedule .month li button').on('click', function () {
            var month = $(this).text();

            $(this).parent('li').addClass('on').siblings().removeClass('on');
            rebuildScheduleSlides(month);
        });

        var currentMonth = new Date().toLocaleString('en-US', { month: 'short' });
        var currentMonthButton = $('.schedule .month li button').filter(function () {
            return $(this).text() === currentMonth;
        }).first();

        if (currentMonthButton.length === 0) {
            currentMonthButton = $('.schedule .month li button').first();
        }

        currentMonthButton.trigger('click');
    }

    function initCalendar() {
        var main = document.querySelector('.main_wrap');
        var rawSchedules = main ? main.getAttribute('data-calendar-schedules') : '[]';
        var schedules = [];
        var date = new Date();
        var realToday = new Date();

        try {
            schedules = JSON.parse(rawSchedules || '[]');
        } catch (error) {
            schedules = [];
        }

        function renderCalendar() {
            var viewYear = date.getFullYear();
            var viewMonth = date.getMonth();
            var prevLast = new Date(viewYear, viewMonth, 0);
            var thisLast = new Date(viewYear, viewMonth + 1, 0);
            var prevDates = [];
            var nextDates = [];
            var thisDates = [];
            var html = '';

            $('.tomonth').text(viewYear + '.' + String(viewMonth + 1).padStart(2, '0'));

            if (prevLast.getDay() !== 6) {
                for (var p = 0; p < prevLast.getDay() + 1; p += 1) {
                    prevDates.unshift(prevLast.getDate() - p);
                }
            }
            for (var n = 1; n < 7 - thisLast.getDay(); n += 1) {
                nextDates.push(n);
            }
            for (var d = 1; d <= thisLast.getDate(); d += 1) {
                thisDates.push(d);
            }

            var dates = prevDates.concat(thisDates, nextDates);
            var firstDateIndex = dates.indexOf(1);
            var lastDateIndex = dates.lastIndexOf(thisLast.getDate());

            dates.forEach(function (day, index) {
                var condition = index >= firstDateIndex && index <= lastDateIndex ? 'this' : 'disabled';
                var dateStr = formatDateKey(new Date(viewYear, viewMonth, day));
                var isToday = viewYear === realToday.getFullYear()
                    && viewMonth === realToday.getMonth()
                    && day === realToday.getDate()
                    && condition === 'this';
                var hasEvent = condition === 'this' && schedulesForDate(schedules, dateStr).length > 0;

                if (index % 7 === 0) {
                    html += '<tr>';
                }
                html += '<td class="' + condition + (isToday ? ' today' : '') + (hasEvent ? ' event' : '') + '" data-date="' + dateStr + '"><button type="button"><span>' + day + '</span></button></td>';
                if (index % 7 === 6) {
                    html += '</tr>';
                }
            });

            $('.month_area .month tbody').html(html);
        }

        renderCalendar();

        $('.select_month .prev').on('click', function () {
            date.setMonth(date.getMonth() - 1);
            renderCalendar();
        });
        $('.select_month .next').on('click', function () {
            date.setMonth(date.getMonth() + 1);
            renderCalendar();
        });

        $(document).on('click', '.month_area .month tbody button', function () {
            var targetTd = $(this).parent('td');
            var selectedDateStr = targetTd.attr('data-date');
            var selectedSchedules = schedulesForDate(schedules, selectedDateStr);

            if (targetTd.hasClass('disabled')) {
                return;
            }

            $('.month_area .month tbody td').removeClass('click');
            targetTd.addClass('click');

            if (!targetTd.hasClass('event')) {
                return;
            }

            openCalendarPopup(selectedDateStr, selectedSchedules);
        });
    }

    function openCalendarPopup(selectedDateStr, schedules) {
        var clickDate = parseDate(selectedDateStr);
        var dayNames = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];
        var popup = $('.calendar_event_popup');
        var listHtml = '';

        if (!clickDate || popup.length === 0) {
            return;
        }

        schedules.forEach(function (schedule) {
            listHtml += '<li><strong>' + escapeHtml(schedule.title) + '</strong><div class="timebox"><span>' + escapeHtml(formatPeriod(schedule.start, schedule.end)) + '</span>';
            if (schedule.content) {
                listHtml += '<span>' + escapeHtml(schedule.content) + '</span>';
            }
            listHtml += '</div></li>';
        });

        popup.removeAttr('hidden');
        popup.find('.event_tit').text(clickDate.getFullYear() + '년 ' + (clickDate.getMonth() + 1) + '월 ' + clickDate.getDate() + '일(' + dayNames[clickDate.getDay()] + ')');
        popup.find('.schedule_list').html(listHtml || '<li><strong>등록된 일정이 없습니다.</strong></li>');
        popup.stop().fadeIn(200);
    }

    function initMemberProgress() {
        $('.member_info').each(function () {
            var flexBetween = $(this).find('.flex_between dd');
            var matches = flexBetween.text().match(/\d+/g);

            if (matches && matches.length >= 2) {
                var current = parseInt(matches[0], 10);
                var total = parseInt(matches[1], 10);
                var percentage = total > 0 ? Math.min(100, (current / total) * 100) : 0;
                $(this).find('.state_line .bar').css('width', percentage + '%');
            }
        });
    }

    function initPopups() {
        var mainCommitteePopupCookie = 'main_committee_join_popup_hide';

        function getCookie(name) {
            var value = '; ' + document.cookie;
            var parts = value.split('; ' + name + '=');
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
            return '';
        }

        function setTodayCookie(name) {
            var expires = new Date();
            expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000));
            document.cookie = name + '=1; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
        }

        $('[data-main-normal-popup]').each(function () {
            var popup = $(this);
            var features = [
                'width=' + (popup.data('popup-width') || 500),
                'height=' + (popup.data('popup-height') || 500),
                'left=' + (popup.data('popup-left') || 100),
                'top=' + (popup.data('popup-top') || 100),
                'scrollbars=yes',
                'resizable=yes',
                'menubar=no',
                'toolbar=no',
                'location=no',
                'status=no'
            ].join(',');

            window.open(popup.data('popup-url'), 'popup_' + popup.data('popup-id'), features);
        });

        $('[data-main-layer-popup]').each(function () {
            var popup = $(this);
            popup.css({
                position: 'absolute',
                width: (popup.data('popup-width') || 500) + 'px',
                height: 'auto',
                top: (popup.data('popup-top') || 100) + 'px',
                left: (popup.data('popup-left') || 100) + 'px',
                zIndex: 99999
            });
            popup.removeAttr('hidden');
        });

        $('[data-main-auto-open]').each(function () {
            if (getCookie(mainCommitteePopupCookie) === '1') {
                return;
            }
            $(this).removeAttr('hidden').fadeIn(200);
        });

        $(document).on('click', '[data-main-popup-hide-today]', function () {
            setTodayCookie(mainCommitteePopupCookie);
        });

        $(document).on('click', '.calendar_event_popup .btn_close_btm, .calendar_event_popup .dm, .popup_login_start .btn_close_btm, .popup_login_start .dm', function () {
            $(this).closest('.popup').fadeOut(300);
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape' || event.keyCode === 27) {
                $('.calendar_event_popup:visible, .popup_login_start:visible').fadeOut(300);
            }
        });
    }

    $(document).ready(function () {
        initMainVisual();
        initBookSlide();
        initScheduleSlide();
        initCalendar();
        initMemberProgress();
        initPopups();
    });
})(jQuery);

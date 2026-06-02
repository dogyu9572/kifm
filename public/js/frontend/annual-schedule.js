(function () {
    'use strict';

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function formatDate(year, month, day) {
        return year + '-' + pad(month) + '-' + pad(day);
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function parseDate(dateString) {
        var parts = String(dateString || '').split('-').map(function (part) {
            return parseInt(part, 10);
        });

        if (parts.length !== 3 || parts.some(function (part) { return Number.isNaN(part); })) {
            return null;
        }

        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function normalizeEvents(rawEvents) {
        if (!Array.isArray(rawEvents)) {
            return [];
        }

        return rawEvents
            .map(function (event) {
                var start = String(event.start || '');
                var end = String(event.end || start);
                var title = String(event.title || '').trim();
                if (!start || !title || parseDate(start) === null || parseDate(end) === null) {
                    return null;
                }

            return {
                start: start,
                end: end || start,
                className: event.class === 'c2' ? 'c2' : 'c1',
                type: event.type === 'training_course' ? 'training_course' : 'academic_conference',
                title: title,
                url: String(event.url || '').trim()
            };
        })
        .filter(Boolean);
    }

    function formatMobileDate(startStr, endStr) {
        var weekDays = ['일', '월', '화', '수', '목', '금', '토'];
        var startDate = parseDate(startStr);
        var startPart = String(startStr).split('-');
        var startLabel = startPart[1] + '.' + startPart[2] + '(' + weekDays[startDate.getDay()] + ')';

        if (startStr === endStr) {
            return startLabel;
        }

        var endDate = parseDate(endStr);
        var endPart = String(endStr).split('-');
        return startLabel + ' ~ ' + endPart[1] + '.' + endPart[2] + '(' + weekDays[endDate.getDay()] + ')';
    }

    function filterByType(events, selectedType) {
        if (selectedType === 'academic_conference' || selectedType === 'training_course') {
            return events.filter(function (event) {
                return event.type === selectedType;
            });
        }

        return events;
    }

    function eventsForMonth(events, year, month, selectedType) {
        var monthStart = formatDate(year, month, 1);
        var monthEnd = formatDate(year, month, new Date(year, month, 0).getDate());

        return filterByType(events, selectedType)
            .filter(function (event) {
                return event.start <= monthEnd && event.end >= monthStart;
            })
            .sort(function (a, b) {
                if (a.start === b.start) {
                    return a.end.localeCompare(b.end);
                }

                return a.start.localeCompare(b.start);
            });
    }

    function renderMobileList(list, events) {
        if (!list) {
            return;
        }

        if (events.length === 0) {
            list.innerHTML = '<li class="no_data">등록된 일정이 없습니다.</li>';
            return;
        }

        list.innerHTML = events.map(function (event) {
            var title = escapeHtml(event.title);
            var titleHtml = event.url
                ? '<a href="' + escapeHtml(event.url) + '">' + title + '</a>'
                : title;

            return '<li class="' + event.className + '">' +
                '<span>' + escapeHtml(formatMobileDate(event.start, event.end)) + '</span>' +
                '<h3>' + titleHtml + '</h3>' +
                '</li>';
        }).join('');
    }

    function renderCalendar(context, year, month) {
        var monthStart = formatDate(year, month, 1);
        var monthEnd = formatDate(year, month, new Date(year, month, 0).getDate());
        var selectedType = context.typeRadios.find(function (radio) {
            return radio.checked;
        })?.value || 'all';
        var events = eventsForMonth(context.events, year, month, selectedType);

        context.yearText.textContent = String(year);
        context.monthButtons.forEach(function (button, index) {
            button.classList.toggle('on', index + 1 === month);
        });

        renderMobileList(context.mobileList, events);

        var firstDayOfMonth = new Date(year, month - 1, 1).getDay();
        var lastDateOfMonth = new Date(year, month, 0).getDate();
        var lastDateOfPrevMonth = new Date(year, month - 1, 0).getDate();
        var calendarDays = [];

        for (var prev = firstDayOfMonth - 1; prev >= 0; prev--) {
            calendarDays.push({ day: lastDateOfPrevMonth - prev, isCurrentMonth: false, dateStr: '' });
        }
        for (var day = 1; day <= lastDateOfMonth; day++) {
            calendarDays.push({ day: day, isCurrentMonth: true, dateStr: formatDate(year, month, day) });
        }

        var nextMonthDay = 1;
        while (calendarDays.length % 7 !== 0) {
            calendarDays.push({ day: nextMonthDay, isCurrentMonth: false, dateStr: '' });
            nextMonthDay++;
        }

        var totalWeeks = calendarDays.length / 7;
        var weekGrid = Array.from({ length: totalWeeks }, function () { return []; });

        events.forEach(function (event) {
            var eventStart = event.start < monthStart ? monthStart : event.start;
            var eventEnd = event.end > monthEnd ? monthEnd : event.end;
            var startIdx = calendarDays.findIndex(function (dayInfo) {
                return dayInfo.isCurrentMonth && dayInfo.dateStr === eventStart;
            });
            var endIdx = calendarDays.findIndex(function (dayInfo) {
                return dayInfo.isCurrentMonth && dayInfo.dateStr === eventEnd;
            });

            if (startIdx === -1 || endIdx === -1) {
                return;
            }

            var eventWeeks = [];
            for (var i = startIdx; i <= endIdx; i++) {
                var week = Math.floor(i / 7);
                if (!eventWeeks.includes(week)) {
                    eventWeeks.push(week);
                }
            }

            var targetRow = -1;
            var rowIdx = 0;
            while (targetRow === -1) {
                var canPlace = true;
                for (var weekIndex = 0; weekIndex < eventWeeks.length; weekIndex++) {
                    var weekNo = eventWeeks[weekIndex];
                    if (!weekGrid[weekNo][rowIdx]) {
                        weekGrid[weekNo][rowIdx] = Array(7).fill(null);
                    }

                    var startDayOfWeek = Math.max(startIdx, weekNo * 7) % 7;
                    var endDayOfWeek = Math.min(endIdx, (weekNo * 7) + 6) % 7;
                    for (var gridDay = startDayOfWeek; gridDay <= endDayOfWeek; gridDay++) {
                        if (weekGrid[weekNo][rowIdx][gridDay] !== null) {
                            canPlace = false;
                            break;
                        }
                    }
                    if (!canPlace) {
                        break;
                    }
                }

                if (canPlace) {
                    targetRow = rowIdx;
                } else {
                    rowIdx++;
                }
            }

            eventWeeks.forEach(function (weekNo) {
                var startGlobal = Math.max(startIdx, weekNo * 7);
                var endGlobal = Math.min(endIdx, (weekNo * 7) + 6);
                var startDayOfWeek = startGlobal % 7;
                var endDayOfWeek = endGlobal % 7;
                var width = endDayOfWeek - startDayOfWeek + 1;

                weekGrid[weekNo][targetRow][startDayOfWeek] = {
                    type: 'event',
                    className: event.className,
                    title: event.title,
                    url: event.url,
                    widthClass: 'width' + width
                };

                for (var blankDay = startDayOfWeek + 1; blankDay <= endDayOfWeek; blankDay++) {
                    weekGrid[weekNo][targetRow][blankDay] = { type: 'blank' };
                }
            });
        });

        var html = '';
        for (var weekRow = 0; weekRow < totalWeeks; weekRow++) {
            html += '<tr>';
            for (var col = 0; col < 7; col++) {
                var globalIdx = (weekRow * 7) + col;
                var dayInfo = calendarDays[globalIdx];
                if (!dayInfo.isCurrentMonth) {
                    html += '<td class="other"><span>' + dayInfo.day + '</span></td>';
                    continue;
                }

                var listHtml = '';
                var maxRows = weekGrid[weekRow].length;
                var hasContent = false;
                for (var row = 0; row < maxRows; row++) {
                    if (weekGrid[weekRow][row] && weekGrid[weekRow][row][col] !== null) {
                        hasContent = true;
                        break;
                    }
                }

                if (hasContent) {
                    listHtml += '<ul class="list">';
                    for (var eventRow = 0; eventRow < maxRows; eventRow++) {
                        var cell = weekGrid[weekRow][eventRow] ? weekGrid[weekRow][eventRow][col] : null;
                        if (cell === null || cell.type === 'blank') {
                            listHtml += '<li class="blank"></li>';
                        } else {
                            var title = escapeHtml(cell.title);
                            var content = '<span title="' + title + '">' + title + '</span>';
                            if (cell.url) {
                                content = '<a href="' + escapeHtml(cell.url) + '">' + content + '</a>';
                            }
                            listHtml += '<li class="' + cell.className + ' ' + cell.widthClass + '">' +
                                content +
                                '</li>';
                        }
                    }
                    listHtml += '</ul>';
                }

                html += '<td><span>' + dayInfo.day + '</span>' + listHtml + '</td>';
            }
            html += '</tr>';
        }

        context.calendarBody.innerHTML = html;
    }

    function initAnnualSchedule(root) {
        var yearText = root.querySelector('.years strong');
        var prevBtn = root.querySelector('.btn.prev');
        var nextBtn = root.querySelector('.btn.next');
        var monthButtons = Array.from(root.querySelectorAll('.month button'));
        var typeRadios = Array.from(root.querySelectorAll('input[name="annual_schedule_type"]'));
        var calendarBody = root.querySelector('.schedule_month_tbl tbody');
        var mobileList = root.querySelector('#mo-schedule-list');

        if (!yearText || !prevBtn || !nextBtn || monthButtons.length === 0 || !calendarBody) {
            return;
        }

        var rawEvents = [];
        try {
            rawEvents = JSON.parse(root.dataset.annualSchedules || '[]');
        } catch (error) {
            rawEvents = [];
        }

        var context = {
            yearText: yearText,
            monthButtons: monthButtons,
            typeRadios: typeRadios,
            calendarBody: calendarBody,
            mobileList: mobileList,
            events: normalizeEvents(rawEvents)
        };
        var currentYear = parseInt(root.dataset.initialYear || yearText.textContent, 10) || new Date().getFullYear();
        var currentMonth = parseInt(root.dataset.initialMonth || '1', 10) || 1;

        prevBtn.addEventListener('click', function () {
            currentYear--;
            renderCalendar(context, currentYear, currentMonth);
        });

        nextBtn.addEventListener('click', function () {
            currentYear++;
            renderCalendar(context, currentYear, currentMonth);
        });

        monthButtons.forEach(function (button, index) {
            button.addEventListener('click', function () {
                currentMonth = index + 1;
                renderCalendar(context, currentYear, currentMonth);
            });
        });

        typeRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                renderCalendar(context, currentYear, currentMonth);
            });
        });

        renderCalendar(context, currentYear, currentMonth);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-annual-schedule]').forEach(initAnnualSchedule);
    });
})();

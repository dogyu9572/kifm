@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon annual_schedule_wrap" aria-labelledby="conference-heading">
	<div class="inner">
		<h1 class="sub_title" id="conference-heading">{{ $sName }}</h1>

		<div class="schedule_wrap">
			<div class="schedule_top">
				<div class="years">
					<strong>2026</strong>
					<button type="button" class="btn prev">이전</button>
					<button type="button" class="btn next">다음</button>
				</div>
				<div class="month">
					<button type="button">1월</button>
					<button type="button">2월</button>
					<button type="button">3월</button>
					<button type="button">4월</button>
					<button type="button">5월</button>
					<button type="button">6월</button>
					<button type="button">7월</button>
					<button type="button">8월</button>
					<button type="button">9월</button>
					<button type="button">10월</button>
					<button type="button">11월</button>
					<button type="button">12월</button>
				</div>
				<div class="tag">
					<span class="c1">학술대회</span>
					<span class="c2">연수강좌</span>
				</div>
			</div>
		</div>
		
		<div class="schedule_month_tbl">
			<table>
				<thead>
					<tr>
						<th>일</th>
						<th>월</th>
						<th>화</th>
						<th>수</th>
						<th>목</th>
						<th>금</th>
						<th>토</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>

	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduleData = {
        "2026": {
            "04": [
                { start: "2026-04-06", end: "2026-04-06", class: "c1", title: "심화 연수강좌 2차" },
                { start: "2026-04-08", end: "2026-04-08", class: "c1", title: "심화 연수강좌 2차" },
                { start: "2026-04-13", end: "2026-04-13", class: "c1", title: "심화 연수강좌 2차" },
                { start: "2026-04-15", end: "2026-04-16", class: "c2", title: "심화 연수강좌 2차" },
                { start: "2026-04-16", end: "2026-04-21", class: "c1", title: "주를 넘어가는 연속 일정" },
                { start: "2026-04-20", end: "2026-04-20", class: "c1", title: "심화 연수강좌 2차" },
                { start: "2026-04-22", end: "2026-04-22", class: "c1", title: "심화 연수강좌 2차" },
                { start: "2026-04-27", end: "2026-04-27", class: "c1", title: "심화 연수강좌 2차" },
                { start: "2026-04-29", end: "2026-04-30", class: "c2", title: "심화 연수강좌 2차" },
                { start: "2026-04-29", end: "2026-04-30", class: "c2", title: "심화 연수강좌 2차3" },
            ],
            "05": [
                { start: "2026-05-01", end: "2026-05-01", class: "c1", title: "학술대회 샘플" },
                { start: "2026-05-15", end: "2026-05-15", class: "c2", title: "연수강좌 샘플" }
            ]
        }
    };

    const yearMonthTxt = document.querySelector('.years strong');
    const prevBtn = document.querySelector('.btn.prev');
    const nextBtn = document.querySelector('.btn.next');
    const monthButtons = document.querySelectorAll('.month button');
    const calendarBody = document.querySelector('.schedule_month_tbl tbody');

    let currentYear = parseInt(yearMonthTxt.textContent.split('.')[0], 10);
    let currentMonth = 4;

    function formatDate(y, m, d) {
        return `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    }

    function renderCalendar(year, month) {
        const formattedMonth = String(month).padStart(2, '0');
        yearMonthTxt.textContent = `${year}`;

        monthButtons.forEach((btn, index) => {
            if (index + 1 === month) {
                btn.classList.add('on');
            } else {
                btn.classList.remove('on');
            }
        });

        const firstDayOfMonth = new Date(year, month - 1, 1).getDay();
        const lastDateOfMonth = new Date(year, month, 0).getDate();
        const lastDateOfPrevMonth = new Date(year, month - 1, 0).getDate();

        const calendarDays = [];

        for (let i = firstDayOfMonth - 1; i >= 0; i--) {
            calendarDays.push({ day: lastDateOfPrevMonth - i, isCurrentMonth: false, dateStr: "" });
        }

        for (let i = 1; i <= lastDateOfMonth; i++) {
            calendarDays.push({ day: i, isCurrentMonth: true, dateStr: formatDate(year, month, i) });
        }

        let nextMonthDay = 1;
        while (calendarDays.length % 7 !== 0) {
            calendarDays.push({ day: nextMonthDay++, isCurrentMonth: false, dateStr: "" });
        }

        const events = (scheduleData[year] && scheduleData[year][formattedMonth]) ? scheduleData[year][formattedMonth] : [];
        
        const totalWeeks = calendarDays.length / 7;
        const weekGrid = Array.from({ length: totalWeeks }, () => []);

        events.forEach(event => {
            let startIdx = calendarDays.findIndex(d => d.isCurrentMonth && d.dateStr === event.start);
            let endIdx = calendarDays.findIndex(d => d.isCurrentMonth && d.dateStr === event.end);

            if (startIdx === -1 && endIdx === -1) return;
            if (startIdx === -1) startIdx = calendarDays.findIndex(d => d.isCurrentMonth);
            if (endIdx === -1) endIdx = calendarDays.length - 1;

            let eventWeeks = [];
            for (let i = startIdx; i <= endIdx; i++) {
                let w = Math.floor(i / 7);
                if (!eventWeeks.includes(w)) eventWeeks.push(w);
            }

            let targetRow = -1;
            let rowIdx = 0;

            while (targetRow === -1) {
                let canPlace = true;
                for (let w of eventWeeks) {
                    if (!weekGrid[w][rowIdx]) {
                        weekGrid[w][rowIdx] = Array(7).fill(null);
                    }
                    let startDayOfW = Math.max(startIdx, w * 7) % 7;
                    let endDayOfW = Math.min(endIdx, (w * 7) + 6) % 7;
                    
                    for (let d = startDayOfW; d <= endDayOfW; d++) {
                        if (weekGrid[w][rowIdx][d] !== null) {
                            canPlace = false;
                            break;
                        }
                    }
                    if (!canPlace) break;
                }

                if (canPlace) {
                    targetRow = rowIdx;
                } else {
                    rowIdx++;
                }
            }

            for (let w of eventWeeks) {
                let startGlobal = Math.max(startIdx, w * 7);
                let endGlobal = Math.min(endIdx, (w * 7) + 6);
                let startDayOfW = startGlobal % 7;
                let endDayOfW = endGlobal % 7;
                let width = endDayOfW - startDayOfW + 1;

                let actualWidthClass = event.widthClass;
                if (!actualWidthClass) {
                    actualWidthClass = `width${width}`;
                }

                weekGrid[w][targetRow][startDayOfW] = {
                    type: 'event',
                    class: event.class,
                    title: event.title,
                    widthClass: actualWidthClass
                };

                for (let d = startDayOfW + 1; d <= endDayOfW; d++) {
                    weekGrid[w][targetRow][d] = { type: 'blank' };
                }
            }
        });

        let html = '';

        for (let w = 0; w < totalWeeks; w++) {
            html += '<tr>';
            for (let d = 0; d < 7; d++) {
                const globalIdx = (w * 7) + d;
                const dayInfo = calendarDays[globalIdx];

                if (!dayInfo.isCurrentMonth) {
                    html += `<td class="other"><span>${dayInfo.day}</span></td>`;
                } else {
                    let listHtml = '';
                    let hasContent = false;
                    const maxRows = weekGrid[w].length;

                    for (let r = 0; r < maxRows; r++) {
                        if (weekGrid[w][r] && weekGrid[w][r][d] !== null) {
                            hasContent = true;
                            break;
                        }
                    }

                    if (hasContent) {
                        listHtml += '<ul class="list">';
                        for (let r = 0; r < maxRows; r++) {
                            const cell = (weekGrid[w][r]) ? weekGrid[w][r][d] : null;
                            if (cell === null) {
                                listHtml += '<li class="blank"></li>';
                            } else if (cell.type === 'event') {
                                const wClass = cell.widthClass ? ` ${cell.widthClass}` : '';
                                listHtml += `<li class="${cell.class}${wClass}"><span>${cell.title}</span></li>`;
                            } else if (cell.type === 'blank') {
                                listHtml += '<li class="blank"></li>';
                            }
                        }
                        listHtml += '</ul>';
                    }

                    html += `<td><span>${dayInfo.day}</span>${listHtml}</td>`;
                }
            }
            html += '</tr>';
        }

        calendarBody.innerHTML = html;
    }

    prevBtn.addEventListener('click', function() {
        currentYear--;
        renderCalendar(currentYear, currentMonth);
    });

    nextBtn.addEventListener('click', function() {
        currentYear++;
        renderCalendar(currentYear, currentMonth);
    });

    monthButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            currentMonth = index + 1;
            renderCalendar(currentYear, currentMonth);
        });
    });

    renderCalendar(currentYear, currentMonth);
});
</script>
@endpush
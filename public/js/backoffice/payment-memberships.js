document.addEventListener('DOMContentLoaded', function () {
    const perPageSelect = document.getElementById('perPageSelect');
    const filterForm = document.querySelector('.bo-payment-membership-filter .filter-form');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');

    if (filterForm && dateFrom && dateTo) {
        filterForm.addEventListener('submit', function (event) {
            const from = (dateFrom.value || '').trim();
            const to = (dateTo.value || '').trim();

            if (from === '' || to === '' || from <= to) {
                return;
            }

            event.preventDefault();
            window.alert('시작일이 종료일보다 늦습니다');
            dateFrom.focus();
        });
    }

    if (perPageSelect && perPageSelect.form) {
        perPageSelect.addEventListener('change', function () {
            perPageSelect.form.submit();
        });
    }

    const toYmd = function (date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    };

    document.querySelectorAll('.bo-date-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const preset = btn.getAttribute('data-preset');
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');
            if (!dateFrom || !dateTo) {
                return;
            }

            const today = new Date();
            if (preset === 'all') {
                dateFrom.value = '';
                dateTo.value = '';
                return;
            }
            if (preset === 'today') {
                const ymd = toYmd(today);
                dateFrom.value = ymd;
                dateTo.value = ymd;
                return;
            }
            if (preset === 'yesterday') {
                const d = new Date(today);
                d.setDate(d.getDate() - 1);
                const ymd = toYmd(d);
                dateFrom.value = ymd;
                dateTo.value = ymd;
                return;
            }
            if (preset === 'week') {
                const d = new Date(today);
                d.setDate(d.getDate() - 6);
                dateFrom.value = toYmd(d);
                dateTo.value = toYmd(today);
                return;
            }
            if (preset === 'month') {
                const d = new Date(today.getFullYear(), today.getMonth(), 1);
                dateFrom.value = toYmd(d);
                dateTo.value = toYmd(today);
                return;
            }
            if (preset === 'year') {
                const d = new Date(today.getFullYear(), 0, 1);
                dateFrom.value = toYmd(d);
                dateTo.value = toYmd(today);
            }
        });
    });

    document.querySelectorAll('.js-confirm-delete').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('해당 납부 내역을 삭제하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
});

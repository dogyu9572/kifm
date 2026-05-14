/**
 * 목록 테이블: #select-all 과 동일 테이블 tbody 내 .bo-row-checkbox 연동
 */
(function () {
    function initBoardTableSelectAll() {
        const selectAll = document.getElementById('select-all');
        if (!selectAll) {
            return;
        }
        const table = selectAll.closest('table');
        if (!table) {
            return;
        }
        const rowChecks = table.querySelectorAll('tbody .bo-row-checkbox');
        if (rowChecks.length === 0) {
            return;
        }

        const syncHeader = () => {
            const checked = Array.from(rowChecks).filter((c) => c.checked).length;
            selectAll.checked = checked > 0 && checked === rowChecks.length;
            selectAll.indeterminate = checked > 0 && checked < rowChecks.length;
        };

        selectAll.addEventListener('change', () => {
            rowChecks.forEach((cb) => {
                cb.checked = selectAll.checked;
            });
            selectAll.indeterminate = false;
        });

        rowChecks.forEach((cb) => {
            cb.addEventListener('change', syncHeader);
        });
        syncHeader();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBoardTableSelectAll);
    } else {
        initBoardTableSelectAll();
    }
})();

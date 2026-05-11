/**
 * 쿠폰 사용 이력: 페이지 크기 변경, 날짜 범위 간단 검증
 */
document.addEventListener('DOMContentLoaded', () => {
    const perPageSelect = document.querySelector('.bo-coupon-history-per-page');
    const perPageForm = document.getElementById('coupon-history-per-page-form');
    const usedFrom = document.getElementById('filter_used_from');
    const usedTo = document.getElementById('filter_used_to');

    const validateDateRange = () => {
        if (!usedFrom || !usedTo || !usedFrom.value || !usedTo.value) {
            return;
        }
        if (usedFrom.value > usedTo.value) {
            window.alert('종료일은 시작일 이후여야 합니다.');
            usedTo.value = '';
        }
    };

    if (usedFrom) {
        usedFrom.addEventListener('change', validateDateRange);
    }
    if (usedTo) {
        usedTo.addEventListener('change', validateDateRange);
    }

    if (perPageSelect && perPageForm) {
        perPageSelect.addEventListener('change', () => {
            perPageForm.submit();
        });
    }
});

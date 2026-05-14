/**
 * 진료 과목 목록: 표시 개수 변경, 삭제 확인
 */
document.addEventListener('DOMContentLoaded', () => {
    const perPageForm = document.getElementById('bo-doctor-categories-per-page-form');
    const perPageSelect = document.getElementById('per_page');
    if (perPageForm && perPageSelect) {
        perPageSelect.addEventListener('change', () => {
            perPageForm.submit();
        });
    }

    document.querySelectorAll('#bo-doctor-categories-index .js-delete-confirm-form').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            const ok = window.confirm('이 진료 과목을 삭제하시겠습니까?');
            if (!ok) {
                event.preventDefault();
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const perPageSelect = document.querySelector('.js-committee-per-page');
    const perPageForm = document.getElementById('committee-per-page-form');
    const deleteForms = document.querySelectorAll('.js-delete-committee-form');

    perPageSelect?.addEventListener('change', () => {
        perPageForm?.submit();
    });

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('정말 이 위원회를 삭제하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
});


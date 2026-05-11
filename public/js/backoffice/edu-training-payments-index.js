(function () {
    const cancelForms = document.querySelectorAll('.js-cancel-form');

    cancelForms.forEach((form) => {
        form.addEventListener('submit', function (event) {
            if (!confirm('해당 건을 취소(환불) 완료 처리하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
})();


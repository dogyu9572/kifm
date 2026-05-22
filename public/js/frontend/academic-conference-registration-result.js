(function () {
    document.querySelectorAll('[data-print-disabled]').forEach((button) => {
        button.addEventListener('click', function () {
            window.alert('결제 완료 후 출력할 수 있습니다.');
        });
    });

    document.querySelectorAll('.btns_btm form.btn').forEach((form) => {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('등록 취소 요청을 접수하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
})();

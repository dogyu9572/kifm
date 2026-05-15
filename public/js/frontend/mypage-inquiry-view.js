/**
 * 마이페이지 1:1 문의 상세: 삭제 확인
 */
(function () {
    'use strict';

    document.querySelectorAll('[data-mypage-inquiry-delete]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('문의를 삭제하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
})();

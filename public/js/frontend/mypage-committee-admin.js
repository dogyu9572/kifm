/**
 * 마이페이지 위원회 관리자 화면
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-reject-reason]').forEach(function (button) {
            button.addEventListener('click', function () {
                window.alert(button.getAttribute('data-reject-reason') || '반려 사유가 없습니다.');
            });
        });
    });
})();

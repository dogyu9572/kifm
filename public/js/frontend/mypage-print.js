/**
 * 마이페이지 인쇄 화면 자동 출력
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('[data-mypage-print]');
        if (!root) {
            return;
        }
        window.print();
        window.addEventListener('afterprint', function () {
            window.close();
        });
    });
})();

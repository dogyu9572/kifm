(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var backButton = document.querySelector('.js-mypage-secession-back');
        if (backButton) {
            backButton.addEventListener('click', function () {
                window.history.back();
            });
        }

        var form = document.querySelector('.js-mypage-secession-form');
        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            if (!window.confirm('정말로 회원탈퇴를 진행하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
})();

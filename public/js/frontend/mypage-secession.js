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
            var agreed = document.querySelector('[name="secession_agreed"]');
            var password = document.querySelector('[name="password"]');
            var reason = document.querySelector('[name="withdrawal_reason"]');

            if (!agreed.checked) {
                event.preventDefault();
                alert('약관에 동의해주세요.');
                return;
            }
            if (!password.value.trim()) {
                event.preventDefault();
                alert('비밀번호를 입력해주세요.');
                password.focus();
                return;
            }
            if (!reason.value.trim()) {
                event.preventDefault();
                alert('탈퇴 사유를 입력해주세요.');
                reason.focus();
                return;
            }

            if (!window.confirm('정말로 회원탈퇴를 진행하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
})();
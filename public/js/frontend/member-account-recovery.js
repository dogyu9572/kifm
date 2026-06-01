/**
 * 회원 아이디/비밀번호 찾기: 휴대폰 번호 표시용 하이픈
 */
(function () {
    'use strict';

    function formatPhoneKoreaDisplay(raw) {
        var d = String(raw || '')
            .replace(/\D/g, '')
            .slice(0, 11);
        if (!d.length) {
            return '';
        }
        if (d.length <= 3) {
            return d;
        }
        if (d.length === 10 && d[2] !== '0') {
            return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
        }
        if (d.startsWith('010') || d.length === 11 || /^01[6789]/.test(d)) {
            if (d.length <= 7) {
                return d.slice(0, 3) + '-' + d.slice(3);
            }
            return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
        }
        if (d.startsWith('011')) {
            if (d.length <= 6) {
                return d.slice(0, 3) + '-' + d.slice(3);
            }
            if (d.length === 10) {
                return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
            }
        }
        if (d.length <= 7) {
            return d.slice(0, 3) + '-' + d.slice(3);
        }
        return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
    }

    function bindPhoneInput(input) {
        var apply = function () {
            var formatted = formatPhoneKoreaDisplay(input.value);
            input.value = formatted;
            var end = formatted.length;
            input.setSelectionRange(end, end);
        };
        if (input.value) {
            input.value = formatPhoneKoreaDisplay(input.value);
        }
        input.addEventListener('input', apply);
        input.addEventListener('blur', function () {
            input.value = formatPhoneKoreaDisplay(input.value);
        });
        input.addEventListener('paste', function () {
            window.requestAnimationFrame(apply);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-account-recovery-phone-input[name="phone_number"]').forEach(bindPhoneInput);
    });
})();

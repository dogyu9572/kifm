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

    function fieldValue(form, name) {
        var input = form.querySelector('[name="' + name + '"]');
        return input ? String(input.value || '').trim() : '';
    }

    function focusField(form, name) {
        var input = form.querySelector('[name="' + name + '"]');
        if (input) {
            input.focus();
        }
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function isValidPhone(value) {
        return /^01[016789]\d{7,8}$/.test(String(value || '').replace(/\D/g, ''));
    }

    function alertAndFocus(form, name, message) {
        window.alert(message);
        focusField(form, name);
    }

    function validateFindIdForm(form) {
        if (!fieldValue(form, 'name')) {
            alertAndFocus(form, 'name', '이름을 입력해주세요.');
            return false;
        }
        if (!fieldValue(form, 'phone_number')) {
            alertAndFocus(form, 'phone_number', '휴대폰 번호를 입력해주세요.');
            return false;
        }
        if (!isValidPhone(fieldValue(form, 'phone_number'))) {
            alertAndFocus(form, 'phone_number', '휴대폰 번호 형식을 확인해주세요. (010 등 10~11자리)');
            return false;
        }
        if (!fieldValue(form, 'email')) {
            alertAndFocus(form, 'email', '이메일을 입력해주세요.');
            return false;
        }
        if (!isValidEmail(fieldValue(form, 'email'))) {
            alertAndFocus(form, 'email', '올바른 이메일 형식이 아닙니다.');
            return false;
        }

        return true;
    }

    function validateFindPasswordForm(form) {
        if (!fieldValue(form, 'login_id')) {
            alertAndFocus(form, 'login_id', '아이디를 입력해주세요.');
            return false;
        }
        if (!fieldValue(form, 'email')) {
            alertAndFocus(form, 'email', '이메일 주소를 입력해주세요.');
            return false;
        }
        if (!isValidEmail(fieldValue(form, 'email'))) {
            alertAndFocus(form, 'email', '올바른 이메일 형식이 아닙니다.');
            return false;
        }
        if (!fieldValue(form, 'phone_number')) {
            alertAndFocus(form, 'phone_number', '휴대폰 번호를 입력해주세요.');
            return false;
        }
        if (!isValidPhone(fieldValue(form, 'phone_number'))) {
            alertAndFocus(form, 'phone_number', '휴대폰 번호 형식을 확인해주세요. (010 등 10~11자리)');
            return false;
        }

        return true;
    }

    function bindSubmitValidation(form) {
        form.addEventListener('submit', function (event) {
            var isFindId = Boolean(form.querySelector('[name="name"]'));
            var isValid = isFindId ? validateFindIdForm(form) : validateFindPasswordForm(form);
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-account-recovery-phone-input[name="phone_number"]').forEach(bindPhoneInput);
        document.querySelectorAll('form.member_inbox').forEach(bindSubmitValidation);
    });
})();

/**
 * 회원가입: 위원회 최대 3개, 중복 확인(fetch), 뒤로가기
 */
(function () {
    'use strict';

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function postForm(url, bodyObj) {
        var body = new URLSearchParams();
        body.append('_token', getCsrfToken());
        Object.keys(bodyObj).forEach(function (key) {
            if (bodyObj[key] !== undefined && bodyObj[key] !== null) {
                body.append(key, bodyObj[key]);
            }
        });
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body.toString(),
            credentials: 'same-origin',
        }).then(function (r) {
            return r.json().then(function (data) {
                if (!r.ok) {
                    var msg = data.message;
                    if (!msg && data.errors) {
                        msg = Object.values(data.errors).flat().join('\n');
                    }
                    throw new Error(msg || '요청에 실패했습니다.');
                }
                return data;
            });
        });
    }

    function bindCommitteeLimit() {
        var checks = document.querySelectorAll('input[name="committee_codes[]"]');
        var max = 3;
        checks.forEach(function (cb) {
            cb.addEventListener('change', function () {
                var n = document.querySelectorAll('input[name="committee_codes[]"]:checked').length;
                if (n > max) {
                    cb.checked = false;
                    window.alert('위원회 참가 신청은 최대 ' + max + '개까지만 선택 가능합니다.');
                }
            });
        });
    }

    function bindDuplicateButton(selector, url, fieldName, getValue) {
        var btn = document.querySelector(selector);
        if (!btn || !url) {
            return;
        }
        btn.addEventListener('click', function () {
            var val = getValue();
            if (!val) {
                window.alert('값을 입력한 뒤 중복 확인을 눌러주세요.');
                return;
            }
            var payload = {};
            payload[fieldName] = val;
            postForm(url, payload)
                .then(function (data) {
                    window.alert(data.message || '');
                })
                .catch(function (e) {
                    window.alert(e.message || '중복 확인 중 오류가 발생했습니다.');
                });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('member-register-page');
        if (!root) {
            return;
        }

        bindCommitteeLimit();

        bindDuplicateButton('.js-register-check-login-id', root.dataset.checkLoginId, 'login_id', function () {
            var el = document.querySelector('input[name="login_id"]');
            return el ? el.value.trim() : '';
        });

        bindDuplicateButton('.js-register-check-email', root.dataset.checkEmail, 'email', function () {
            var el = document.querySelector('input[name="email"]');
            return el ? el.value.trim() : '';
        });

        bindDuplicateButton('.js-register-check-phone', root.dataset.checkPhone, 'phone_number', function () {
            var el = document.querySelector('input[name="phone_number"]');
            return el ? el.value.trim() : '';
        });

        bindDuplicateButton('.js-register-check-license', root.dataset.checkLicense, 'license_number', function () {
            var el = document.querySelector('input[name="license_number"]');
            return el ? el.value.trim() : '';
        });

        document.querySelectorAll('.js-register-back').forEach(function (btn) {
            btn.addEventListener('click', function () {
                window.history.back();
            });
        });
    });
})();
